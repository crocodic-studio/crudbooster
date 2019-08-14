<?php

cb()->routeGroupBackend(function () {
    cb()->routePost("upload-file",'\crocodicstudio\crudbooster\types\file\FileController@postUploadFile');
});

cb()->routeGroupDeveloper(function () {
    cb()->routePost("upload-file",'\crocodicstudio\crudbooster\types\file\FileController@postUploadFile');
});