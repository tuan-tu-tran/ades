#!/bin/bash

docker build -t ades-base .. -f runtime-base.dockerfile
docker build -t ades:latest -f Dockerfile ../../