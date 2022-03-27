#!/bin/bash

if [[ ! -f .env ]]; then
    echo "APP_NAME=Laravel
    APP_ENV=testing
    APP_KEY=base64:UlesxAgWqDiCARG88KeHqPAsbsjl7Plu1e31lj8rkX0=
    APP_DEBUG=true
    APP_URL=http://localhost
    
    LOG_CHANNEL=stack" > .env
fi

if [[ ! -f Dockerfile ]]; then
    echo "Dockerfile is not present. Make sure that you run the script from the \"./frontend\" dir."
    exit 1
fi

#docker rmi adsos
docker build -t adsos .
docker stop adsos-devel
docker rm adsos-devel
docker run -d --name=adsos-devel -p 3000:3000 -t -i adsos