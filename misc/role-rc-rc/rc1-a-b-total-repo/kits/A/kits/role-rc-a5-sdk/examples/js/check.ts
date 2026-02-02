import {Client} from '../../sdk/js/client';

const base = process.env.ROLE_PDP_BASE_URL ?? 'http://localhost:8000';
const apiKey = process.env.ROLE_PDP_API_KEY ?? undefined;
const hmacSecret = process.env.ROLE_PDP_HMAC ?? undefined;

const cli = new Client(base, {apiKey, hmacSecret});
const res = await cli.check({
    subjectId: 'u1',
    action: 'message.read',
    scopeType: 'global'
});

console.log(JSON.stringify(res, null, 2));
