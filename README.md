
## SETUP

- Clone the repo
- Run `docker run --rm \
  -u "$(id -u):$(id -g)" \
  -v "$(pwd):/var/www/html" \
  -w /var/www/html \
  laravelsail/php81-composer:latest \
  composer install --ignore-platform-reqs`
- Check existence of `.env` file and set custom values for `APP_PORT`, `FORWARD_DB_PORT` and `VITE_PORT` if you have other containers
- Run `vendor/bin/sail up -d`
- Run `vendor/bin/sail artisan migrate --seed`
- You can now use the application

## TESTING
- Make sure you have `.env.testing` file and database connection is correct
- Run `vendor/bin/sail artisan migrate:fresh --env=testing`
- Run `vendor/bin/sail artisan test`
