<?php

 ini_set("display_errors","0");
 ini_set("display_startup_errors","1");
 ini_set('error_reporting', E_ALL);
define ('MESSAGES_PER_PAGE',2);
define ('PAGINATION_INDENT',5);

require_once('controller/Controller.php');
require_once('model/Model.php');
require_once('view/View.php');
require_once('DataBase.php');

session_start();
$controller = new Controller();
$controller->run();
?>