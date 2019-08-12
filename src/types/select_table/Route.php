<?php

cb()->routeGroupBackend(function () {
    cb()->routePost("select-table-lookup",'\crocodicstudio\crudbooster\types\select_table\SelectTableController@postLookup');
});