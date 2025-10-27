#!/bin/bash

podman pod create --name TodoAPP -p 127.0.0.1:8080:80 -p 127.0.0.1:6379:6379

podman run --pod TodoAPP \
      -d --env MYSQL_ALLOW_EMPTY_PASSWORD=1 \
      --name mysql \
      mysql:8.4

podman run --pod TodoAPP \
      -d \
      --name valkey \
      docker.io/valkey/valkey:9-alpine

podman run  --pod TodoAPP \
      -d \
      --name fpm \
      localhost/todo-pod-fpm:latest

podman run --pod TodoAPP \
      -d \
      --name caddy \
      --volumes-from fpm \
      localhost/todo-pod-caddy:latest

podman logs -f fpm

podman pod stop TodoAPP
podman pod rm TodoAPP

