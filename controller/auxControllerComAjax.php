<?php
// namespace Heggdorne\controller;
//include "./controller/FilmesController.php";
REQUIRE "./repository/FilmesRepositoryPDO.php"; 
$controller = new FilmesController();
//$repository = new FilmesRepositoryPDO();

$metodo = $_SERVER["REQUEST_METHOD"];
$usuario = $_POST['usuario'];
$senha = $_POST['senha'];
$request = $_REQUEST;

switch ($metodo){
    case "POST":
        //$dados=  $controller->validate($request);
        $dados=  $repository->validar($request);
        print_r($request);
        break;

}
?>