package main

import (
	"context"
	"log"
	"net"
	"os"

	authv3 "github.com/envoyproxy/go-control-plane/envoy/service/auth/v3"
	corev3 "github.com/envoyproxy/go-control-plane/envoy/config/core/v3"
	typev3 "github.com/envoyproxy/go-control-plane/envoy/type/v3"
	"google.golang.org/grpc"
)

type server struct{ authv3.UnimplementedAuthorizationServer }

func (s *server) Check(ctx context.Context, req *authv3.CheckRequest) (*authv3.CheckResponse, error) {
	h := req.GetAttributes().GetRequest().GetHttp().GetHeaders()
	// Simple policy: deny if "x-role-debug-deny" is present; otherwise allow
	if _, bad := h["x-role-debug-deny"]; bad {
		return &authv3.CheckResponse{
			Status: &typev3.Status{Code: int32(7)}, // PERMISSION_DENIED
			HttpResponse: &authv3.CheckResponse_DeniedResponse{
				DeniedResponse: &authv3.DeniedHttpResponse{
					Status: &typev3.HttpStatus{Code: typev3.StatusCode_Forbidden},
					Body:   "denied by role adapter",
				},
			},
		}, nil
	}
	okHeaders := []*corev3.HeaderValueOption{
		{Header: &corev3.HeaderValue{Key: "x-role-check", Value: "allow"}},
	}
	return &authv3.CheckResponse{
		Status: &typev3.Status{Code: int32(0)},
		HttpResponse: &authv3.CheckResponse_OkResponse{
			OkResponse: &authv3.OkHttpResponse{HeadersToAdd: okHeaders},
		},
	}, nil
}

func main() {
	addr := ":9002"
	if v := os.Getenv("ROLE_ADAPTER_ADDR"); v != "" { addr = v }
	lis, err := net.Listen("tcp", addr)
	if err != nil { log.Fatalf("listen: %v", err) }
	s := grpc.NewServer()
	authv3.RegisterAuthorizationServer(s, &server{})
	log.Printf("role-envoy-adapter listening on %s", addr)
	if err := s.Serve(lis); err != nil {
		log.Fatalf("serve: %v", err)
	}
}
