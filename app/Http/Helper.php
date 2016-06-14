<?php 

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
                    $data['table'] = $table = get_string_between($query,"CREATE TABLE `","` (");
                    $response['tables'][] = $data['table'];
                    $response['tables_create'][$table] = $query;
                }elseif (substr($query, 0,11)=='INSERT INTO') {
                    $data['type'] = 'INSERT_INTO';
                    $data['table'] = $table = get_string_between($query,"INSERT INTO `","` (");
                    $response['tables_insert'][$table][] = $query;
                }elseif (substr($query, 0, 11)=='ALTER TABLE') {
                    $data['type'] = 'ALTER_TABLE';
                    $data['table'] = $table = get_string_between($query,"ALTER TABLE `","`");
                    $response['tables_alter'][$table][] = $query;
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

function super_unique($array,$key)
{
   $temp_array = array();
   foreach ($array as &$v) {
       if (!isset($temp_array[$v[$key]]))
       $temp_array[$v[$key]] =& $v;
   }
   $array = array_values($temp_array);
   return $array;
}
function time_elapsed_string($datetime='',$datetimeto, $full = false) {
    $now = new DateTime;
    if($datetime!='') {
        $now = new DateTime($datetime);
    }
    $ago = new DateTime($datetimeto);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ' : 'just now';
}
function human_filesize($bytes, $decimals = 2) {
  $sz = 'BKMGTP';
  $factor = floor((strlen($bytes) - 1) / 3);
  return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}

function get_size($url) {
    $head = array_change_key_case(get_headers($url, TRUE));
    return $head['content-length'];
}

function send_email($to,$subject,$data,$from='',$template='') {
     $setting = DB::table('cms_settings')->where('name','like','smtp%')->get();
     $set = array();
     foreach($setting as $s) {
        $set[$s->name] = $s->content;
     }

    \Config::set('mail.host',$set['smtp_host']);
    \Config::set('mail.port',$set['smtp_port']);
    \Config::set('mail.username',$set['smtp_username']);
    \Config::set('mail.password',$set['smtp_password']);

    $template = ($template)?:"emails.blank";
    $from = ($from)?:$set['smtp_username'];
    \Mail::send($template,$data,function($message) use ($to,$subject,$from) {
        $message->to($to);
        $message->from($from);
        $message->subject($subject);
    });
}