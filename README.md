# CRUDBOOSTER - Laravel CRUD Generator
[![Latest Stable Version](https://poser.pugx.org/crocodicstudio/crudbooster/v/stable)](https://packagist.org/packages/crocodicstudio/crudbooster)
[![Total Downloads](https://poser.pugx.org/crocodicstudio/crudbooster/downloads)](https://packagist.org/packages/crocodicstudio/crudbooster)
[![Latest Unstable Version](https://poser.pugx.org/crocodicstudio/crudbooster/v/unstable)](https://packagist.org/packages/crocodicstudio/crudbooster)
[![License](https://poser.pugx.org/crocodicstudio/crudbooster/license)](https://packagist.org/packages/crocodicstudio/crudbooster)
[![Monthly Downloads](https://poser.pugx.org/crocodicstudio/crudbooster/d/monthly)](https://packagist.org/packages/crocodicstudio/crudbooster)
[![Daily Downloads](https://poser.pugx.org/crocodicstudio/crudbooster/d/daily)](https://packagist.org/packages/crocodicstudio/crudbooster)

> Laravel CRUD Generator, Make a Web Application Just In Minutes, Even With Less Code and fewer Steps !

[<img src="http://crudbooster.com/CrudBooster_Banner.png"/>](http://crudbooster.com) 

CRUDBooster is Laravel Framework that modified to bring a lot of features to develop a web application so simply. One of the main features is Smart CRUD Generator, so CRUDBooster will create a module automatically included Create, Read, Update, Delete standard function. And CRUDBooster is not usual CRUD Generator,you will get a lot of new experience with a new concept. CRUD Booster is dedicated to those who already understand the basic laravel. We do not recommend for those of you who have never used laravel altogether.

## System Requirement
- PHP >= 5.6.x, PHP <= 7
- Mcrypt PHP Extension
- OpenSSL PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension

## Installation For Laravel 5.2.x
### 1. Install via composer
```
composer require crocodicstudio/crudbooster
```
### 2. Add Dependencies Package to Service Provider (config/app.php)
```
Barryvdh\DomPDF\ServiceProvider::class,
Collective\Bus\BusServiceProvider::class,
Maatwebsite\Excel\ExcelServiceProvider::class,
Unisharp\Laravelfilemanager\LaravelFilemanagerServiceProvider::class,
Intervention\Image\ImageServiceProvider::class,
crocodicstudio\crudbooster\CRUDBoosterServiceProvider::class,
```
### 3. Add Bellow Facades to (config/app.php)
```
'PDF' => Barryvdh\DomPDF\Facade::class,
'Excel' => Maatwebsite\Excel\Facades\Excel::class,
'Image' => Intervention\Image\Facades\Image::class,
```
### 4. Publish Configuration and Databases
```
php artisan vendor:publish
```
Then  
```
php artisan vendor:publish --force --provider="crocodicstudio\crudbooster\CRUDBoosterServiceProvider"
```
### 5. Migrating and Seeding the Core Database of CRUDBooster
```
php artisan migrate --seed
```
### 6. Testing
Try to login at /admin  
default username : admin@crudbooster.com  
default password : 123456  

## Update Guide
1. <code>composer update crocodicstudio/crudbooster</code>
1. Re-pulbish asset and configuration (Step 4)  
1. Migration & Seeding (Step 5) to make sure any update work properly.


## DOCUMENTATION
The complete documentation can be found at : 
[http://crudbooster.com/page/documentation](http://crudbooster.com/page/documentation)

## SUPPORT AND CONTRIBUTION
All of issues and new feature request, please create an issue at GitHub, please do not send an email or Private Message to us.

## CREDITS
1. Laravel Export HTML to Excel by [Maatwebsite](https://github.com/Maatwebsite/Laravel-Excel)
2. Laravel DOM PDF by [Barryvdh](https://github.com/barryvdh/laravel-dompdf)
3. Admin Theme by [AdminLTE Almsaeed Studio](https://almsaeedstudio.com/preview)
4. Laravel Framework by [Taylor Otwell](https://github.com/laravel/laravel)
