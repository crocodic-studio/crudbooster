<?php namespace crocodicstudio\crudbooster\inputs;

class Checkbox extends InputContainer
{
    use InputTrait;

    protected $type = "checkbox";

    private $datatable;
    private $sort_by_column;
    private $sort_by_dir;
    private $relationship_table;

    public function dataSource($table, $display_column) {
        $this->datatable = $table.",".$display_column;
        return $this;
    }

    public function relationshipTable($table) {
        $this->relationship_table = $table;
        return $this;
    }

    public function sortBy($column, $dir = "desc") {
        $this->sort_by_column = $column;
        $this->sort_by_dir = $dir;
        return $this;
    }
    

}