package com.smartresponsor.role.example;

import com.smartresponsor.role.Client;
import java.util.Map;

public class Main {
    public static void main(String[] args) throws Exception {
        String endpoint = System.getenv().getOrDefault("ROLE_ENDPOINT", "http://localhost:8088/v2");
        String hmac = System.getenv().getOrDefault("ROLE_HMAC", "");
        Client c = new Client(endpoint, hmac);
        Map<String,Object> res = c.check("user:1", "viewer", "doc:42", Map.of("ip","127.0.0.1"), "eventual");
        System.out.println(res);
    }
}
