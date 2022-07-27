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
  - `linkable_id` - FOREIGN - reference polymorphic link type table (1-to-1)
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
  - `linkable_id` - FOREIGN - reference polymorphic sublink type table (1-to-1)
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

**Extensibility**:
In the DB scehmas, there are 2 levels of polymorphic relationships in `links` and `sublinks`. Therefore, when there are new link types or sublink types, we just need to define new polymorphic tables extending `links` and `sublinks` respectively. Examples:
- When there is a new link type i.e. `survey` (survey form):
  - Create a new link type `survey` in `links.linkable_type`
  - Create a new polymorphic link type table `survey_links` referencing `links` with columns:
    - `id` - PRIMARY,UNIQUE
    - `name` - VARCHAR(255),INDEX - form name
    - `form` - JSONB - form data
    - etc.
- When there is a new link type with sublinks i.e. `todo` (todo list):
  - Create a new link type `todo` in `links.linkable_type`
  - Create a new polymorphic link type table `todo_links` referencing `links` with columns:
    - `id` - PRIMARY,UNIQUE
    - `name` - VARCHAR(255),INDEX - todo list name
    - etc.
  - Create a new sublink type `todoSublink` in `sublinks.linkable_type` with columns:
    - `id` - PRIMARY,UNIQUE
    - `status` - ENUM(`done,not-done,not-available`) - status of todo item
    - `name` - VARCHAR(255),INDEX - todo item name
    - etc


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


## API usage examples

### Index links (GET)

**Example request**:
```
GET /api/v1/links?userUuid=d8f56fb5-f098-432a-84a9-71b7750fa187&orderBy=created_at&sortBy=desc
```

**Example 200 response**:
```
[
    {
        "id": 1,
        "linkable_type": "classic",
        "created_at": "2022-07-25T14:32:16.000000Z",
        "updated_at": "2022-07-25T14:32:16.000000Z",
        "deleted_at": null,
        "linkable": {
            "title": "Classic",
            "url": "https://www.google.com"
        },
        "sublinks": []
    },
    {
        "id": 2,
        "linkable_type": "music",
        "created_at": "2022-07-25T14:32:23.000000Z",
        "updated_at": "2022-07-25T14:32:23.000000Z",
        "deleted_at": null,
        "linkable": {
            "title": "Music"
        },
        "sublinks": [
            {
                "id": 1,
                "created_at": "2022-07-25T14:32:23.000000Z",
                "updated_at": "2022-07-25T14:32:23.000000Z",
                "deleted_at": null,
                "linkable": {
                    "name": "Spotify",
                    "url": "https://open.spotify.com/track/6dGnYIeXmHdcikdzNNDMm2"
                }
            },
            {
                "id": 2,
                "created_at": "2022-07-25T14:32:23.000000Z",
                "updated_at": "2022-07-25T14:32:23.000000Z",
                "deleted_at": null,
                "linkable": {
                    "name": "SoundCloud",
                    "url": "https://soundcloud.com/mettamusicman/here-comes-the-sun-the-beatles"
                }
            }
        ]
    },
    {
        "id": 3,
        "linkable_type": "shows",
        "created_at": "2022-07-25T14:32:27.000000Z",
        "updated_at": "2022-07-25T14:32:27.000000Z",
        "deleted_at": null,
        "linkable": {
            "title": "Shows"
        },
        "sublinks": [
            {
                "id": 3,
                "created_at": "2022-07-25T14:32:27.000000Z",
                "updated_at": "2022-07-25T14:32:27.000000Z",
                "deleted_at": null,
                "linkable": {
                    "name": "Liam Gallagher",
                    "url": "https://premier.ticketek.com.au/shows/show.aspx?sh=LIAMGALL22&irclickid=2Y0VF20dHxyNTMdTP306r0szUkD3DxwZPxlpws0&irgwc=1",
                    "status": "on-sale",
                    "date": "2022-07-27",
                    "venue": "John Cain Arena, Melbourne"
                }
            },
            {
                "id": 4,
                "created_at": "2022-07-25T14:32:27.000000Z",
                "updated_at": "2022-07-25T14:32:27.000000Z",
                "deleted_at": null,
                "linkable": {
                    "name": "Florence + The Machine",
                    "url": "https://premier.ticketek.com.au/events/FLORENCE23/venues/SSD/performances/ESSD2023677FM/tickets",
                    "status": "not-on-sale",
                    "date": "2023-03-13",
                    "venue": "Qudos Bank Arena, Sydney"
                }
            },
            {
                "id": 5,
                "created_at": "2022-07-25T14:32:27.000000Z",
                "updated_at": "2022-07-25T14:32:27.000000Z",
                "deleted_at": null,
                "linkable": {
                    "name": "Simone Young & Hilary Hahn",
                    "url": "https://www.sydneysymphony.com/concerts/simone-young-hilary-hahn",
                    "status": "sold-out",
                    "date": "2022-07-28",
                    "venue": "Sydney Opera House, Sydney"
                }
            }
        ]
    }
]
```

