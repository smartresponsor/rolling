// npx ts-node examples/ts/check_sample/main.ts (if ts-node installed), or transpile first.
import {RoleClient} from '../../../sdk/ts/role/src/client';

async function main() {
    const cli = new RoleClient(process.env.ROLE_ENDPOINT || 'http://localhost:8088/v2', process.env.ROLE_HMAC || null);
    const out = await cli.check({
        subject: 'user:1',
        relation: 'viewer',
        resource: 'doc:42',
        context: {ip: '127.0.0.1'},
        consistency: 'eventual'
    });
    console.log(out);
}

main().catch(e => {
    console.error(e);
    process.exit(1);
});
