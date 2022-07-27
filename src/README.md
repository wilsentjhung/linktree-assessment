# The Solution


## Intro

The solution is written in PHP 8.1 with Laravel 9.2 framework (https://laravel.com/docs/9.x/releases). The app is a REST API that is also fully Dockerized. It uses several Docker containers such as:
- `php-fpm` - FastCGI process (https://hub.docker.com/_/php)
- `nginx` - web server (https://hub.docker.com/_/nginx)
- `mysql` - local DB (https://hub.docker.com/_/mysql)
- `redis` - local cache (https://hub.docker.com/_/redis)


## Local setup

**Prerequisites**:
For local development, `docker-compose` (https://docs.docker.com/compose/) is used. Please ensure that you have Docker installed on your machine (https://docs.docker.com/get-docker/).

**Steps**:
1. `cd <PROJECT>/src` - cd into the project's src directory
2. `cp .env.example .env` - copy env file
3. `docker-compose build` - to build docker environment
4. `docker-compose up -d` - bring up all Docker containers
5. `./docker-connect.sh` - to exec into Docker php-fpm container (might need to `chmod +x docker-connect.sh` if failed)
6. `composer install` - Composer install
7. `php artisan migrate` - do DB migrations
8. The app will be served on http://localhost:8010


## Run feature and unit tests

There are 25 tests implemented with 91 assertions using PHPUnit (https://laravel.com/docs/9.x/testing). Test data is also mocked using Laravel factory (https://laravel.com/docs/9.x/database-testing#defining-model-factories) and faker (https://github.com/FakerPHP/Faker) defined in `database/factories/*`. To run the tests, in the Docker container run:
`php artisan test` or `vendor/bin/phpunit`


## DB schemas

**Definitions**:
- All DB migrations are defined in `database/migrations/*`
- All DB factories are defined in `database/factories/*`
- MySQL DB is exposed on port `3310` locally 
- DB credentials are defined in `docker-compose.yaml` and `.env.example`

**Tables**:
- `users` table - user info to support `links`
  - `id` - PRIMARY,UNIQUE
  - `uuid` - SECONDARY,UNIQUE
  - `email` - VARCHAR(255),UNIQUE
  - `created_at` - DATETIME
  - `updated_at` - DATETIME
  - `deleted_at` - DATETIME
  - NOTE:
    - Run `php artisan migrate:fresh --seed` to seed some users into the local DB
  - TODO:
    - More coloumns to define a user
    - Authentication/authorization/ACL capabilities
- `links` table - base link info
  - `id` - PRIMARY,UNIQUE
  - `user_id` - FOREIGN - reference `users` (1-to-many)
  - `linkable_id` - FOREIGN - reference polymorphic link type (1-to-1)
  - `linkable_type` - VARCHAR(255) - link type (`classic,music,shows`)
  - `created_at` - DATETIME
  - `updated_at` - DATETIME
  - `deleted_at` - DATETIME
- `classic_links` table - top-level classic link info
  - `id` - PRIMARY,UNIQUE
  - `title` - VARCHAR(144),INDEX
  - `url` - VARCHAR(255)
  - TODO:
    - More coloumns to define a classic link i.e. `thumbnail_url,attachments`, etc.
- `music_links` table - top-level mucic link info
  - `id` - PRIMARY,UNIQUE
  - `title` - VARCHAR(144),INDEX
  - TODO:
    - More coloumns to define a music link i.e. `summary`, etc.
- `shows_links` table - top-level shows link info 
  - `id` - PRIMARY,UNIQUE
  - `title` - VARCHAR(144),INDEX
  - TODO:
    - More coloumns to define a shows link i.e. `summary`, etc.
- `sublinks` table - child link info mainly for music and shows
  - `id` - PRIMARY,UNIQUE
  - `link_id` - FOREIGN - reference `links` (1-to-many)
  - `linkable_id` - FOREIGN - reference polymorphic sublink type (1-to-1)
  - `linkable_type` - VARCHAR(255) - sublink type (`musicSublink,showsSublink`)
  - `created_at` - DATETIME
  - `updated_at` - DATETIME
  - `deleted_at` - DATETIME
- `music_sublinks` table - child music link info
  - `id` - PRIMARY,UNIQUE
  - `name` - VARCHAR(255),INDEX
  - `url` - VARCHAR(255)
- `shows_sublinks` table - child shows link info
  - `id` - PRIMARY,UNIQUE
  - `name` - VARCHAR(255),INDEX
  - `url` - VARCHAR(255)
  - `status` - ENUM(`on-sale,not-on-sale,sold-out`)
  - `date` - DATE,NULL
  - `venue` - VARCHAR(255),NULL


## API specs

**Definitions**:
- Local base URL is http://localhost:8010
- All API routes are defined in `routes/api.php`
- The controller for the link routes is defined in `app/Http/Controllers/Api/V1/LinkController.php`
- The controller logic helper is defined in `app/Handlers/LinkHandler.php`

**Routes**:
- `GET /api/v1/links` - index links by user uuid
  - Query params:
    - `userUuid` - `required|uuid`
    - `sortBy` - `in:created_at`
    - `orderBy` - `in:asc,desc` - `asc` by default
- `POST /api/v1/links` - store a link for the given user uuid
  - Base payload:
    - `userUuid` - `required|uuid`
    - `type` - `required|in:classic,music,shows` - link type
  - Additional payload for classic link type (`type=classic`):
    - `title` - `required|string|max:144`
    - `url` - `required|url`
  - Additional payload for music link type (`type=music`):
    - `title` - `required|string|max:144`
    - `sublinks` - `required|nullable|array`
    - `sublinks.*` - `required|json`:
      - `name` - `required|string`
      - `url` - `required|url`
  - Additional payload for shows link type (`type=string`):
    - `title` - `required|string|max:144`
    - `sublinks` - `required|nullable|array`
    - `sublinks.*` - `required|json`:
      - `name` - `required|string`
      - `url` - `required|url`
      - `status` - `required,in:on-sale,not-on-sale,sold-out`
      - `date` - `date`
      - `venue` - `string`

