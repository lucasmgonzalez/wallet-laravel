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
    - At project's root directory run `docker-compose up`
    - On first build, inside the app container on working directory, run `php artisan migrate` to build all tables from the database.
    - Use `localhost:8080` as the host.
* Laravel Sail
  * Requirements
    - Install composer dev dependencies or require sail with `composer require laravel/sail`
  * Steps:
    - If this is your first run, run `php artisan sail:install` at the project's root directory
    - At project's root directory run `./vendor/bin/sail up`
    - On first run, after executing `./vendor/bin/sail up`, run `./vendor/bin/sail artisan migrate`



# Falta
- Refatorar Balance::buildFromUser
- Refatorar TransactionController -  Remover lógica de validação e criação de transação
- Testes Testes Testes
- Documentação
  - Explicar como subir projeto pra produção ?
    - Vercel?
    - Dockerfile?
- Endpoint de depósito?
- Código de erro da aplicação?
