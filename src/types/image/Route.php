<?php

cb()->routeGroupBackend(function () {
    cb()->routePost("upload-image",'\crocodicstudio\crudbooster\types\image\ImageController@postUploadImage');
});

cb()->routeGroupDeveloper(function () {
    cb()->routePost("upload-image",'\crocodicstudio\crudbooster\types\image\ImageController@postUploadImage');
});
