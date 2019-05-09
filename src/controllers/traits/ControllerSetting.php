<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2/3/2019
 * Time: 7:37 PM
 */

namespace crocodicstudio\crudbooster\controllers\traits;

use Closure;
use crocodicstudio\crudbooster\controllers\partials\ButtonColor;
use crocodicstudio\crudbooster\controllers\partials\SidebarStyle;
use crocodicstudio\crudbooster\models\AddActionButtonModel;
use crocodicstudio\crudbooster\models\IndexActionButtonModel;

trait ControllerSetting
{

    private $data = [];

    private function defaultData() {
        $this->setSearchForm(true);
        $this->setButtonDelete(true);
        $this->setButtonEdit(true);
        $this->setButtonAdd(true);
        $this->setButtonCancel(true);
        $this->setButtonAddMore(true);
        $this->setButtonDetail(true);
        $this->setButtonSave(true);
        $this->setButtonLimitPage(true);
        $this->hideButtonDeleteWhen(function ($row) { return false; });
        $this->hideButtonDetailWhen(function ($row) { return false; });
        $this->hideButtonEditWhen(function ($row) { return false; });
    }


    /**
     * @param callable $condition
     */
    public function hideButtonDetailWhen(callable $condition) {
        $this->data['hide_button_detail'] = $condition;
    }

    /**
     * @param callable $condition
     */
    public function hideButtonEditWhen(callable $condition) {
        $this->data['hide_button_edit'] = $condition;
    }

    /**
     * @param callable $condition
     */
    public function hideButtonDeleteWhen(callable $condition) {
        $this->data['hide_button_delete'] = $condition;
    }

    /**
     * @param $enable boolean
     */
    public function setButtonLimitPage($enable) {
        $this->data['button_limit_page'] = $enable;
    }

    public function setPermalink($path)
    {
        $this->data['permalink'] = $path;
    }

    public function disableSoftDelete()
    {
        $this->data['disable_soft_delete'] = true;
    }

    /**
     * @param Closure $callbackQuery
     */
    public function hookIndexQuery(Closure $callbackQuery)
    {
        $this->data['hook_index_query'] = $callbackQuery;
    }

    /**
     * @param Closure $callback
     */
    public function hookAfterInsert(Closure $callback)
    {
        $this->data['hook_after_insert'] = $callback;
    }

    /**
     * @param Closure $callback
     */
    public function hookBeforeInsert(Closure $callback)
    {
        $this->data['hook_before_insert'] = $callback;
    }

    /**
     * @param Closure $callback
     */
    public function hookBeforeUpdate(Closure $callback)
    {
        $this->data['hook_before_update'] = $callback;
    }

    /**
     * @param Closure $callback
     */
    public function hookAfterUpdate(Closure $callback)
    {
        $this->data['hook_after_update'] = $callback;
    }

    /**
     * @param Closure $callbackQuery
     */
    public function hookSearchQuery(Closure $callbackQuery)
    {
        $this->data['hook_search_query'] = $callbackQuery;
    }

    /**
     * @param string $label
     * @param string $actionURL
     * @param string $fontAwesome_icon
     * @param ButtonColor $color
     * @param string $attributes
     */
    public function addIndexActionButton($label, $actionURL, $fontAwesome_icon=null, $color=null, $attributes = null)
    {
        $color = ($color)?:ButtonColor::DARK_BLUE;

        $model = new IndexActionButtonModel();
        $model->setLabel($label);
        $model->setIcon($fontAwesome_icon);
        $model->setColor($color);
        $model->setUrl($actionURL);
        $model->setAttributes($attributes);

        $this->data['index_action_button'][] = $model;
    }

    public function setOrderBy($field, $sort = 'desc')
    {
        $this->data['order_by'] = [$field, $sort];
        return $this;
    }

    /**
     * @param boolean $enable
     * @return $this
     */
    public function setSearchForm($enable) {
        $this->data['search_form'] = $enable;
        return $this;
    }

    /**
     * @param boolean $button_save
     * @return ControllerSetting
     */
    public function setButtonSave($button_save)
    {
        $this->data['button_save'] = $button_save;
        return $this;
    }

    /**
     * @param boolean $button_cancel
     * @return $this
     */
    public function setButtonCancel($button_cancel)
    {
        $this->data['button_cancel'] = $button_cancel;
        return $this;
    }


