# Slim Framework Example App

this is a simple API implementation using jsonplaceholder.

## Running Locally

### Install the Application

Install the dependencies using Composer:

```bash
composer install
```

Next, execute the migration script to prepare the database:

```bash
composer migrate
```

Create the database, rename the **.env.example** to **.env** and configure the database.


To run the application in development, you can run this command

```bash
composer start
```

## Running With Docker

use `docker-compose` to run the app with `docker`, so you can run this command:
```bash
docker-compose up -d
```

Create the database, rename the **.env.example** to **.env** and configure the database.

Next, execute the migration script to prepare the database:

```bash
docker compose exec slim composer migrate
```

After that, open `http://localhost:8080` in your browser.

That's it!
