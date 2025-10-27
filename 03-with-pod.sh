#!/bin/bash

if ! podman pod exists TodoAPP; then
  podman pod create --name TodoAPP -p 127.0.0.1:9000:9000
fi

podman run --pod TodoAPP \
      -d --env MYSQL_ALLOW_EMPTY_PASSWORD=1 \
      mysql:8.4

podman run  --pod TodoAPP \
      -ti --rm \
      localhost/todo:latest

podman pod stop TodoAPP
podman pod rm TodoAPP
