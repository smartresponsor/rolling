/* Minimal TypeScript client for SmartResponsor/Role */
import crypto from 'node:crypto';

export type CheckRequest = {
    subject: string;
    relation: string;
    resource: string;
    context?: Record<string, unknown>;
    consistency?: 'strong' | 'eventual';
};

export type CheckResponse = {
    allowed: boolean;
    meta?: any;
};

export class RoleClient {
    constructor(
        private endpoint: string,
        private hmacKey?: string | null,
        private timeoutMs: number = 800,
        private retries: number = 2,
        private backoffMs: number = 120
    ) {
    }

    private sign(payload: string, dateIso: string): string | null {
        if (!this.hmacKey) return null;
        const h = crypto.createHmac('sha256', this.hmacKey);
        h.update(dateIso + '\n' + payload);
        const sig = h.digest('base64');
        return `hmac-sha256:${sig}`;
    }

    async check(req: CheckRequest): Promise<CheckResponse> {
        const payload = JSON.stringify({
            subject: req.subject, relation: req.relation, resource: req.resource, context: req.context || {}
        });
        const url = this.endpoint.replace(/\/$/, '') + '/check' + (req.consistency ? `?consistency=${req.consistency}` : '');
        let attempt = 0, lastErr: any = null;
        while (attempt <= this.retries) {
            try {
                const dateIso = new Date().toISOString();
                const headers: any = {'Content-Type': 'application/json'};
                const sig = this.sign(payload, dateIso);
                if (sig) {
                    headers['X-Role-Date'] = dateIso;
                    headers['X-Role-Signature'] = sig;
                }
                if (req.consistency) headers['X-Role-Consistency'] = req.consistency;
                const ctrl = new AbortController();
                const timer = setTimeout(() => ctrl.abort(), this.timeoutMs);
                const res = await fetch(url, {method: 'POST', body: payload, headers, signal: ctrl.signal as any});
                clearTimeout(timer);
                const text = await res.text();
                let json: any = null;
                try {
                    json = JSON.parse(text);
                } catch {
                    json = {allowed: res.ok};
                }
                return {allowed: !!json.allowed, meta: json};
            } catch (e) {
                lastErr = e;
                if (attempt === this.retries) break;
                await new Promise(r => setTimeout(r, this.backoffMs * Math.pow(2, attempt)));
            }
            attempt++;
        }
        return {allowed: false, meta: {error: (lastErr && (lastErr.message || String(lastErr))) || 'request failed'}};
    }
}
