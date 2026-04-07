# syntax=docker/dockerfile:1
FROM golang:1.22 AS build
WORKDIR /app
COPY . .
RUN go mod tidy && CGO_ENABLED=0 go build -o /out/role-envoy-adapter main.go

FROM gcr.io/distroless/base-debian12
COPY --from=build /out/role-envoy-adapter /role-envoy-adapter
EXPOSE 9002
ENTRYPOINT ["/role-envoy-adapter"]
