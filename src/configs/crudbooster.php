<?php

return [
    /*
     * To customize the dashboard controller, you can set the value bellow
     */
    'ADMIN_DASHBOARD_CONTROLLER'=> null,

    /*
     * Some setting use this dummy photo setting, you can edit the dummy photo url that you want
     */
    'DUMMY_PHOTO' => 'cb_asset/adminlte/dist/img/user8-128x128.jpg',

    /*
     * Default limit table data
     */
    'LIMIT_TABLE_DATA'=>10,

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
    'ADDITIONAL_DATA_MIGRATION'=>['users','migrations'],


    /*
    * Layout for the Admin LTE backend theme
    *
    * Fixed:               use the class .fixed to get a fixed header and sidebar.
    *                      This makes scrolling affect the content only and put the sidebar and header in a fixed position.
    *
    * Collapsed Sidebar:   use the class .sidebar-collapse to have a collapsed sidebar upon loading.
    *                      Use this if you want the sidebar to be hidden by default.
    *
    * Boxed Layout:        use the class .layout-boxed to get a boxed layout that stretches only to 1250px.
    *                      Provides spaces on both sides of the screen, if the screen is big enough.
    *
    * Top Navigation:      use the class .layout-top-nav to remove the sidebar and have your links at the top navbar.
    *                      Makes the sidebar hover the content when expanded.
    *
    * Sidebar Mini:        Shows the only the icons of the sidebar items when collapsed. Sidebar will not fully collapse.
    *
    * Available options:
    *
    * fixed
    * sidebar-collapse
    * layout-boxed
    * layout-top-nav
    * sidebar-mini
    *
    * Note: you cannot use both layout-boxed and fixed at the same time. Anything else can be mixed together.
    */

    'ADMIN_LAYOUT' => '',

    /*
    * NOTE :
    * Make sure yo clear your config cache by using command : php artisan config:clear
    */
];