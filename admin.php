<?php

require_once 'includes/db.php';          
require_once 'Admin/Admin_control.php';  

$controller = new AdminController($pdo);
$controller->run();   // xử lý request, redirect nếu cần, build $data

$data = $controller->data;
require_once 'Admin/Admin_ui.php';     