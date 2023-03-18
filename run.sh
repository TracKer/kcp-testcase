#!/bin/bash

PWD=$(pwd)

# Build the Docker image
docker build --build-arg APP_ENV=production -f ./Dockerfile -t kcp-image .

# Run the Docker container with a mounted volume
docker run -it -v "$PWD":/app --env-file .env kcp-image bash
