# Wallet
## Entities
* User
  * JuridicalPerson
  * NaturalPerson
* Transaction
* Balance
* Money

## How to run
#### Locally
* Docker Compose
  * Requirements:
    - Docker
    - Docker Compose
  * Steps:
  ```sh 
  # Build and turn on containers
  docker-compose up
  
  # SSH to container to execute commands inside of it
  docker exec -it $(docker ps -aqf "name=wallet-laravel_app") bash
  
  ### Inside container

  # Run all migrations
  php artisan migrate

  # Application will be binded to port 8080 on host.
  ```
* Laravel Sail
  * Requirements
    - Docker
    - Docker Compose
    - Composer (PHP dependency manager)
    - PHP(^7.4)
  * Steps:
  ```sh
    # Install Laravel Sail
    composer require laravel/sail
    php artisan sail:install

    # Build and turn on containers
    ./vendor/bin/sail up

    # Run all migrations
    ./vendor/bin/sail artisan migrate

    # Application will be binded to port 80 on host
  ```
#### Production
For production, use the file Dockerfile to build the project image. Note that the production build doesn't include a web server just an image for the application with php 8 fpm installed. The choice and configuration of the web server is entirely up to the administrator.
```sh 
# To build application image
docker build .
```

## Tests

```sh
php artisan test 

# For a specific test suit
php artisan test --testsuite=Unit
# Or
php artisan test --testsuite=Feature
```

## Endpoints
### /transaction
#### Request Body - application/json
| Name        | Type           | Required |
| ----------- | -------------- | -------- |
| payee_id    | integer        | Yes      |
| payer_id    | integer        | Yes      |
| value       | integer        | Yes      |
#### Response
| Name | Type    |
| ---- | --------|
| data | Balance |

### /user/{user_id}/balance
#### Query Parameters
| Name    | Type    | Required |
| ------- | ------- | -------- |
| user_id | integer | Yes      |
#### Response
| Name | Type    |
| ---- | ------- |
| data | Balance |

##### Balance
| Name     | Type    |
| -------- | ------- |
| amount   | integer |
| currency | string  |


## To Do
- Locking User for transaction while another is being made. This will ensure that there will be no concurrency dispute over an User balance.
- Application Error Code. Build a map of Application exception and Error codes.
- Log Events and errors. Maybe add a uuid to the request to be able to follow the request's path throughout the application
- How to run for Vercel 
- Refactoring tests
- Add a deposit endpoint
- Add User authentication and authorization
- Create User subclasses for NaturalPerson and JuridicalPerson
