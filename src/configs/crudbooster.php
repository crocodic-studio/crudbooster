<?php

return [

    /*
     * You can customize the main path admin url
     */
    'ADMIN_PATH' => 'admin',

    /*
     * You can customize the admin login url
     */
    'ADMIN_LOGIN_PATH'=> 'login',

    /*
     * To customize the dashboard controller, you can set the value bellow
     */
    'ADMIN_DASHBOARD_CONTROLLER'=> null,

    /*
     * Some setting use this dummy photo setting, you can edit the dummy photo url that you want
     */
    'DUMMY_PHOTO' => 'cb_asset/avatar.jpg',

    /*
     * The default login form use this bellow view path, but you can customize the view set to your own
     */
    'LOGIN_FORM_VIEW'    => 'crudbooster::login',

    /*
     * For security reason we have limit the upload file formats bellow
     */
    'UPLOAD_FILE_EXTENSION_ALLOWED' => ['jpg','jpeg','png','gif','pdf','xls','xlsx','doc','docx','txt','zip','rar','rtf'],

    'UPLOAD_IMAGE_EXTENSION_ALLOWED' => ['jpg','jpeg','png','gif'],

    /*
     * Google FCM Server Key is used to send a notification via FireBase cloud message
     * If you want to use FCM Helper you should set this setting
     */
    'GOOGLE_FCM_SERVER_KEY'=> null,


    /*
     * Credential For Developer
     */
    'DEV_USERNAME'  => 'developer',

    'DEV_PASSWORD'  => 'developer',

    'DEV_PATH'      => 'developer',

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