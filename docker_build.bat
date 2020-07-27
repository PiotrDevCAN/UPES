docker build -t upes . --no-cache 
docker run -dit -p 8086:8080  --name upes -v C:/Users/RobDaniel/git/uPes:/var/www/html --env-file C:/Users/RobDaniel/git/uPES/dev_env.list upes
