<?php

namespace crocodicstudio\crudbooster\CBCoreModule;

use Illuminate\Support\Facades\Schema;

class DataRemover
{
    private $ctrl;

    /**
     * DataRemover constructor.
     *
     * @param $ctrl
     */
    public function __construct($ctrl)
    {
        $this->ctrl =  $ctrl;
    }

    /**
     * @param $idsArray
     */
    public function deleteIds(array $idsArray)
    {
        $query = $this->ctrl->table()->whereIn($this->ctrl->primaryKey, $idsArray);
        if (Schema::hasColumn($this->ctrl->table, 'deleted_at')) {
            $query->update(['deleted_at' => YmdHis()]);
        } else {
            $query->delete();
        }
    }

    /**
     * @param $idsArray
     */
    public function doDeleteWithHook(array $idsArray)
    {
        $idsArray = $this->ctrl->hookBeforeDelete($idsArray);
        $this->deleteIds($idsArray);
        $this->ctrl->hookAfterDelete($idsArray);
    }
}