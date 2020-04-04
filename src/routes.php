<?php

use crocodicstudio\crudbooster\helpers\CB;

CB::routeAPI();
CB::routeAdminAuth();
CB::routeFileHandler();
CB::routeAdminModules();
CB::routeCBModules();
