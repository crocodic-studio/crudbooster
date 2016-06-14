<?php 
require "helper.php";

$type = $_GET['type'];
$post = $_POST;
$conf = load_env();

if($type=='save_db') {
	$con = mysqli_connect($post['DB_HOST'],$post['DB_USERNAME'],$post['DB_PASSWORD']);

	// Check connection
	if (mysqli_connect_errno())
	{
		echo 0;
		exit;
	}	
	//CREATE DATABASE IF NOT EXISTS DBName;
	@mysqli_query($con,"CREATE DATABASE IF NOT EXISTS `$post[DB_DATABASE]`");
	
    $conf['DB_HOST'] = $post['DB_HOST'];
    $conf['DB_DATABASE'] = $post['DB_DATABASE'];
    $conf['DB_USERNAME'] = $post['DB_USERNAME'];
    $conf['DB_PASSWORD'] = $post['DB_PASSWORD'];

    $env = '';
    foreach($conf as $k=>$v) {
    	$env .= "$k=$v\n";
    }

    file_put_contents("../.env", $env);
    echo 1;
}elseif ($type=='check_table') {

	$con = mysqli_connect($conf['DB_HOST'],$conf['DB_USERNAME'],$conf['DB_PASSWORD'],$conf['DB_DATABASE']);

	// Check connection
	if (mysqli_connect_errno())
	  {
	  	die("Failed to connect to MySQL: " . mysqli_connect_error());
	  }	

	if(mysqli_num_rows(mysqli_query($con,"SHOW TABLES LIKE '".$post['table']."'"))==1) {
		echo 1;
	}else{
		echo 0;
	}
}