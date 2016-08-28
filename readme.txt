| --------------------------------------------------------------------------------------------
| Installation Guide
| --------------------------------------------------------------------------------------------
| Package Name 	: crudbooster
| Description 	: CRUDBooster is integrated backend & crud generator for Laravel
| Package Path 	: crocodicstudio/crudbooster
| Homepage      : http://crudbooster.com
|
| --------------------------------------------------------------------------------------------
| 1. Install via Composer
| --------------------------------------------------------------------------------------------
| composer require crocodicstudio/crudbooster
| 
| --------------------------------------------------------------------------------------------
| 2. Add Dependeny Packages to Service Provider (config/app.php)
| --------------------------------------------------------------------------------------------
| * Laravel DOMPDF
| Barryvdh\DomPDF\ServiceProvider::class,
| ** Facade : 
| 'PDF' => Barryvdh\DomPDF\Facade::class,
| 
| * Maatwebsite Excel
| Collective\Bus\BusServiceProvider::class,
| Maatwebsite\Excel\ExcelServiceProvider::class,
| ** Facade : 
| 'Excel' => Maatwebsite\Excel\Facades\Excel::class,
| 
| * UniSharp Laravel File Manager
| Unisharp\Laravelfilemanager\LaravelFilemanagerServiceProvider::class,
| Intervention\Image\ImageServiceProvider::class,
| ** Facade : 
| 'Image' => Intervention\Image\Facades\Image::class,
|
| --------------------------------------------------------------------------------------------
| 3. Add CRUDBooster Package to Service Provider (config/app.php)
| --------------------------------------------------------------------------------------------
| crocodicstudio\crudbooster\CRUDBoosterServiceProvider::class,
|
| --------------------------------------------------------------------------------------------
| 4. Publish All Configuration and Files
| --------------------------------------------------------------------------------------------
| * For first time use or refresh the project : 
| php artisan vendor:publish --force
| 
| * For publishing CRUDBooster files only : 
| php artisan vendor:publish --provider="crocodicstudio\crudbooster\CRUDBoosterServiceProvider" --force
| 
| --------------------------------------------------------------------------------------------
| 5. Migration & Seed the CRUDBooster Core Database
| --------------------------------------------------------------------------------------------
| ** For first time use & update : 
| php artisan migrate --seed --force
| 
| * For refresh the project : 
| php artisan migrate:refresh --seed --force
|
| * Everytime you update the CRUDBooster, don't forget to migrating again and seeding again
| --------------------------------------------------------------------------------------------
