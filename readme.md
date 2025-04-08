# Nginx Auth Request test with JS Module

## Install
- Git Clone
- Copy `.env.example` to `.env` file. Update the `.env` file as needed.
- Run `docker compose build`
- Run `docker compose run --rm auth-app composer install`
- Run `docker compose run --rm auth-app php artisan key:generate`
- Run `docker compose run --rm service-app composer install`
- Run `docker compose run --rm service-app php artisan key:generate`
- Run `docker compose up -d`

## Usage

### CURL the first API to get token.

```
curl --location 'http://localhost:8080/api/auth/login' \
    --form 'username="abc"' \
    --form 'password="1234"'
```

In response, you will receive the `token` value. We will use it in next API. Token will be valid for one minute.

### Use token in header for next API

```
curl --location 'http://localhost:8080/api/service/status' \
    --header 'X-Token: token-value'
```

In respone, you will see success API response if token valid.


## References

- https://www.f5.com/company/blog/nginx/validating-oauth-2-0-access-tokens-nginx
