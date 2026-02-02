package com.smartresponsor.role;

public class ClientSmoke {
    public static void main(String[] args) throws Exception {
        Client c = new Client();
        c.baseUrl = "http://127.0.0.1:8080";
        Scope s = new Scope(); s.key = "global"; s.type = "global";
        // This is a smoke: it just compiles; actual HTTP requires server.
        System.out.println("SDK ready: "+(c!=null && s!=null));
    }
}
