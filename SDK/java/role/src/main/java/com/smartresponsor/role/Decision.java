package com.smartresponsor.role;

import java.util.*;

public class Decision {
    public boolean allow;
    public String reason;
    public List<Obligation> obligations = new ArrayList<>();
    public String rev;

    public static Decision fromJson(String json) {
        Map<String, Object> obj = MiniJson.parseObject(json);
        Decision d = new Decision();
        d.allow = Boolean.TRUE.equals(obj.get("allow"));
        Object r = obj.get("reason"); d.reason = r != null ? r.toString() : null;
        Object rev = obj.get("rev"); d.rev = rev != null ? rev.toString() : null;
        Object obs = obj.get("obligations");
        if (obs instanceof List) {
            for (Object o : (List<?>)obs) {
                if (o instanceof Map) {
                    Map<?,?> m = (Map<?,?>)o;
                    Obligation ob = new Obligation();
                    Object t = m.get("type"); if (t != null) ob.type = t.toString();
                    Object p = m.get("params"); if (p instanceof Map) ob.params = (Map<String,Object>)p;
                    d.obligations.add(ob);
                }
            }
        }
        return d;
    }
}
