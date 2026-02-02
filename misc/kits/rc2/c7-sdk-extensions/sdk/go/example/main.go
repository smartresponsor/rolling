package main

import (
	"context"
	"fmt"
	role "github.com/smartresponsor/role-sdk-go"
)

func main() {
	client := &role.Client{
		BaseURL: "http://127.0.0.1:8080",
		APIKey:  "devkey",
	}
	scope := role.Scope{Key: "global", Type: "global"}
	dec, err := client.Check(context.Background(), "user:42", "message.read", scope, map[string]any{})
	if err != nil { panic(err) }
	fmt.Printf("allow=%v reason=%s rev=%s\n", dec.Allow, dec.Reason, dec.Rev)
}
