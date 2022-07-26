# The Solution


## Intro

The solution is written in PHP 8.1 with Laravel 9.2 framework (https://laravel.com/docs/9.x/releases). The application is a REST API that is also fully Dockerized. It uses several Docker containers such as:
- `php-fpm` - PHP FastCGI process
- `nginx` - web server
- `mysql` - local DB
- `redis` - local cache


## Local setup

For local development, `docker-compose` (https://docs.docker.com/compose/) is used. Follow the steps below:
1. Ensure you have Docker installed on your machine (https://docs.docker.com/get-docker/)
2. Clone this repo
3. `cd <PROJECT>/src` - cd into the project's src directory
4. `cp .env.example .env` - copy env file
5. `docker-compose build` - to build docker environment
6. `docker-compose up -d` - bring up all Docker containers
7. `./docker-connect.sh` - to exec into Docker php-fpm container (might need to `chmod +x docker-connect.sh` if failed)
8. `composer install` - Composer install
9. `php artisan migrate` - do DB migrations


## Run feature and unit tests

There are 25 tests implemented with 91 assertions using PHPUnit (https://laravel.com/docs/9.x/testing). Test data is also mocked using Laravel factory (https://laravel.com/docs/9.x/database-testing#defining-model-factories) and faker(https://github.com/FakerPHP/Faker). To run the tests, in the Docker container run:
```
php artisan test
```
or
```
vendor/bin/phpunit
```


## DB schemas

- `users` table - user info to support `links`
  - `id` - PRIMARY, UNIQUE
  - `uuid` - SECONDARY, UNIQUE
  - `email` - VARCHAR(255), UNIQUE
  - `created_at` - DATETIME
  - `updated_at` - DATETIME
  - `deleted_at` - DATETIME
  - NOTE:
    - run `php artisan migrate:fresh --seed` to seed some users into the local DB
  - TODO:
    - more coloumns to define a user
    - authentication/authorization capabilities
- `links` table - base link info
  - `id` - PRIMARY, UNIQUE
  - `user_id` - FOREIGN - reference `users` (1-to-many)
  - `linkable_id` - FOREIGN - reference polymorphic link type (1-to-1)
  - `linkable_type` - VARCHAR(255) - link type (`classic`, `music`, `shows`)
  - `created_at` - DATETIME
  - `updated_at` - DATETIME
  - `deleted_at` - DATETIME
- `classic_links` table - top-level classic link info
  - `id` - PRIMARY, UNIQUE
  - `title` - VARCHAR(144), INDEX
  - `url` - VARCHAR(255)
  - TODO:
    - more coloumns to define a classic link i.e. `thumbnail_url`, `attachments`, etc.
- `music_links` table - top-level mucic link info
  - `id` - PRIMARY, UNIQUE
  - `title` - VARCHAR(144), INDEX
  - TODO:
    - more coloumns to define a music link i.e. `thumbnail_url`, `attachments`, etc.
- `shows_links` table - top-level shows link info 
  - `id` - PRIMARY, UNIQUE
  - `title` - VARCHAR(144), INDEX
  - TODO:
    - more coloumns to define a shows link i.e. `thumbnail_url`, `attachments`, etc.
- `sublinks` table - child link info mainly for music and shows
  - `id` - PRIMARY, UNIQUE
  - `link_id` - FOREIGN - reference `links` (1-to-many)
  - `linkable_id` - FOREIGN - reference polymorphic sublink type (1-to-1)
  - `linkable_type` - VARCHAR(255) - sublink type (`musicSublink`, `showsSublink`)
  - `created_at` - DATETIME
  - `updated_at` - DATETIME
  - `deleted_at` - DATETIME
- `music_sublinks` table - child music link info
  - `id` - PRIMARY, UNIQUE
  - `name` - VARCHAR(255), INDEX
  - `url` - VARCHAR(255)
- `shows_sublinks` table - child shows link info
  - `id` - PRIMARY, UNIQUE
  - `name` - VARCHAR(255), INDEX
  - `url` - VARCHAR(255)
  - `status` - ENUM(`on-sale`, `not-on-sale`, `sold-out`)
  - `date` - DATE, NULL
  - `venue` - VARCHAR(255), NULL

