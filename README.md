# CO2-Sensor Service
A sensor service for collecting data and make alert against various levels.

## Requirements
- PHP >= 7.3.12
- symfony >= 4.4.x

## Installation 
Symfony utilizes composer to manage its dependencies. So, before using symfony, make sure you have composer installed on your machine. To download all required packages run a following commands or you can download [Composer](https://getcomposer.org/doc/00-intro.md).
- composer install `OR` COMPOSER_MEMORY_LIMIT=-1 composer install

## Database Setup
Need to set a .env file to make database connection with this setting.
```
DATABASE_URL=mysql://username:password@host:port/database_name
```
After that run these commands, it will help to create database and database scheme to process the sensors activities.
```
To create the database: php bin/console doctrine:database:create
To make migration: php bin/console make:migration
To run migration: php bin/console doctrine:migrations:migrate
```

## Run
```
symfony server:start 
```

## Test
```
./vendor/bin/phpunit
```
