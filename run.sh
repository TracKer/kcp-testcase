#!/bin/bash

# Build the Docker image
docker build --build-arg APP_ENV=production -f ./Dockerfile -t kcp-image .

# Run the Docker container
docker run --env-file .env kcp-image
