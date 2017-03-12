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
- Open the terminal, navigate to your project directory.
```php
$ composer require crocodicstudio/crudbooster=5.3.2.*
```
- Add the following class, to "providers" array in the file **config/app.php**
```php
crocodicstudio\crudbooster\CRUDBoosterServiceProvider::class,
```
- Run the following command at the terminal
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

## Documentation
- [English Documentation](en/index.md)
