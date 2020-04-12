<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 4/12/2020
 * Time: 4:45 PM
 */

namespace crocodicstudio\crudbooster\helpers;


class addActionButton
{
    private $label;
    private $url;
    private $icon;
    private $color;
    private $target = "_self";
    private $show_if = null;
    private $hide_when = null;
    private $confirmation = false;
    private $confirmation_title;
    private $confirmation_text;
    private $confirmation_type;
    private $confirmation_showCancelButton;
    private $confirmation_confirmButtonColor;
    private $confirmation_confirmButtonText;
    private $confirmation_cancelButtonText;
    private $confirmation_closeOnConfirm;

    public function __construct($label, $url, $icon, $color)
    {
        $this->label = $label;
        $this->url = $url;
        $this->icon = $icon;
        $this->color = $color;
    }

    public function toArray() {
        $result = [];
        foreach ($this as $key=>$val) {
            $result[$key] = $val;
        }
        return $result;
    }

    /**
     * @param callable $condition
     */
    public function showIf(callable $condition) {
        $this->show_if = $condition;
    }

    /**
     * @param callable $condition
     */
    public function hideWhen(callable $condition) {
        $this->hide_when = $condition;
    }

    public function targetSelf() {
        $this->target = "_self";
    }

    public function targetBlank() {
        $this->target = "_blank";
    }

    public function confirmation($title, $text, $type = "info") {
        $this->confirmation = true;
        $this->confirmation_title = $title;
        $this->confirmation_text = $text;
        $this->confirmation_type = $type;
    }

    public function confirmShowCancel() {
        $this->confirmation_showCancelButton = true;
    }

    public function confirmButton($text, $hex_color) {
        $this->confirmation_confirmButtonColor = $hex_color;
        $this->confirmation_confirmButtonText = $text;
    }

    public function confirmCancelText($text) {
        $this->confirmation_cancelButtonText = $text;
    }

    public function confirmCloseOnConfirm() {
        $this->confirmation_closeOnConfirm = true;
    }
}