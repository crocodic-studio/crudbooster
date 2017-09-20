<?php
if ($ro['options']['latitude'] && $ro['options']['longitude']) {
    $latitude_name = $ro['options']['latitude'];
    $longitude_name = $ro['options']['longitude'];
    $this->arr[$latitude_name] = Request::get('input-latitude-'.$name);
    $this->arr[$longitude_name] = Request::get('input-longitude-'.$name);
}