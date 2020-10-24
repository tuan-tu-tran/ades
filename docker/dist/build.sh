#!/bin/bash

cd ..
docker build -t ades-base .

cd dist
docker build -t ades:latest -f Dockerfile ../../