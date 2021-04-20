docker build -t upes . --no-cache 
docker run -dit -p 8086:8080  --name upes -v C:/CETAapps/uPES:/var/www/html --env-file C:/CETAapps/uPES/dev_env.list upes
