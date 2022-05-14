<?php

REQUIRE "FilmesController.php"; 
$controller = new FilmesController();

$metodo = $_SERVER["REQUEST_METHOD"];
$usuario = $_POST['usuario'];
$senha = $_POST['senha'];
$request = $_REQUEST;

switch ($metodo){
    case "POST":
        $dados=  $controller->validate($request);
        break;
}
?>