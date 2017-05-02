<?php

return [
	/**
	 * Config the admin path
	 * 
	 * @var string
	 */	
	'admin_path'                =>'admin',	

	/**
	 * Config method for profile view
	 * 
	 * @var string
	 */
	'profile_get_method'		=> 'CBUsers@getProfile',

	/** 
	 * Config method for profile save
	 * 
	 * @var string
	 */
	'profile_save_method'		=> 'CBUsers@postProfile',

	
	'image_fields_candidate'    =>'image,picture,photo,photos,foto,gambar,thumbnail',
	
	'password_fields_candidate' =>'password,pass,pwd,passwrd,sandi,pin',
	
	'date_fields_candidate'     =>'date,tanggal,tgl,created_at,updated_at,deleted_at',

	'email_fields_candidate'	=>'email,mail,email_address',

	'phone_fields_candidate'	=>'phone,phonenumber,phone_number,telp,hp,no_hp,no_telp',

	'name_fields_candidate'		=>'name,nama,person_name,person,fullname,full_name,nickname,nick,nick_name,title,judul,content',

	'url_fields_candidate'		=>'url,link',
	
	'upload_types'              =>'jpg,png,jpeg,gif,bmp,pdf,xls,xlsx,doc,docx,txt,zip,rar,7z',

	'default_thumbnail_width' 	=>300,

	'image_extension'			=>'jpg,png,jpeg,gif,bmp',

	/**
	 * To set the main database, get db name while config:cache enabled
	 * 
	 * @var string
	 */ 
	'main_database' 			=>env('DB_DATABASE'),

	/**
	 * Array of database if you have more than one databases
	 * 
	 * @var array
	 */
	'multiple_database' 	=>[],

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

	'admin_layout'	=> '',

	/* 
	* NOTE : 
	* As a default, CRUDBooster making a cache for the configuration with command `php artisan config:cache`. It means any changes that you've made,
	* would not make an effect, until you run `php artisan config:cache` again.
	*
	*/
];