    /**
     * @param $label
     * @param callable|string $url_target
     * @param callable|string $condition_callback
     * @param $fontAwesome_icon
     * @param ButtonColor|string $color
     */
    public function addActionButton($label, $url_target, $condition_callback=null, $fontAwesome_icon=null, $color=null, $confirmation = false)
    {
        $new = new AddActionButtonModel();
        $new->setLabel($label);
        $new->setIcon($fontAwesome_icon?:"fa fa-bars");
        $new->setColor($color?:"primary");
        $new->setUrl($url_target);
        $new->setCondition($condition_callback);
        $new->setConfirmation($confirmation);
        $this->data['add_action_button'][] = $new;
    }
    /**
     * @param mixed $button_edit
     * @param null $condition_callback
     * @return ControllerSetting
     */
    public function setButtonEdit($button_edit, $condition_callback = null)
    {
        $this->data['button_edit'] = $button_edit;
        $this->data['button_edit_callback'] = $condition_callback;
        return $this;
    }

    /**
     * @param mixed $button_detail
     * @param null $condition_callback
     * @return ControllerSetting
     */
    public function setButtonDetail($button_detail, $condition_callback = null)
    {
        $this->data['button_detail'] = $button_detail;
        $this->data['button_detail_callback'] = $condition_callback;
        return $this;
    }

    /**
     * @param mixed $button_delete
     * @param null $condition_callback
     * @return ControllerSetting
     */
    public function setButtonDelete($button_delete, $condition_callback = null)
    {
        $this->data['button_delete'] = $button_delete;
        $this->data['button_delete_callback'] = $condition_callback;
        return $this;
    }



    /**
     * @param string $alert_message
     * @return ControllerSetting
     */
    public function setAlertMessage($alert_message)
    {
        $this->data['alert_message'] = $alert_message;
        return $this;
    }


    /**
     * @param string $html_or_view
     * @return ControllerSetting
     */
    public function setBeforeIndexTable($html_or_view)
    {
        $this->data['before_index_table'] = $html_or_view;
        return $this;
    }

    /**
     * @param mixed $html_or_view
     * @return ControllerSetting
     */
    public function setAfterIndexTable($html_or_view)
    {
        $this->data['after_index_table'] = $html_or_view;
        return $this;
    }

    /**
     * @param callable|string $html_or_view
     * @return ControllerSetting
     */
    public function setBeforeDetailForm($html_or_view)
    {
        $this->data['before_detail_form'] = $html_or_view;
        return $this;
    }

    /**
     * @param callable|string $html_or_view
     * @return ControllerSetting
     */
    public function setAfterDetailForm($html_or_view)
    {
        $this->data['after_detail_form'] = $html_or_view;
        return $this;
    }


    /**
     * @param mixed $page_title
     * @return ControllerSetting
     */
    public function setPageTitle($page_title)
    {
        $this->data['page_title'] = $page_title;
        return $this;
    }


    /**
     * @param mixed $icon
     * @return ControllerSetting
     */
    public function setPageIcon($icon = "fa fa-table")
    {
        $this->data['page_icon'] = $icon;
        return $this;
    }

    /**
     * @param mixed $table
     * @return ControllerSetting
     */
    public function setTable($table)
    {
        $this->data['table'] = $table;
        return $this;
    }

    /**
     * @param int $limit
     * @return ControllerSetting
     */
    public function setLimit($limit)
    {
        $this->data['limit'] = $limit;
        return $this;
    }

    /**
     * @param bool $button_filter
     * @return ControllerSetting
     */
    public function setButtonFilter($button_filter)
    {
        $this->data['button_filter'] = $button_filter;
        return $this;
    }


    /**
     * @param bool $button_add_more
     * @return ControllerSetting
     */
    public function setButtonAddMore($button_add_more)
    {
        $this->data['button_add_more'] = $button_add_more;
        return $this;
    }

    /**
     * @param bool $button_add
     * @return ControllerSetting
     */
    public function setButtonAdd($button_add)
    {
        $this->data['button_add'] = $button_add;
        return $this;
    }

    /**
     * @param string $head_html
     * @return ControllerSetting
     */
    public function setHeadHtml($head_html)
    {
        $this->data['head_html'] = $head_html;
        return $this;
    }

    /**
     * @param string $bottom_html
     * @return ControllerSetting
     */
    public function setBottomHtml($bottom_html)
    {
        $this->data['bottom_html'] = $bottom_html;
        return $this;
    }

    /**
     * @param SidebarStyle|string $sidebar_style
     * @return ControllerSetting
     */
    public function setSidebarStyle($sidebar_style)
    {
        $this->data['sidebar_style'] = $sidebar_style;
        return $this;
    }

}