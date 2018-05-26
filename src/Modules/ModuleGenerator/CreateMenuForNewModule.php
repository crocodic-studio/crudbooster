<?php

namespace crocodicstudio\crudbooster\Modules\ModuleGenerator;

class CreateMenuForNewModule
{
    public function execute($ctrl, $name, $icon)
    {
        $parentMenuId = $this->createParentMenu($name, $icon);
        $this->createSubMenus($ctrl, $name, $parentMenuId);
    }

    /**
     * @param $name
     * @param $icon
     * @return mixed
     */
    private function createParentMenu($name, $icon)
    {
        $menu_id = $this->table()->insertGetId([
            'created_at' => YmdHis(),
            'name' => $name,
            'icon' => $icon,
            'path' => '#',
            'type' => 'URL External',
            'is_active' => 1,
            'cms_privileges' => auth('cbAdmin')->user()->id_cms_privileges,
            'sorting' => $this->table()->where('parent_id', 0)->max('sorting') + 1,
            'parent_id' => 0,
        ]);

        return $menu_id;
    }

    /**
     * @param $arr
     * @param $name
     * @param $ctrl
     */
    private function createAddLinkSubmenu($arr , $name, $ctrl)
    {
        $this->table()->insert([
                'name' => cbTrans('text_default_add_new_module', ['module' => $name]),
                'icon' => 'fa fa-plus',
                'path' => $ctrl.'GetAdd',
                'sorting' => 1,
            ] + $arr);
    }

    /**
     * @param $arr
     * @param $name
     * @param $ctrl
     */
    private function createIndexLinkSubMenu($arr, $name, $ctrl)
    {
        $this->table()->insert([
                'name' => cbTrans('text_default_list_module', ['module' => $name]),
                'icon' => 'fa fa-bars',
                'path' => $ctrl.'GetIndex',
                'sorting' => 2,
            ] + $arr);
    }

    /**
     * @param $ctrl
     * @param $name
     * @param $parentMenuId
     */
    private function createSubMenus($ctrl, $name, $parentMenuId)
    {
        $arr = [
            'created_at' => YmdHis(),
            'type' => 'Route',
            'is_active' => 1,
            'cms_privileges' => auth('cbAdmin')->user()->id_cms_privileges,
            'parent_id' => $parentMenuId,
        ];

        $this->createAddLinkSubmenu($arr, $name, $ctrl);
        $this->createIndexLinkSubMenu($arr, $name, $ctrl);
    }

    /**
     * @return mixed
     */
    private function table()
    {
        return \DB::table('cms_menus');
    }
}