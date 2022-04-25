<?php
session_start(); 

//ini_set('display_errors', 0);

require "./controller/FilmesController.php";

$rota = $_SERVER["REQUEST_URI"];
$metodo = $_SERVER["REQUEST_METHOD"];
$logado = false;
$logadoAdmin = false;

//Receber o número da página
$pagina_atual= filter_input(INPUT_GET, 'pagina', FILTER_SANITIZE_NUMBER_INT);
 
 $pagina = (!empty($pagina_atual))? $pagina_atual : 1;

 //Setar a quantidade de itens
 $qtd_itens_pag = 30;

// //Calcular o início visualização
 $inicio = ($qtd_itens_pag*$pagina)-$qtd_itens_pag;

//Política e termos
if($rota === '/politica-e-termos'){
    require "./view/paginas/politicaDePrivacidade.php";
    exit;
}

//Página de Login
if (substr($rota, 0, strlen("/login"))  === "/login") {
    $controller = new FilmesController();
    if($metodo == "GET") 
        
        $chave = filter_input(INPUT_GET, "chave", FILTER_SANITIZE_STRING);
        if (isset($_SESSION['usuario'])){
            if ($controller->verificaUsuario($_SESSION['usuario'])){
                $logado = true;
                header("location: /");
            }
        }else if(isset($chave)){
            unset($_SESSION['usuario']);
            $controller = new FilmesController();
            $controller->confirmaEmail($chave);
        }else
            require "./view/paginas/login.php";

    if($metodo == "POST") {
        if(isset($_REQUEST['email_redefinir']) AND $_REQUEST['email_redefinir'] !== ""){
            $controller = new FilmesController();
            $controller->redefinirSenha($_REQUEST['email_redefinir']);
        }else{
            $controller = new FilmesController();
            $result = $controller->validate($_REQUEST);    
            if($result){ //false or user
                $logado = true;
                if($result['situacao'] === 'ativo'){
                    $_SESSION['usuario'] = $result['dados']['usuario'];
                    $_SESSION['id_usuario'] = $result['dados']['id'];
                    $_SESSION['admin'] = $result['dados']['admin'];
                    if(isset($_SESSION['usuario']))
                        if($controller->verificaUsuario($_SESSION['usuario']))
                            $_SESSION['msg'] = "Seja Bem Vindo!";
                            header("location: /");
                }else if ($result['situacao'] === "aguardando confirmação"){
                    $_SESSION['msg'] = "Ops, precisamos que confirme seu email!";
                    header("location: /login");
                }
            }else{
                $_SESSION['msg'] = "Email ou Senha Incorretos";
                header("location: /login");
            }
        }
    }
    exit();
}

