<?php
require_once '../router.php';

$request_uri = explode ("/", $_SERVER['REQUEST_URI']);
$router = new Router($request_uri, $_REQUEST, $_SERVER["REQUEST_METHOD"]);

$router->run();