<?php

namespace crocodicstudio\crudbooster\controllers\CBController;

trait CbFormLoader
{
    public $data_inputan;

    public $hide_form = [];

    public $button_cancel = true;

    public $button_save = true;

    public $button_addmore = true;

    protected function cbFormLoader()
    {
        $this->data_inputan = $this->form;
        $this->data['forms'] = $this->data_inputan;
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