#!/bin/bash

podman build -t todo-pod-fpm-socket:latest -f pod-socket/fpm/Containerfile .
podman build -t todo-pod-caddy-socket:latest -f pod-socket/caddy/Containerfile .

