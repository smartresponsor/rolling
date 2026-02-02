package role

import (
	"bytes"
	"context"
	"crypto/hmac"
	"crypto/sha256"
	"encoding/hex"
	"encoding/json"
	"errors"
	"io"
	"net/http"
	"time"
)

type Client struct {
	BaseURL    string
	APIKey     string
	HMACSecret string // optional
	HTTP       *http.Client
}

func (c *Client) httpClient() *http.Client {
	if c.HTTP != nil {
		return c.HTTP
	}
	return &http.Client{Timeout: 3 * time.Second}
}

func (c *Client) sign(date string, body []byte) string {
	if c.HMACSecret == "" {
		return ""
	}
	mac := hmac.New(sha256.New, []byte(c.HMACSecret))
	mac.Write([]byte(date))
	mac.Write(body)
	return hex.EncodeToString(mac.Sum(nil))
}

func (c *Client) post(ctx context.Context, path string, payload any) (*http.Response, []byte, error) {
	b, err := json.Marshal(payload)
	if err != nil { return nil, nil, err }
	req, err := http.NewRequestWithContext(ctx, http.MethodPost, c.BaseURL+path, bytes.NewReader(b))
	if err != nil { return nil, nil, err }
	req.Header.Set("Content-Type", "application/json")
	if c.APIKey != "" {
		req.Header.Set("Authorization", "Bearer "+c.APIKey)
	}
	if c.HMACSecret != "" {
		date := time.Now().UTC().Format(time.RFC3339)
		req.Header.Set("X-Date", date)
		req.Header.Set("X-Signature", c.sign(date, b))
	}
	resp, err := c.httpClient().Do(req)
	if err != nil { return nil, nil, err }
	defer resp.Body.Close()
	body, _ := io.ReadAll(resp.Body)
	if resp.StatusCode >= 300 {
		return resp, body, errors.New("http status "+resp.Status)
	}
	return resp, body, nil
}

func (c *Client) Check(ctx context.Context, subject, action string, scope Scope, contextMap map[string]interface{}) (*Decision, error) {
	payload := map[string]any{
		"subject": subject,
		"action":  action,
		"scope":   scope,
		"context": contextMap,
	}
	_, body, err := c.post(ctx, "/v2/access/check", payload)
	if err != nil { return nil, err }
	var out struct{ Allow bool `json:"allow"`; Reason string `json:"reason"`; Obligations []Obligation `json:"obligations"`; Rev string `json:"rev"` }
	if err := json.Unmarshal(body, &out); err != nil { return nil, err }
	return &Decision{Allow: out.Allow, Reason: out.Reason, Obligations: out.Obligations, Rev: out.Rev}, nil
}

func (c *Client) BatchCheck(ctx context.Context, reqs []CheckRequest) ([]Decision, error) {
	payload := map[string]any{ "items": reqs }
	_, body, err := c.post(ctx, "/v2/access/check:batch", payload)
	if err != nil { return nil, err }
	var out struct{ Items []Decision `json:"items"` }
	if err := json.Unmarshal(body, &out); err != nil { return nil, err }
	return out.Items, nil
}
