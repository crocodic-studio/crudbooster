<?php

return [

    'ADMIN_PATH' => 'admin',

    /*
        To Allowed Specific User Agent Only
        E.g : ['Android','OkHttp','Mozilla','Mac']
    */
    'API_USER_AGENT_ALLOWED' => [],

    'USER_TABLE' => 'cms_users',

    'IMAGE_FIELDS_CANDIDATE' => 'image,picture,photo,photos,foto,gambar,thumbnail',

    'PASSWORD_FIELDS_CANDIDATE' => 'password,pass,pwd,passwrd,sandi,pin',

    'DATE_FIELDS_CANDIDATE' => 'date,tanggal,tgl,created_at,updated_at,deleted_at',

    'EMAIL_FIELDS_CANDIDATE' => 'email,mail,email_address',

    'PHONE_FIELDS_CANDIDATE' => 'phone,phonenumber,phone_number,telp,hp,no_hp,no_telp',

    'NAME_FIELDS_CANDIDATE' => 'name,nama,person_name,person,fullname,full_name,nickname,nick,nick_name,title,judul,content',

    'URL_FIELDS_CANDIDATE' => 'url,link',

    'UPLOAD_TYPES' => 'jpg,png,jpeg,gif,bmp,pdf,xls,xlsx,doc,docx,txt,zip,rar,7z',

    'DEFAULT_THUMBNAIL_WIDTH' => 0,

    'DEFEAULT_UPLOAD_MAX_SIZE' => 1000, //in KB

    'IMAGE_EXTENSIONS' => 'jpg,png,jpeg,gif,bmp',

    'MAIN_DB_DATABASE' => env('DB_DATABASE'), //Very useful if you use config:cache

    'MULTIPLE_DATABASE_MODULE' => [],

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