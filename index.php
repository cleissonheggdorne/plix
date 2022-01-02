<?php
ini_set('display_errors', 0);

$rota = $_SERVER["REQUEST_URI"];
$metodo = $_SERVER["REQUEST_METHOD"];
$logado = false;

//require "./util/mensagem.php";
require "./controller/FilmesController.php";

//Págia de Login
if ($rota === "/login") {

    if($metodo == "GET") 
        require "./view/login.php";

        if (isset($_SESSION['usuario']))
            if ($_SESSION['usuario'] != "")
                $logado = true;
            else
                $_SESSION['msg'] = "Erro de Acesso!";
                require "./view/login.php";

    if($metodo == "POST") {
        $controller = new FilmesController();
        $result = $controller->validate($_REQUEST);
        
        if($result){
            $logado = true;
            $_SESSION['msg'] = "Seja Bem Vindo!";
            //require "./view/galeria.php";
        }
    };
    exit();
}

//Pagina Galeria
if ($rota === "/") {
    require "./view/galeria.php";
    exit();
}

//Pagina Favoritos
if ($rota === "/favoritos") {
    require "./view/favoritos.php";
    exit();
}

//Pagina individual de cada filme
if (substr($rota, 0, strlen("/assistir")) ==="/assistir"){
    require "./view/assistir.php";
    //pageFilme($_GET["id"]);
    exit();
}

//Pagina Editar
if (substr($rota, 0, strlen("/editar")) ==="/editar"){
    if($metodo == "GET") require "./view/editar.php";
        if($metodo == "POST") {
            $controller = new FilmesController();
            $controller->edit($_REQUEST);
        };
        exit();
}

//Página Cadastrar
if ($rota === "/novo") {
    if($metodo == "GET") require "./view/cadastrar.php";
        if($metodo == "POST") {
            $controller = new FilmesController();
            $controller->save($_REQUEST);
        };
        exit();
}

//Rota de Favoritar
if (substr($rota, 0, strlen("/favoritar")) ==="/favoritar"){
    $controller= new FilmesController();
    $controller->favorite(basename($rota)); //Passa apenas a base da url (id do filme) 
    exit();
}
  
//Rota de deletar
if (substr($rota, 0, strlen("/filmes")) ==="/filmes"){
    if($metodo == "GET") require "./view/galeria.php";
    if($metodo == "DELETE") {
        $controller= new FilmesController();
        $controller->delete(basename($rota)); //Passa apenas a base da url (id do filme) 
    }
    exit();
}


require "./view/404.php";

?>