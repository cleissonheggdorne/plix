<?php
ini_set('display_errors', 0);
require "./controller/FilmesController.php";

$rota = $_SERVER["REQUEST_URI"];
$metodo = $_SERVER["REQUEST_METHOD"];
$busca = filter_input(INPUT_GET, 'busca', FILTER_SANITIZE_STRING);
$logado = false;

//Receber o número da página
$pagina_atual= filter_input(INPUT_GET, 'pagina', FILTER_SANITIZE_NUMBER_INT);
 
 $pagina = (!empty($pagina_atual))? $pagina_atual : 1;

 //Setar a quantidade de itens
 $qtd_itens_pag = 30;

// //Calcular o início visualização
 $inicio = ($qtd_itens_pag*$pagina)-$qtd_itens_pag;

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
        $usuario= (object) $_REQUEST;
    
        if(is_bool($controller->verificaDadosUsuario($_REQUEST))){
            if($controller->verificaDadosUsuario($_REQUEST))
                $controller->saveUser($_REQUEST);
               
        }else if($controller->verificaDadosUsuario($_REQUEST) == $usuario->senha1){
            $_SESSION['usuarioCadastro'] = $usuario->usuario;
            $_SESSION['msg'] = 'Senhas não correspondem';
            header("location: /cadastro-de-usuario");
           
        }else if($controller->verificaDadosUsuario($_REQUEST) == $usuario->usuario){
            $_SESSION['usuarioCadastro'] = $usuario->usuario;
            $_SESSION['msg'] = 'Usuário já cadastrado';
            header("location: /cadastro-de-usuario");
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
if ($rota === "/" or substr($rota, 0, strlen("/inicio")) ==="/inicio" && (substr($rota, 0, strlen("/inicio?busca")) != "/inicio?busca")) {
    $controller = new FilmesController();
    $_SESSION['filmes'] = $controller->index($qtd_itens_pag, $inicio);
    require "./view/galeria.php";
    unset($_SESSION['busca']);
    exit();
    
}

//Busca
if($metodo == "GET" && substr($rota, 0, strlen("/inicio?busca")) === "/inicio?busca") {
    if(isset($busca))
        $_SESSION['busca']=$busca;
        $filmesController = new FilmesController();
        $buscaRetornada = $filmesController->busca($busca);
        $_SESSION['buscaRetornada'] = $buscaRetornada;
        header("location: /inicio");
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
//Importar arquivo
 if ($rota === "/importar-arquivo")
    require "./util/importaDados.php";

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