package main

import (
	"context"
	"fmt"
	"os"
	role "smartresponsor/role"
)

func main() {
	endpoint := os.Getenv("ROLE_ENDPOINT")
	if endpoint == "" { endpoint = "http://localhost:8088/v2" }
	hmac := os.Getenv("ROLE_HMAC")
	cli := role.New(endpoint, hmac)
	res, err := cli.Check(context.Background(), role.CheckReq{Subject:"user:1", Relation:"viewer", Resource:"doc:42", Consistency:"eventual"})
	fmt.Println("res:", res, "err:", err)
}
