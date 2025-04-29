#!/bin/bash

export WEBSERVER_MODE=artisan
docker network inspect lcr-network >/dev/null 2>&1 || docker network create --driver bridge lcr-network
docker build --no-cache --build-arg UID=$(id -u) --build-arg GID=$(id -g) --build-arg USER=${USER} -t phpinstalllcr -f .docker/install/Dockerfile .
docker compose build --build-arg UID=$(id -u) --build-arg GID=$(id -g) --build-arg USER=${USER}
