package com.smartresponsor.role;

import java.util.*;

/** Minimal JSON parser for objects/arrays/strings/numbers/bools/null. Not robust; for SDK demo only. */
public final class MiniJson {
    private final String s; private int i;
    private MiniJson(String s){ this.s=s; this.i=0; }

    public static Map<String,Object> parseObject(String s){
        Object v = new MiniJson(s).parseValue();
        if (v instanceof Map) return (Map<String,Object>)v;
        throw new RuntimeException("not a JSON object");
    }

    private void ws(){ while(i<s.length() && Character.isWhitespace(s.charAt(i))) i++; }

    private Object parseValue(){
        ws();
        if (i>=s.length()) throw new RuntimeException("eof");
        char c=s.charAt(i);
        if(c=='{') return parseObject();
        if(c=='[') return parseArray();
        if(c=='"') return parseString();
        if(c=='t'){ expect("true"); return Boolean.TRUE; }
        if(c=='f'){ expect("false"); return Boolean.FALSE; }
        if(c=='n'){ expect("null"); return null; }
        return parseNumber();
    }

    private Map<String,Object> parseObject(){
        Map<String,Object> m=new LinkedHashMap<>();
        i++; ws();
        if (s.charAt(i)=='}'){ i++; return m; }
        while(true){
            ws(); String k=parseString(); ws();
            if (s.charAt(i++)!=':') throw new RuntimeException("colon"); ws();
            Object v=parseValue(); m.put(k,v); ws();
            char c=s.charAt(i++);
            if(c=='}') break;
            if(c!=',') throw new RuntimeException("comma");
        }
        return m;
    }

    private List<Object> parseArray(){
        List<Object> a=new ArrayList<>(); i++; ws();
        if (s.charAt(i)==']'){ i++; return a; }
        while(true){
            Object v=parseValue(); a.add(v); ws();
            char c=s.charAt(i++);
            if(c==']') break;
            if(c!=',') throw new RuntimeException("comma");
        }
        return a;
    }

    private String parseString(){
        StringBuilder b=new StringBuilder(); if (s.charAt(i++)!='"') throw new RuntimeException("quote");
        while(true){
            if(i>=s.length()) throw new RuntimeException("eof string");
            char c=s.charAt(i++);
            if(c=='"') break;
            if(c=='\\'){
                char e=s.charAt(i++);
                switch(e){
                    case '"': b.append('"'); break;
                    case '\\': b.append('\\'); break;
                    case '/': b.append('/'); break;
                    case 'b': b.append('\b'); break;
                    case 'f': b.append('\f'); break;
                    case 'n': b.append('\n'); break;
                    case 'r': b.append('\r'); break;
                    case 't': b.append('\t'); break;
                    case 'u':
                        String hex=s.substring(i,i+4); i+=4;
                        b.append((char)Integer.parseInt(hex,16));
                        break;
                    default: throw new RuntimeException("esc");
                }
            } else {
                b.append(c);
            }
        }
        return b.toString();
    }

    private Number parseNumber(){
        int j=i; while(i<s.length()){
            char c=s.charAt(i);
            if((c>='0'&&c<='9')||c=='-'||c=='+'||c=='.'||c=='e'||c=='E'){ i++; } else break;
        }
        String sub=s.substring(j,i);
        if (sub.indexOf('.')>=0 || sub.indexOf('e')>=0 || sub.indexOf('E')>=0) return Double.parseDouble(sub);
        return Long.parseLong(sub);
    }

    private void expect(String lit){ if(!s.regionMatches(i, lit, 0, lit.length())) throw new RuntimeException("exp"+lit); i+=lit.length(); }
}
