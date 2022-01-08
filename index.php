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
        
        if (isset($_SESSION['usuario'])){
            if ($_SESSION['usuario'] != "")
                $logado = true;
        }else
            require "./view/login.php";
            //header("location: /login");

    if($metodo == "POST") {
        $controller = new FilmesController();
        $result = $controller->validate($_REQUEST);
        
        if($result){ //false or user
            $logado = true;
            $_SESSION['usuario'] = $result['usuario'];
            $_SESSION['id_usuario'] = $result['id'];
            if(isset($_SESSION['usuario']))
                $_SESSION['msg'] = "Seja Bem Vindo!";
                header("location: /");
        }else{
           // session_destroy;
            //$_SESSION['msg'] = "Erro de Acesso!";
            header("location: /login");
        }
    };
    exit();
}
//SAIR
if ($rota === "/sair") {
    session_destroy();
    header("location: /login");
    exit();
}

//Pagina Galeria
if ($rota === "/") {
    require "./view/galeria.php";
    exit();
}

//Pagina Favoritos
if ($rota === "/favoritos") {
    if($metodo == "GET") 
        
        if (isset($_SESSION['usuario'])){
            if ($_SESSION['usuario'] != "")
                $logado = true;
                require "./view/favoritos.php";
        }else
            $_SESSION['msg'] = "Entre para visualizar seus Favoritos";
            header("location: /");

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
    //if($metodo == "GET") require "./view/editar.php";
    if($metodo == "GET") 
        

        if (isset($_SESSION['usuario'])){
            if ($_SESSION['usuario'] != "")
                $logado = true;
                require "./view/editar.php";

        }else
            $_SESSION['msg'] = "Você não tem acesso a esta funcionalidade";
            header("location: /");
            //header("location: /login"); 
     
    
    if($metodo == "POST") {
            $controller = new FilmesController();
            $controller->edit($_REQUEST);
    };
    exit();
}

//Página Cadastrar
if ($rota === "/novo") {
    if($metodo == "GET") 
    
        if (isset($_SESSION['usuario'])){
            if ($_SESSION['usuario'] != "")
                $logado = true;
                require "./view/cadastrar.php";

        }else
            $_SESSION['msg'] = "Você não tem acesso a esta funcionalidade";
            header("location: /");
            //header("location: /login"); 

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