<?php
//Define folder-path of this site-folder
define('ROOT_PATH', realpath('..'));

//Register Composer Autoload
require_once  '../../vendor/autoload.php';

//Include Bootstrap
require_once ROOT_PATH.'/app/Bootstrap.php';

//Run App
$app = new Bootstrap('local');

$app->run();