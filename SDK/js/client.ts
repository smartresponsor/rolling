import type {AccessCheckRequest, AccessDecision, BatchRequest, BatchResult} from './types';

// Node HMAC (preferred in Node)
let nodeHmac: ((data: string, secret: string) => string) | null = null;
try {
    // eslint-disable-next-line @typescript-eslint/no-var-requires
    const crypto = require('crypto') as typeof import('crypto');
    nodeHmac = (data: string, secret: string) =>
        'v1=' + crypto.createHmac('sha256', secret).update(data).digest('base64');
} catch { /* browser or no node crypto */
}

// WebCrypto fallback (browser)
async function webHmac(data: string, secret: string): Promise<string> {
    const enc = new TextEncoder();
    const key = await crypto.subtle.importKey('raw', enc.encode(secret), {
        name: 'HMAC',
        hash: 'SHA-256'
    }, false, ['sign']);
    const sig = await crypto.subtle.sign('HMAC', key, enc.encode(data));
    const b64 = btoa(String.fromCharCode(...new Uint8Array(sig)));
    return 'v1=' + b64;
}

export interface ClientOptions {
    apiKey?: string;
    hmacSecret?: string;
    fetchImpl?: typeof fetch;
    clock?: () => Date; // for tests
}

export class Client {
    private baseUrl: string;
    private readonly apiKey?: string;
    private readonly hmacSecret?: string;
    private fetchImpl: typeof fetch;
    private clock: () => Date;

    constructor(baseUrl: string, opts: ClientOptions = {}) {
        this.baseUrl = baseUrl.replace(/\/+$/, '');
        this.apiKey = opts.apiKey;
        this.hmacSecret = opts.hmacSecret;
        this.fetchImpl = opts.fetchImpl ?? fetch;
        this.clock = opts.clock ?? (() => new Date());
    }

    async check(req: AccessCheckRequest): Promise<AccessDecision> {
        const res = await this.post('/v2/access/check', req);
        return res as AccessDecision;
    }

    async checkBatch(batch: BatchRequest): Promise<BatchResult> {
        const res = await this.post('/v2/access/check:batch', batch);
        return res as BatchResult;
    }

    private async post(path: string, payload: unknown): Promise<unknown> {
        const url = this.baseUrl + path;
        const body = JSON.stringify(payload);
        const hdrs: Record<string, string> = {'Content-Type': 'application/json'};

        const date = this.clock().toUTCString(); // RFC1123
        hdrs['Date'] = date;

        if (this.apiKey) hdrs['Authorization'] = `Bearer ${this.apiKey}`;

        if (this.hmacSecret) {
            const base = `POST ${path}\n${date}\n${body}`;
            if (nodeHmac) {
                hdrs['X-Signature'] = nodeHmac(base, this.hmacSecret);
            } else {
                hdrs['X-Signature'] = await webHmac(base, this.hmacSecret);
            }
        }

        const r = await this.fetchImpl(url, {method: 'POST', headers: hdrs, body});
        if (!r.ok) {
            const text = await r.text();
            const err = new Error(`Role API error ${r.status}`);
            (err as any).status = r.status;
            (err as any).body = text;
            throw err;
        }
        return await r.json();
    }
}
