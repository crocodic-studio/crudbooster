<?php

namespace crocodicstudio\crudbooster\Modules\EmailTemplates;

class EmailTemplateForm
{
    public static function makeForm()
    {
        $form = [
            [
                'label' => 'Template Name',
                'name' => 'name',
                'type' => 'text',
                'required' => true,
                'validation' => 'required|min:3|max:255|alpha_spaces',
                'placeholder' => 'You can only enter the letter only',
            ],
            [
                'label' => 'Slug',
                'type' => 'text',
                'name' => 'slug',
                'required' => true,
                'validation' => 'required|unique:cms_email_templates,slug'],
            [
                'label' => 'Subject',
                'name' => 'subject',
                'type' => 'text',
                'required' => true,
                'validation' => 'required|min:3|max:255'],
            [
                'label' => 'Content',
                'name' => 'content',
                'type' => 'wysiwyg',
                'required' => true,
                'validation' => 'required'],
            [
                'label' => 'Description',
                'name' => 'description',
                'type' => 'text',
                'required' => true,
                'validation' => 'required|min:3|max:255'],

            [
                'label' => 'From Name',
                'name' => 'from_name',
                'type' => 'text',
                'required' => false,
                'width' => 'col-sm-6',
                'placeholder' => 'Optional',
            ],
            [
                'label' => 'From Email',
                'name' => 'from_email',
                'type' => 'email',
                'required' => false,
                'validation' => 'email',
                'width' => 'col-sm-6',
                'placeholder' => 'Optional',
            ],

            [
                'label' => 'Cc Email',
                'name' => 'cc_email',
                'type' => 'email',
                'required' => false,
                'validation' => 'email',
                'placeholder' => 'Optional',
            ],
        ];

        return $form;
    }
}
