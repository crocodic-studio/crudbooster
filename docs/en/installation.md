# Welcome To CRUDBooster

CRUDBooster is CRUD Generator for laravel, with the most important features web application development. It's easy, flexible, and powerful.

## System Requirement 
- LARAVEL 5.3.x or higher
- PHP >= 5.6.x
- Mcrypt PHP Extension
- OpenSSL PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension
- FileInfo PHP Extension

## Installation
1. Open the terminal, navigate to your project directory.
```php
$ composer require crocodicstudio/crudbooster=5.3.2.*
```

2. Add the following class, to "providers" array in the file **config/app.php**
```php
crocodicstudio\crudbooster\CRUDBoosterServiceProvider::class,
```

3. Setting the database configuration, open .env file at project root directory
```
DB_DATABASE=**your_db_name**
DB_USERNAME=**your_db_user**
DB_PASSWORD=**password**
```

4. Run the following command at the terminal
```php
$ php artisan crudbooster:install
```

## Update
- Open the terminal, navigate to your project directory.
```php
$ composer require crocodicstudio/crudbooster=5.3.2.*
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
- [Back To Index](./index.md)
