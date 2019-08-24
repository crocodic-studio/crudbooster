<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 4/24/2019
 * Time: 11:24 PM
 */

namespace crocodicstudio\crudbooster\models;


class SidebarModel
{
    private $id;
    private $name;
    private $url;
    private $permalink;
    private $sub;
    private $sub_active;
    private $icon;
    private $basepath;
    private $is_active;

    /**
     * @return mixed
     */
    public function getPermalink()
    {
        return $this->permalink;
    }

    /**
     * @param mixed $permalink
     */
    public function setPermalink($permalink): void
    {
        $this->permalink = $permalink;
    }



    /**
     * @return mixed
     */
    public function getSubActive()
    {
        return $this->sub_active;
    }

    /**
     * @param mixed $sub_active
     */
    public function setSubActive($sub_active)
    {
        $this->sub_active = $sub_active;
    }



    /**
     * @return mixed
     */
    public function getisActive()
    {
        return $this->is_active;
    }

    /**
     * @param mixed $is_active
     */
    public function setIsActive($is_active)
    {
        $this->is_active = $is_active;
    }



    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }



    /**
     * @return mixed
     */
    public function getBasepath()
    {
        return $this->basepath;
    }

    /**
     * @param mixed $basepath
     */
    public function setBasepath($basepath): void
    {
        $this->basepath = $basepath;
    }



    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url): void
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getSub()
    {
        return $this->sub;
    }

    /**
     * @param mixed $sub
     */
    public function setSub($sub): void
    {
        $this->sub = $sub;
    }

    /**
     * @return mixed
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param mixed $icon
     */
    public function setIcon($icon): void
    {
        $this->icon = $icon;
    }




}