<?php

cb()->routeGroupBackend(function () {
    cb()->routePost("upload-image",'\crocodicstudio\crudbooster\types\image\ImageController@postUploadImage');
});