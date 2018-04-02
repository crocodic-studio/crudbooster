<?php

namespace crocodicstudio\crudbooster\controllers\CBController;

trait CbFormLoader
{

    public $hide_form = [];

    public $button_cancel = true;

    public $button_save = true;

    public $button_add = true;

    public $button_addmore = true;

    public $show_addaction = true;

    protected function cbFormLoader()
    {
        $this->data['button_add'] = $this->button_add;
        $this->data['button_addmore'] = $this->button_addmore;
        $this->data['button_cancel'] = $this->button_cancel;
        $this->data['button_save'] = $this->button_save;
    }

    private function checkHideForm()
    {
        if (! count($this->hide_form)) {
            return null;
        }
        foreach ($this->form as $i => $f) {
            if (in_array($f['name'], $this->hide_form)) {
                unset($this->form[$i]);
            }
        }
    }
}