//Cadastro de Usuário
if ($rota === "/cadastro-de-usuario") {
    $controller = new FilmesController();
    if($metodo == "GET") 
        require "./view/paginas/cadastrarUsuario.php";
    
    if($metodo == "POST") 
        $usuario= (object) $_REQUEST;
    
        if(is_bool($controller->verificaDadosUsuario($_REQUEST))){
            if($controller->verificaDadosUsuario($_REQUEST))
                $controller->saveUser($_REQUEST);
        //Verifica Senhas se correspondem
        }else if($controller->verificaDadosUsuario($_REQUEST) == $usuario->senha1){
            $_SESSION['usuarioCadastro'] = $usuario->usuario;
            $_SESSION['msg'] = 'Senhas não correspondem';
            header("location: /cadastro-de-usuario");
        //Verifica se usuário já não existe (email)
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
if ($rota === "/" 
    or substr($rota, 0, strlen("/inicio")) ==="/inicio" && 
    (substr($rota, 0, strlen("/inicio?busca")) != "/inicio?busca")) {
    $controller = new FilmesController();
    $_SESSION['filmes'] = $controller->index($qtd_itens_pag, $inicio);
    require "./view/paginas/galeria.php";
    unset($_SESSION['busca']);
    exit(); 
}
//Pagina Filmes por Genero
if (substr($rota, 0, strlen("/genero/")) === "/genero/") {
    $generoId= filter_input(INPUT_GET, 'genero', FILTER_SANITIZE_NUMBER_INT);
    $urlEmArray = explode("/", $rota); //Divide a url em um array de acordo com o separador escolhido
    $controller = new FilmesController();
    $_SESSION['filmes'] = $controller->generoController(['id'=>$urlEmArray[2], 'inicio'=>$inicio, 'numItens' => $qtd_itens_pag]);
    require "./view/paginas/galeriaPorGenero.php";
    unset($_SESSION['busca']);
    $_SESSION['teste'] = $arr2;
    require "./tests/tests.php";
    exit(); 
}

//Pagina de Controle
if ((substr($rota, 0, strlen("/syscontrol")) === "/syscontrol") && $_SESSION['admin'] == true) {
    $controller = new FilmesController();
    $_SESSION['filmes'] = $controller->index($qtd_itens_pag, $inicio);
    if($metodo == "GET")
        if(substr($rota, 0, strlen("/syscontrol?busca")) === "/syscontrol?busca"){
            $busca = filter_input(INPUT_GET, 'busca', FILTER_SANITIZE_STRING);
            $_SESSION['busca']=$busca;
            if(isset($busca))
                $filmesController = new FilmesController();
                $_SESSION['buscaRetornada'] = $titulosRetornados = $filmesController->busca(['titulo'=>$busca], 'syscontrol');
                header("location: /syscontrol");
                exit();

        //Busca filmes pelo título
        }else if(substr($rota, 0, strlen("/syscontrol?titulo-para-buscar")) === "/syscontrol?titulo-para-buscar"){
            $buscaTitulo = filter_input(INPUT_GET, 'titulo-para-buscar', FILTER_SANITIZE_STRING); //Busca para salvar filme
            $tipo = filter_input(INPUT_GET, 'tipo', FILTER_SANITIZE_STRING); //Busca para salvar filme
            if(isset($buscaTitulo))
                $filmesController = new FilmesController();
                $filmesController->ondeBuscar($buscaTitulo, $tipo);

        //Busca filme específico pelo id do TMDB
        }else if(substr($rota, 0, strlen("/syscontrol?id-filme-tmdb")) === "/syscontrol?id-filme-tmdb"){
                $idTmdb = filter_input(INPUT_GET, 'id-filme-tmdb', FILTER_SANITIZE_STRING); //Id do filme no TMDB
                $filmesController = new FilmesController();
                $filmesController->getApiRegisterFilm($idTmdb);

        //Busca para adicionar ao slide
        }else if(substr($rota, 0, strlen("/syscontrol?id-para-destaque")) === "/syscontrol?id-para-destaque"){
            
            $idNovo = filter_input(INPUT_GET, 'id-para-destaque', FILTER_SANITIZE_STRING); 
            $idAnterior = filter_input(INPUT_GET, 'id-anterior', FILTER_SANITIZE_STRING);
           
            $filmesController = new FilmesController();
            $filmesController->atualizaDestaques($idNovo, $idAnterior);
        }else if(substr($rota, 0, strlen("/syscontrol?destaque-busca=")) === "/syscontrol?destaque-busca="){
            
            $busca = filter_input(INPUT_GET, 'destaque-busca', FILTER_SANITIZE_STRING);
            $tipo = filter_input(INPUT_GET, 'tipo', FILTER_SANITIZE_STRING);
            
            $filmesController = new FilmesController();
            $filmesController->busca(['titulo'=>$busca], $tipo);
        }else if(substr($rota, 0, strlen("/syscontrol?id-para-editar")) === "/syscontrol?id-para-editar"){
            
            $idFilme = filter_input(INPUT_GET, 'id-para-editar', FILTER_SANITIZE_STRING);
            $tipo = filter_input(INPUT_GET, 'tipo', FILTER_SANITIZE_STRING);
            
            $filmesController = new FilmesController();
            $infoFilme = $filmesController-> buscaInfoFilme([$tipo=>$idFilme]);
          

        }else{
            require "./view/paginas/controle.php";
            //busca no modal de seleção de destaques
        }
    else if ($metodo == "POST") {
        if(isset($_FILES))
            if(array_key_exists('import_file',$_FILES)){
                //$_SESSION['teste'] = $_FILES;
                //include "./view/Teste.php";
                $filmesController = new FilmesController();
                $dadosFilme = (array) $_REQUEST;
                $filmesController->save('arquivo', $_REQUEST);
            }else{
                $filmesController = new FilmesController();
                $dadosFilme = (array) $_REQUEST;
                (array_key_exists('id', $dadosFilme))? $filmesController->edit($_REQUEST) :  $filmesController->save('filme',$_REQUEST);
            }
        
    };

    exit();
}

//Busca
if($metodo == "GET" && (substr($rota, 0, strlen("/inicio?busca")) === "/inicio?busca")
     or substr($rota, 0, strlen("/?busca")) === "/?busca") {
    
        $busca = filter_input(INPUT_GET, 'busca', FILTER_SANITIZE_STRING); //Busca Galeria
        if(isset($busca))
            $_SESSION['busca']= $busca;
            $filmesController = new FilmesController();
            $_SESSION['buscaRetornada'] = $filmesController->busca(['titulo'=>$busca], "galeria");
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
                require "./view/paginas/favoritos.php";
        }else
            $_SESSION['msg'] = "Entre para visualizar seus Favoritos";
            header("location: /");

    exit();
}
//Pagina Assistir Mais Tarde
if ($rota === "/salvos") {
    $controller = new FilmesController();
    if($metodo == "GET") 
        
        if (isset($_SESSION['usuario'])){
            if($controller->verificaUsuario($_SESSION['usuario']))
                $logado = true;
                require "./view/paginas/assistirMaisTarde.php";
        }else
            $_SESSION['msg'] = "Entre para visualizar suas mídias salvas";
            header("location: /");

    exit();
}

//Pagina individual de cada filme
if (substr($rota, 0, strlen("/assistir")) ==="/assistir"){
    $busca = filter_input(INPUT_GET, 'busca', FILTER_SANITIZE_STRING);
    if (substr($rota, 0, strlen("/assistir-mais-tarde")) ==="/assistir-mais-tarde"){
        $controller= new FilmesController();
        $controller->assistirMaisTarde(basename($rota)); //Passa apenas a base da url (id do filme) 
        exit();
    }else if(isset($busca) && $busca != "")
        header("location: /?busca=$busca");
    else
        require "./view/paginas/assistir.php";
     exit();
 }

//Pagina Editar
// if (substr($rota, 0, strlen("/editar")) ==="/editar" && $_SESSION['admin'] == true){
//     $controller = new FilmesController();
//     if($metodo == "GET") 
        
//         if (isset($_SESSION['usuario'])){
//             if($controller->verificaUsuario($_SESSION['usuario']) && $_SESSION['admin']=== true)
//                 $logado = true;
//                 require "./view/editar.php";

//         }else
//             $_SESSION['msg'] = "Você não tem acesso a esta funcionalidade";
//             header("location: /");
    
//     if($metodo == "POST") {
//             $controller = new FilmesController();
//             $controller->edit($_REQUEST);
//     };
//     exit();
// }

//Rota de Favoritar
if (substr($rota, 0, strlen("/favoritar")) ==="/favoritar"){
    $controller= new FilmesController();
    $controller->favorite(basename($rota)); //Passa apenas a base da url (id do filme) 
    exit();
}
  
//Rota de salvar para assistir mais tarde
// if (substr($rota, 0, strlen("/assistir-mais-tarde")) ==="/assistir-mais-tarde"){
//     //$controller= new FilmesController();
//     //$controller->assistirMaisTarde(basename($rota)); //Passa apenas a base da url (id do filme) 
//     echo json_encode('ok');
//     exit();
// }

//Rota de deletar
if (substr($rota, 0, strlen("/filmes")) ==="/filmes"){
    if($metodo == "GET") require "./view/galeria.php";
    if($metodo == "DELETE") {
        $controller= new FilmesController();
        $controller->delete(basename($rota)); //Passa apenas a base da url (id do filme) 
    }
    exit();
}

//Atualiza com API
// if($rota === "/atualizaBack"){
//     require "./util/api.php";
//     exit;
// }

//Atualiza com API
//if($rota === "/importar-arquivo"){
   // require "./util/importaDados.php";
  //  exit;
//}

require "./view/paginas/404.php";
