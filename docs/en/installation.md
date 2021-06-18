# Welcome To CRUDBooster

CRUDBooster is CRUD Generator for laravel, with the most important features web application development. It's easy, flexible, and powerful.

## System Requirement and Basic Technical Knowledge
- Web Server as:
  - Apache 2.4.x or higher with rewrite engine on (mod_rewrite)  
  - Nginx 1.11.x or higher
- Database that laravel supports, actually can be:
  - MySQL
  - Postgres
  - SQLite
  - SQL Server
- Composer
- Laravel 6.* / 7.* / 8.*
- Php 7.2 or higher and the extensions:
  - Mcrypt
  - OpenSSL
  - Mbstring
  - Tokenizer
  - FileInfo

## Installation

0. Please make sure you have install laravel project, please follow [https://laravel.com/docs/8.x/installation](https://laravel.com/docs/8.x/installation)

1. Open the terminal, navigate to your laravel project directory.
```php
$ composer require crocodicstudio/crudbooster=5.6.*
```

2. Setting the database configuration, open .env file at project root directory
```
DB_DATABASE=**your_db_name**
DB_USERNAME=**your_db_user**
DB_PASSWORD=**password**
```

3. Run the following command at the terminal
```php
$ php artisan crudbooster:install
```

## Backend URL
```php
/admin/login
```
- default email : admin@crudbooster.com
- default password : 123456

## What's Next
- [How To Create A Module (CRUD)](./how-to-create-module.md)

## Table Of Contents
- [Back To Index](./index.md)
