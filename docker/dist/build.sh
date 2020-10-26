#!/bin/bash

set -e #stop the script if any command fails

docker build -t ades-base .. -f ../runtime-base.dockerfile
docker build -t tuantu/ades:latest -f Dockerfile ../../