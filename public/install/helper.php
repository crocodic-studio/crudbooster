<?php 

function load_env() {
	$env = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'.env';
    $env = array_filter(file($env));
    $conf = array();
    foreach($env as $e) {
        $e = explode('=',$e);
        $k = $e[0];
        @$v = $e[1];
        $conf[$k] = trim($v);
    }
    $conf = array_filter($conf);
    return $conf;
}

function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

function SplitSQL($file, $delimiter = ';')
{
    set_time_limit(0);

    $result = array();

    $response = array();

    $file = fopen($file, 'r');

    $query = array();

    while (feof($file) === false)
    {
        $query[] = fgets($file);

        if (preg_match('~' . preg_quote($delimiter, '~') . '\s*$~iS', end($query)) === 1)
        {
            $query = trim(implode('', $query));

            if(substr($query, 0, 2)!="/*") {

	            $data = array(); 

	            if(substr($query, 0,12)=='CREATE TABLE') {
	            	$data['type'] = 'CREATE_TABLE';            	
	            	$data['table'] = get_string_between($query,"CREATE TABLE `","` (");
	            	$response['tables'][] = $data['table'];
	            }elseif (substr($query, 0,11)=='INSERT INTO') {
	            	$data['type'] = 'INSERT_INTO';
	            	$data['table'] = get_string_between($query,"INSERT INTO `","` (");
	            }elseif (substr($query, 0, 11)=='ALTER TABLE') {
	            	$data['type'] = 'ALTER_TABLE';
	            	$data['table'] = get_string_between($query,"ALTER TABLE `","`");
	            }

	            $data['query'] = $query;

	            $result[] = $data;
            }

        }

        if (is_string($query) === true)
        {
            $query = array();
        }
    }
    fclose($file);

    $response['result'] = $result;

    return $response;
}