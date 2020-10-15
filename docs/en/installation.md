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
- Laravel 5.5 - 6.0
- Php 7.2 or higher and the extensions:
  - Mcrypt
  - OpenSSL
  - Mbstring
  - Tokenizer
  - FileInfo

## Installation
1. Open the terminal, navigate to your laravel project directory.
```php
$ composer require crocodicstudio/crudbooster=5.5.*
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

## Update
Do not update your project directly, please making a backup before updating the CRUDBooster.
- Open the terminal, navigate to your project directory.
```php
$ composer require crocodicstudio/crudbooster=^5.5
```
- Run the following command at the terminal
```php
$ php artisan crudbooster:update
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
