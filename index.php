<?php
ini_set('display_errors', 0);

$rota = $_SERVER["REQUEST_URI"];
$metodo = $_SERVER["REQUEST_METHOD"];
$logado = false;

require "./controller/FilmesController.php";

//Págia de Login
if ($rota === "/login") {
    $controller = new FilmesController();
    if($metodo == "GET") 
        
        if (isset($_SESSION['usuario'])){
            if ($controller->verificaUsuario($_SESSION['usuario'])){
                $logado = true;
                header("location: /");
            }
        }else
            require "./view/login.php";

    if($metodo == "POST") {
        $result = $controller->validate($_REQUEST);
        
        if($result){ //false or user
            $logado = true;
            $_SESSION['usuario'] = $result['usuario'];
            $_SESSION['id_usuario'] = $result['id'];
            $_SESSION['admin'] = $result['admin'];
            if(isset($_SESSION['usuario']))
                if($controller->verificaUsuario($_SESSION['usuario']))
                    $_SESSION['msg'] = "Seja Bem Vindo!";
                    header("location: /");
        }else{
            $_SESSION['msg'] = "Email ou Senha Incorretos";
            header("location: /login");
        }
    };
    exit();
}

//Cadastro de Usuário
if ($rota === "/cadastro-de-usuario") {
    $controller = new FilmesController();
    if($metodo == "GET") 
        require "./view/cadastrarUsuario.php";
    
    if($metodo == "POST") 
        if(!(is_bool($controller->verificaDadosUsuario($_REQUEST)))){
            $_SESSION['usuarioCadastro'] = $controller->verificaDadosUsuario($_REQUEST);
            $_SESSION['msg'] = 'Senhas não correspondem';
            header("location: /cadastro-de-usuario");
            
        }else {
            $controller->saveUser($_REQUEST);
        }
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
    $controller = new FilmesController();
    if($metodo == "GET") 
        
        if (isset($_SESSION['usuario'])){
            if($controller->verificaUsuario($_SESSION['usuario']))
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
    exit();
}

//Pagina Editar
if (substr($rota, 0, strlen("/editar")) ==="/editar"){
    $controller = new FilmesController();
    if($metodo == "GET") 
        
        if (isset($_SESSION['usuario'])){
            if($controller->verificaUsuario($_SESSION['usuario']))
                $logado = true;
                require "./view/editar.php";

        }else
            $_SESSION['msg'] = "Você não tem acesso a esta funcionalidade";
            header("location: /");
    
    if($metodo == "POST") {
            $controller = new FilmesController();
            $controller->edit($_REQUEST);
    };
    exit();
}

//Página Cadastrar
if ($rota === "/novo") {
    $controller = new FilmesController();
    if($metodo == "GET") 
    
        if (isset($_SESSION['usuario'])){
            if($controller->verificaUsuario($_SESSION['usuario']))
                $logado = true;
                require "./view/cadastrar.php";

        }else
            $_SESSION['msg'] = "Você não tem acesso a esta funcionalidade";
            header("location: /");

    if($metodo == "POST") {
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