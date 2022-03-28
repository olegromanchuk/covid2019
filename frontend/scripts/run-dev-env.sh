#!/bin/bash


echo "
---------- Note, that this script will start the docker with BINDED directory \"frontend\" and will try to install all compose/npm packages. 
Any changes in the /var/www/app/ inside the container will be replicated to the \"frontend\" directory on the host and vice versa. 
If this is not what you need, you can uncomment \"docker run\" without \"bind\" (check below) and uncomment \"COPY\" command in the Dockerfile"

read -p "Press enter to continue"

if [[ ! -f .env ]]; then
    echo "APP_NAME=Laravel
APP_ENV=testing
APP_KEY=base64:UlesxAgWqDiCARG88KeHqPAsbsjl7Plu1e31lj8rkX0=
APP_DEBUG=true
APP_URL=http://localhost
LOG_CHANNEL=stack
    
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=rootpassword" > .env
fi

if [[ ! -f Dockerfile ]]; then
    echo "Dockerfile is not present. Make sure that you run the script from the \"./frontend\" dir."
    exit 1
fi

docker-compose up -d
echo "sleep 120. Wait until everything is installed"
sleep 120  #wait untill database is up
#admin@mail.net - adminpass
echo 'INSERT INTO users VALUES (1,'"'"'Admin'"'"','"'"'admin@mail.net'"'"',NULL,'"'"'$2y$10$sqD12SHprOvX4SJo3LA9ieDVFKI5GORHZGeMxS20iBuTkB37TwsYu'"'"',NULL,'"'"'2022-03-27 20:12:48'"'"','"'"'2022-03-27 20:12:48'"'"');' | mysql -h 127.0.0.1 -u root -prootpassword laravel

## Old version with docker only
# #docker rmi adsos
# docker build -t adsos-dev-image .
# #docker stop adsos-devel
# #docker rm adsos-devel

# ## version without bind. Any changes inside the contained will NOT be propagated to the host. To use this version and docker you need uncomment "COPY" command in the Dockerfile
# #docker run -d --name=adsos-devel -p 3000:3000 adsos --mount type=bind,source="$(pwd)",target=/var/www/app

# ## version with bind. Any changes inside the contained will be propagated to the host
# echo "container start will take some time. composer install; npm install are running"
# docker run -d --name=adsos-devel -p 3000:3000 --mount type=bind,source="$(pwd)",target=/var/www/app adsos-dev-image