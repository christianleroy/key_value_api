# Getting Started

## Required Installations
- Docker
- PHP
- Composer
- Laravel
- Postgres
- npm

## Setup

Copy .env.template to a local .env file and fill in the values as required.

```text
APP_NAME=secret_api
APP_ENV=local
APP_KEY=<KEY>
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=secret_db
DB_USERNAME=secret_user
DB_PASSWORD=secretpass
```

Generate app key and set it as the APP_KEY variable.
```bash
php artisan key:generate
```

The default values above are used in docker-compose.yml. This is how we will run the project locally. Modify docker-compose.yml accordingly if you changed the default values.

## Running the application

### Build and run
```bash
docker compose up -d --build
```
### Build and run the application only, database will not be restarted.

```bash
docker compose up -d --build app
```

### Stop the entire service

```bash
docker compose down
```

### Or stop only the application

```bash
docker compose stop app
```

## Swagger

### Regenerating docs

Run the below command to update the Swagger documentation based on Controller annotation changes.

```bash
php artisan l5-swagger:generate 
```

### Testing the API
To access Swagger, go to:
http://localhost:8000/

Experimental UI frontend:
http://localhost:8000/key-values
