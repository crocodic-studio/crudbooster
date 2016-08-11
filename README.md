# CRUDBOOSTER - Laravel CRUD Generator
[![AUR](https://img.shields.io/aur/license/yaourt.svg?maxAge=2592000?style=flat-square)]()

> Laravel CRUD Generator, Make a Web Application Just In Minutes, Even With Less Code and fewer Steps !

[<img src="http://crudbooster.com/CrudBooster_Banner.png"/>](http://crudbooster.com)

CRUDBooster is Laravel Framework that modified to bring a lot of features to develope a web application so simply. One of main feature is Smart CRUD Generator, so CRUDBooster will be create a module automatically included Create, Read, Update, Delete standard function. And CRUDBooster is not usual CRUD Generator,you will get a lot of new experience with a new concept. CRUD Booster is dedicated to those who already understand the basic laravel. We do not recommend for those of you who have never used laravel altogether.

## System Requirement
Currently Based on Laravel 5.0
- PHP >= 5.4, PHP < 7
- Mcrypt PHP Extension
- OpenSSL PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension

## Installation
Please following these instructions for fresh installation : 
```
1. Download from github
2. Create folder in your htdocs, and extract
3. Create an empty database at your PHPMYADMIN
4. Setting database configuration at file .env.example and save as .env
   - ADMIN_PATH=admin << you can set custom admin path here just leave it to default /admin 
5. Open Command Prompt at your project, run "php artisan key:generate"
6. Run "composer install"
7. Run "php artisan migrate --seed"
8. Run "php artisan serve"
9. Try go to "http://localhost:8000/admin/" or  "http://localhost:8000/your_admin_path/"
10. Login with default login "u : admin@crudbooster.com, p : 123456"
```

## DOCUMENTATION
The complete documentation can be found at : [http://crudbooster.com/page/documentation](http://crudbooster.com/page/documentation)

## SUPPORT AND CONTRIBUTION
All of issues and new feature request, please create an issue or pull request at GitHub, please do not send an email or Private Message to us.

## UPDATE
Please stay to check, if you have fork CRUDBooster, please always update.

## CREDITS
1. Laravel Export HTML to Excel by [Maatwebsite](https://github.com/Maatwebsite/Laravel-Excel)
2. Laravel DOM PDF by [Barryvdh](https://github.com/barryvdh/laravel-dompdf)
3. Admin Theme by [AdminLTE Almsaeed Studio](https://almsaeedstudio.com/preview)
4. Laravel Framework by [Taylor Otwell](https://github.com/laravel/laravel)
