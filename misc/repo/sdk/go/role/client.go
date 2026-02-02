package role

import (
	"bytes"
	"context"
	"crypto/hmac"
	"crypto/sha256"
	"encoding/base64"
	"encoding/json"
	"errors"
	"io"
	"net/http"
	"time"
)

type Client struct {
	Endpoint string
	HmacKey  string
	Timeout  time.Duration
	Retries  int
	Backoff  time.Duration
	Client   *http.Client
}

type CheckReq struct {
	Subject     string                 `json:"subject"`
	Relation    string                 `json:"relation"`
	Resource    string                 `json:"resource"`
	Context     map[string]any         `json:"context,omitempty"`
	Consistency string                 `json:"-"`
}

type CheckRes struct {
	Allowed bool                   `json:"allowed"`
	Meta    map[string]any         `json:"meta,omitempty"`
}

func New(endpoint, hmacKey string) *Client {
	return &Client{Endpoint: endpoint, HmacKey: hmacKey, Timeout: 800*time.Millisecond, Retries: 2, Backoff: 120*time.Millisecond, Client: &http.Client{}}
}

func (c *Client) sign(b []byte, dateIso string) string {
	if c.HmacKey == "" { return "" }
	h := hmac.New(sha256.New, []byte(c.HmacKey))
	h.Write([]byte(dateIso + "\n"))
	h.Write(b)
	return "hmac-sha256:" + base64.StdEncoding.EncodeToString(h.Sum(nil))
}

func (c *Client) Check(ctx context.Context, req CheckReq) (CheckRes, error) {
	body, _ := json.Marshal(map[string]any{
		"subject": req.Subject, "relation": req.Relation, "resource": req.Resource, "context": req.Context,
	})
	url := c.Endpoint
	if url[len(url)-1] == '/' { url = url[:len(url)-1] }
	url += "/check"
	if req.Consistency != "" { url += "?consistency=" + req.Consistency }
	attempt := 0
	var lastErr error
	for {
		dateIso := time.Now().UTC().Format(time.RFC3339Nano)
		httpReq, _ := http.NewRequestWithContext(ctx, http.MethodPost, url, bytes.NewReader(body))
		h := httpReq.Header
		h.Set("Content-Type", "application/json")
		if req.Consistency != "" { h.Set("X-Role-Consistency", req.Consistency) }
		if sig := c.sign(body, dateIso); sig != "" { h.Set("X-Role-Date", dateIso); h.Set("X-Role-Signature", sig) }
		c.Client.Timeout = c.Timeout
		resp, err := c.Client.Do(httpReq)
		if err == nil {
			defer resp.Body.Close()
			b, _ := io.ReadAll(resp.Body)
			var out CheckRes
			if json.Unmarshal(b, &out) == nil { return out, nil }
			return CheckRes{Allowed: resp.StatusCode == 200}, nil
		}
		lastErr = err
		if attempt >= c.Retries { break }
		time.Sleep(c.Backoff * (1 << attempt))
		attempt++
	}
	return CheckRes{Allowed: false}, lastErr
}
