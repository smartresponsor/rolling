package role

import (
	"context"
	"encoding/json"
	"net/http"
	"net/http/httptest"
	"testing"
)

func TestCheck(t *testing.T) {
	s := httptest.NewServer(http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		w.Header().Set("Content-Type", "application/json")
		_ = json.NewEncoder(w).Encode(map[string]any{
			"allow": true, "reason": "ok", "obligations": []any{}, "rev": "1",
		})
	}))
	defer s.Close()

	c := &Client{BaseURL: s.URL}
	scope := Scope{Key: "global", Type: "global"}
	res, err := c.Check(context.Background(), "user:1", "message.read", scope, nil)
	if err != nil { t.Fatal(err) }
	if !res.Allow { t.Fatal("expected allow=true") }
}
