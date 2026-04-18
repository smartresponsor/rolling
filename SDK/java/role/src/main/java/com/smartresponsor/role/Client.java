package com.smartresponsor.role;

import javax.crypto.Mac;
import javax.crypto.spec.SecretKeySpec;
import java.net.URI;
import java.net.http.HttpClient;
import java.net.http.HttpRequest;
import java.net.http.HttpResponse;
import java.nio.charset.StandardCharsets;
import java.time.Duration;
import java.time.ZonedDateTime;
import java.time.format.DateTimeFormatter;
import java.util.Base64;
import java.util.Map;

public class Client {
    private final String endpoint;
    private final String hmacKey;
    private final HttpClient http;
    private final int retries;
    private final long backoffMs;
    private final int timeoutMs;

    public Client(String endpoint, String hmacKey) {
        this(endpoint, hmacKey, 800, 2, 120);
    }
    public Client(String endpoint, String hmacKey, int timeoutMs, int retries, long backoffMs) {
        this.endpoint = endpoint.endsWith("/") ? endpoint.substring(0, endpoint.length()-1) : endpoint;
        this.hmacKey = hmacKey;
        this.timeoutMs = timeoutMs;
        this.retries = retries;
        this.backoffMs = backoffMs;
        this.http = HttpClient.newBuilder().build();
    }

    private String sign(String payload, String dateIso) throws Exception {
        if (hmacKey == null || hmacKey.isEmpty()) return null;
        Mac mac = Mac.getInstance("HmacSHA256"); mac.init(new SecretKeySpec(hmacKey.getBytes(StandardCharsets.UTF_8), "HmacSHA256"));
        byte[] sig = mac.doFinal((dateIso + "\n" + payload).getBytes(StandardCharsets.UTF_8));
        return "hmac-sha256:" + Base64.getEncoder().encodeToString(sig);
    }

    public Map<String,Object> check(String subject, String relation, String resource, Map<String,Object> context, String consistency) throws Exception {
        String payload = String.format("{\"subject\":\"%s\",\"relation\":\"%s\",\"resource\":\"%s\",\"context\":%s}",
                subject, relation, resource, context == null ? "{}" : Json.minify(context));
        String url = this.endpoint + "/check" + (consistency != null ? ("?consistency=" + consistency) : "");
        int attempt = 0;
        Exception last = null;
        while (attempt <= this.retries) {
            try {
                String dateIso = ZonedDateTime.now().format(DateTimeFormatter.ISO_OFFSET_DATE_TIME);
                HttpRequest.Builder b = HttpRequest.newBuilder()
                        .timeout(Duration.ofMillis(this.timeoutMs))
                        .uri(URI.create(url))
                        .header("Content-Type","application/json")
                        .POST(HttpRequest.BodyPublishers.ofString(payload));
                if (consistency != null) b.header("X-Role-Consistency", consistency);
                String sig = sign(payload, dateIso);
                if (sig != null) { b.header("X-Role-Date", dateIso).header("X-Role-Signature", sig); }
                HttpResponse<String> resp = http.send(b.build(), HttpResponse.BodyHandlers.ofString());
                return Json.parseToMap(resp.body());
            } catch (Exception e) {
                last = e; if (attempt == this.retries) break;
                Thread.sleep(this.backoffMs * (1<<attempt));
                attempt++;
            }
        }
        throw last;
    }

    // Tiny JSON helpers to avoid dependencies
    static class Json {
        static String minify(Map<String,Object> m) {
            StringBuilder sb = new StringBuilder("{"); boolean first = true;
            for (var e : m.entrySet()) {
                if (!first) sb.append(',');
                first = false;
                sb.append('"').append(escape(e.getKey())).append('"').append(':');
                Object v = e.getValue();
                if (v == null) sb.append("null");
                else if (v instanceof Number || v instanceof Boolean) sb.append(v.toString());
                else sb.append('"').append(escape(String.valueOf(v))).append('"');
            }
            sb.append('}'); return sb.toString();
        }
        static String escape(String s) { return s.replace("\\","\\\\").replace("\"","\\\""); }
        @SuppressWarnings("unchecked")
        static Map<String,Object> parseToMap(String json) {
            // very small parser: expect {"allowed":true,...} — fallback when parsing fails
            if (json == null) return Map.of("allowed", false);
            boolean ok = json.contains("\"allowed\":true") || json.contains("\"allowed\": true");
            return Map.of("allowed", ok, "raw", json);
        }
    }
}
