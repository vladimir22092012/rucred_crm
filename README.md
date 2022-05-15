# Rucred Project

## Install and run
- Clone repository: ```git clone git@bitbucket.org:rukred/rucred-crm.git```
- Copy .env.example file into .env
- Run ```composer install``` and ```yarn install```
- Add this line to your .bashrc file(for Linux only): ```alias sail='bash vendor/bin/sail'```
- Start Sail with command: ```sail up -d```
- Run ```sail artisan key:generate``` for application key generation
- Make migrations and seeds for the central app: ```sail artisan migrate:fresh```, ```sail artisan db:seed```
- Make migrations and seeds for tenants: ```sail artisan tenants:migrate-fresh```, ```sail artisan tenants:seed```
- Generate translations with [Matice](https://github.com/GENL/matice) ```sail artisan matice:generate```(Linux)
- Start Yarn dev server with hot reload: ```yarn hot```
- Add three local domains to your machine hosts file:
  ```127.0.0.1 rucred.test``` - central app
  
  ```127.0.0.1 api.rucred.test``` - client api
  
  ```127.0.0.1 client.rucred.test``` - client frontend

# Install project for frontend development with OpenServer
- Download and install [OpenServer](https://ospanel.io/)
- Clone repository: ```git clone git@bitbucket.org:weconf/weconf.git``` into www folder of OpenServer installation directory
- Copy .env.openserver file into .env
- Setup OpenServer for use PHP 8.1 and MySQL 8 with Redis server for caching
- Run ```composer install``` from OpenServer console and ```yarn install``` from default system console
- Run ```yarn hot``` from default system console for start frontend development process
- Create PostgreSQL database, named ```rucred```
- Run ```php artisan key:generate``` for application key generation
- Make migrations and seeds for the central app: ```php artisan migrate:fresh```, ```php artisan db:seed```
- [Change public path for rucred domain](http://joxi.ru/82QW95GIwkkzKr)
- Add three local domains to your machine hosts file:
  ```127.0.0.1 rucred.test``` - central app

  ```127.0.0.1 api.rucred.test``` - client api

  ```127.0.0.1 client.rucred.test``` - client frontend

## Documentation
[Laravel](https://laravel.com/docs/8.x)

[Laravel PG extensions](https://github.com/umbrellio/laravel-pg-extensions)

[JetStream](https://jetstream.laravel.com)

[Inertia](https://inertiajs.com)

[Tenancy](https://tenancyforlaravel.com/docs)

[Laravel Wallet](https://bavix.github.io/laravel-wallet)

## Laravel Best Practices
[Laravel Best Practices](https://github.com/alexeymezenin/laravel-best-practices)

[Spatie Guidelines](https://spatie.be/guidelines)

[Laravel Tips](https://github.com/LaravelDaily/laravel-tips)

Our migrations [anonymous](https://laravel-news.com/laravel-anonymous-migrations).

## Code Style Guidelines
[Frontend (CSS)](https://github.com/meetjet/css)
[Frontend (JS)](https://github.com/meetjet/javascript)

We use Prettier for auto-format code for Backend and Frontend parts

## Commit Message Guidelines

`TASK-ID action: action description`

Examples:

`WC-1 install: commit action`

`WC-35689 fix: commit action`

`WC-35 change: commit action`

## Project Helpers

### PHP
```project('key')``` - get tenant property by key(string), returns null or property value.

## FAQ

### If you don't see images from storage:
```
#first remove old storage link
rm public/storage 
ln -s ../storage/app/public public/storage
```
