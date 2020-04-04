<?php
namespace crocodicstudio\crudbooster\models;

use Crocodicstudio\Cbmodel\Core\Model;

class CmsUsers extends Model
{
    public $connection = "mysql";
    public $table = "cms_users";
    public $primary_key = "id";

    public $id;
    public $created_at;
    public $updated_at;
    public $name;
    public $photo;
    public $email;
    public $password;
    public $id_cms_privileges;

    public function privileges() {
        
    }
}