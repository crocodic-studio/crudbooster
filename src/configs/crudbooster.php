<?php

return [

    /*
     * For security reason we have limit the upload file formats bellow
     */
    'UPLOAD_FILE_EXTENSION_ALLOWED' => ['jpg','jpeg','png','gif','pdf','xls','xlsx','doc','docx','txt','zip','rar','rtf'],

    'UPLOAD_IMAGE_EXTENSION_ALLOWED' => ['jpg','jpeg','png','gif'],

    /*
     * Override Local FileSystem From Storage To Another
     * Default : public_path()
     * It means the upload path will be changed to public path of your laravel
     */
    'LOCAL_FILESYSTEM_PATH'=> public_path(),

    /*
     * You can set the upload path as you wish.
     * {Y} will be replaced with a Year E.g : 2019
     * {m} will be replaced with a Month E.g : 01
     * {d} will be replaced with a Day E.g : 01
     */
    'UPLOAD_PATH_FORMAT'=>'storage/files/{Y}/{m}/{d}',


    /*
     * Google FCM Server Key is used to send a notification via FireBase cloud message
     * If you want to use FCM Helper you should set this setting
     */
    'GOOGLE_FCM_SERVER_KEY'=> null,

    /*
     * To prevent too high of width resolution, you can set the maximum of image width
     * Please keep in mind this config's affect only when you do not set a resize() object
     */
    'DEFAULT_IMAGE_MAX_WIDTH_RES'=>1300,


    /*
     * Data Migration table additional
     * You can define in this array what the tables you want to include in "php artisan crudbooster:data_migration"
     * The default is all cb_ prefix will be include in data migration
     */
    'ADDITIONAL_DATA_MIGRATION'=>['users','migrations']
];