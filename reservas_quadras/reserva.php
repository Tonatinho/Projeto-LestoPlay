<?php
require_once __DIR__ . '/config.php';
require_once ROOT_PATH . '/app/controllers/ReservaController.php';
require_once ROOT_PATH . '/includes/db.php';

$db = conectarDB();
$controller = new ReservaController($db);
$controller->index();
?>

