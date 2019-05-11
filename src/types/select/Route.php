<?php

cb()->routeGroupBackend(function () {
    cb()->routePost("select-lookup",'\crocodicstudio\crudbooster\types\select\SelectController@postLookup');
});