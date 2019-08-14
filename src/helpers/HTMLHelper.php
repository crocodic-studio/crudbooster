<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 8/14/2019
 * Time: 1:19 PM
 */

namespace crocodicstudio\crudbooster\helpers;


class HTMLHelper
{

    /**
     * @param $label
     * @param $name
     * @param $value
     * @param array $options
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function makeFileUpload($label, $name, $value = null, $required = true, array $options = []) {
        $data = [];
        $data['value'] = $value;
        $data['name'] = $name;
        $data['label'] = $label;
        $data['required'] = $required;
        $data['encrypt'] = isset($options['encrypt'])?$options['encrypt']:true;
        return view("crudbooster::html_helper.file_uploader.index", $data);
    }

    /**
     * @param $label
     * @param $name
     * @param $value
     * @param array $options
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function makeImageUpload($label, $name, $value = null, $required = true, array $options = []) {
        $data = [];
        $data['value'] = $value;
        $data['name'] = $name;
        $data['label'] = $label;
        $data['required'] = $required;
        $data['encrypt'] = isset($options['encrypt'])?$options['encrypt']:true;
        @$data['resizeWidth'] = $options['resize_width'];
        @$data['resizeHeight'] = $options['resize_height'];
        return view("crudbooster::html_helper.image_uploader.index", $data);
    }

}