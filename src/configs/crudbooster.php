<?php

return [
	
	'ADMIN_PATH'                =>'admin',

	'USER_TABLE'				=>'cms_users',
	
	'IMAGE_FIELDS_CANDIDATE'    =>'image,picture,photo,photos,foto,gambar,thumbnail',
	
	'PASSWORD_FIELDS_CANDIDATE' =>'password,pass,pwd,passwrd,sandi,pin',
	
	'DATE_FIELDS_CANDIDATE'     =>'date,tanggal,tgl,created_at,updated_at,deleted_at',

	'EMAIL_FIELDS_CANDIDATE'	=>'email,mail,email_address',

	'PHONE_FIELDS_CANDIDATE'	=>'phone,phonenumber,phone_number,telp,hp,no_hp,no_telp',

	'NAME_FIELDS_CANDIDATE'		=>'name,nama,person_name,person,fullname,full_name,nickname,nick,nick_name,title,judul,content',

	'URL_FIELDS_CANDIDATE'		=>'url,link',
	
	'UPLOAD_TYPES'              =>'jpg,png,jpeg,gif,bmp,pdf,xls,xlsx,doc,docx,txt,zip,rar,7z',

	'DEFAULT_THUMBNAIL_WIDTH' 	=>300,

	'IMAGE_EXTENSIONS'			=>'jpg,png,jpeg,gif,bmp',

	'MAIN_DB_DATABASE' 			=>env('DB_DATABASE'), //Very useful if you use config:cache

	'MULTIPLE_DATABASE_MODULE' 	=>[],

	/* 
	* NOTE : 
	* As a default, CRUDBooster making a cache for the configuration with command `php artisan config:cache`. It means any changes that you've made,
	* would not make an effect, until you run `php artisan config:cache` again.
	*
	*/
];