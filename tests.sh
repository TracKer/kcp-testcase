#!/bin/bash

# Build the Docker image
docker build --build-arg APP_ENV=test -f ./Dockerfile -t kcp-image-test .

# Run the Docker container
docker run --env-file .env.tests kcp-image-test
