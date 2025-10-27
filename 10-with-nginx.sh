#!/bin/bash

podman pod create --name TodoAPP

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
      localhost/todo-pod-fpm-socket:latest

podman run --pod TodoAPP \
      -d \
      --name nginx \
      --volumes-from fpm \
      -v /run/TodoAPP:/app/sockets \
      localhost/todo-pod-nginx-socket:latest

podman logs -f fpm

podman pod stop TodoAPP
podman pod rm TodoAPP