**Errors**:
- 404 - user not found
```
{
    "msg": "User not found"
}
```
- 422 - missing input
```
{
    "message": "The given data was invalid.",
    "errors": {
        "userUuid": [
            "The user uuid field is required."
        ]
    }
}
```
- 422 - invalid input
```
{
    "message": "The given data was invalid.",
    "errors": {
        "userUuid": [
            "The user uuid must be a valid UUID."
        ],
        "sortBy": [
            "The selected sort by is invalid."
        ]
    }
}
```

### Store a classic link (POST)

**Example request**:
```
POST /api/v1/links
{
    "userUuid": "d8f56fb5-f098-432a-84a9-71b7750fa187",
    "type": "classic",
    "title": "Classic",
    "url": "https://www.google.com"
}
```

**Example 200 response**:
Return JSON of the newly created classic link (similar to the request payload above).

**Example errors**:
- 404 - user not found
```
{
    "msg": "User not found"
}
```
- 422 - missing input
```
{
    "message": "The given data was invalid.",
    "errors": {
        "userUuid": [
            "The user uuid field is required."
        ],
        "type": [
            "The type field is required."
        ],
        "title": [
            "The title field is required."
        ],
        "url": [
            "The url field is required."
        ]
    }
}
```
- 422 - invalid input
```
{
    "message": "The given data was invalid.",
    "errors": {
        "userUuid": [
            "The user uuid must be a valid UUID."
        ],
        "type": [
            "The selected type is invalid."
        ],
        "title": [
            "The title must be a string."
        ],
        "url": [
            "The url must be a valid URL."
        ]
    }
}
```

### Store a music link (POST)

**Example request**:
```
POST /api/v1/links
{
    "userUuid": "d8f56fb5-f098-432a-84a9-71b7750fa187",
    "type": "music",
    "sublinks": [
        "{\"name\": \"Spotify\", \"url\": \"https://open.spotify.com/track/6dGnYIeXmHdcikdzNNDMm2\"}",
        "{\"name\": \"SoundCloud\", \"url\": \"https://soundcloud.com/mettamusicman/here-comes-the-sun-the-beatles\"}"
    ]
}
```

**Example 200 response**:
Return JSON of the newly created classic link (similar to the request payload above).

**Example errors**:
- 404 - user not found
```
{
    "msg": "User not found"
}
```
- 422 - missing input
```
{
    "message": "The given data was invalid.",
    "errors": {
        "userUuid": [
            "The user uuid field is required."
        ],
        "type": [
            "The type field is required."
        ],
        "sublinks.0": [
            "The name field is required."
        ],
        "sublinks.1": [
            "The url field is required."
        ]
    }
}
```
- 422 - invalid input
```
{
    "message": "The given data was invalid.",
    "errors": {
        "userUuid": [
            "The user uuid must be a valid UUID."
        ],
        "type": [
            "The selected type is invalid."
        ],
        "sublinks.0": [
            "The name must be a string."
        ],
        "sublinks.1": [
            "The url must be a valid URL."
        ]
    }
}
```

### Store a shows link (POST)

**Example request**:
```
POST /api/v1/links
{
    "userUuid": "d8f56fb5-f098-432a-84a9-71b7750fa187",
    "type": "shows",
    "sublinks": [
        "{\"name\": \"Liam Gallagher\", \"url\": \"https://premier.ticketek.com.au/shows/show.aspx?sh=LIAMGALL22&irclickid=2Y0VF20dHxyNTMdTP306r0szUkD3DxwZPxlpws0&irgwc=1\", \"status\": \"on-sale\", \"date\": \"2022-07-27\", \"venue\": \"John Cain Arena, Melbourne\"}",
        "{\"name\": \"Florence + The Machine\", \"url\": \"https://premier.ticketek.com.au/events/FLORENCE23/venues/SSD/performances/ESSD2023677FM/tickets\", \"status\": \"not-on-sale\", \"date\": \"2023-03-13\", \"venue\": \"Qudos Bank Arena, Sydney\"}",
        "{\"name\": \"Simone Young & Hilary Hahn\", \"url\": \"https://www.sydneysymphony.com/concerts/simone-young-hilary-hahn\", \"status\": \"sold-out\", \"date\": \"2022-07-28\", \"venue\": \"Sydney Opera House, Sydney\"}",
    ]
}
```

**Example 200 response**:
Return JSON of the newly created classic link (similar to the request payload above).

**Example errors**:
- 404 - user not found
```
{
    "msg": "User not found"
}
```
- 422 - missing input
```
{
    "message": "The given data was invalid.",
    "errors": {
        "userUuid": [
            "The user uuid field is required."
        ],
        "type": [
            "The type field is required."
        ],
        "sublinks.0": [
            "The name field is required."
        ],
        "sublinks.1": [
            "The url field is required."
        ],
        "sublinks.2": [
            "The status field is required."
        ]
    }
}
```
- 422 - invalid input
```
{
    "message": "The given data was invalid.",
    "errors": {
        "userUuid": [
            "The user uuid must be a valid UUID."
        ],
        "type": [
            "The selected type is invalid."
        ],
        "sublinks.0": [
            "The name must be a string."
        ],
        "sublinks.1": [
            "The url must be a valid URL."
        ],
        "sublinks.2": [
            "The selected status is invalid."
        ]
    }
}
```
