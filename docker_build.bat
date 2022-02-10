docker build -t kpes . --no-cache 
docker run -dit -p 8090:8080  --name kpes -v C:/CETAapps/uPES:/var/www/html --env-file C:/CETAapps/uPES/dev_env.list kpes
