<?php 
// namespace Heggdorne\controller;

if(!isset($_SESSION)){
    session_start(); 
}

REQUIRE __DIR__."/../repository/FilmesRepositoryPDO.php"; 
REQUIRE __DIR__."/../util/ConsumoApi.php";
REQUIRE __DIR__."/../model/confirmacaoEmail.php";
// use Heggdorne\repository\FilmesRepositoryPDO as FilmesRepositoryPDO;
//Load Composer's autoloader
require __DIR__."/../lib/vendor/autoload.php";
class FilmesController{
    public function index($itens, $inicio){
        $filmesRepository = new FilmesRepositoryPDO();
        return $filmesRepository->listarTodos($itens, $inicio);
    }

    public function destaques(){
        $filmesRepository = new FilmesRepositoryPDO();
        return $filmesRepository->buscaDestaques();
    }

    public function busca($busca, $tipo){
        $filmesRepository = new FilmesRepositoryPDO();
        if(array_key_exists('id', $busca)){
            echo json_encode($filmesRepository->listarBusca($busca));
        }else if(array_key_exists('titulo', $busca) and ($tipo == "galeria" or $tipo == "syscontrol")){
            return $filmesRepository->listarBusca($busca);
        }else if(array_key_exists('titulo', $busca) and $tipo == "destaque"){
            echo json_encode($filmesRepository->listarBusca($busca));
        }
        
    }

    public function generoController(array $dados){
        $filmesRepository = new FilmesRepositoryPDO();
        return $filmesRepository->listarPorGenero($dados);
    }

    public function atualizaDestaques($idNovo, $idAnterior){
         $filmesRepository = new FilmesRepositoryPDO();
         $result = ['success' => $filmesRepository->atualizaDestaques($idNovo, $idAnterior)];
         if($result['success'] === "ok")
            $_SESSION['msg'] = "Destaque Alterado Com Sucesso";
         else 
            $_SESSION['msg'] = "Houve Um Problema Ao Alterar Destaque";

        echo json_encode($result);
    }

    // Valida login 
    public function validate($request){
        $filmesRepository = new FilmesRepositoryPDO();
        $dados = (object) $request;
        
        $dadosUsuario = $filmesRepository->validar($dados);
        if($dadosUsuario['dados']!== false){ //Executa a instru????o verificando se TRUE//
            if($dadosUsuario['situacao'] === 'ativo'){
                $_SESSION['usuario'] = $dadosUsuario['dados']['usuario'];
                $_SESSION['id_usuario'] = $dadosUsuario['dados']['id'];
                $_SESSION['admin'] = $dadosUsuario['dados']['admin'];
                $_SESSION['msg'] = 'Seja bem vindo!';
            }
        }
        header('Content-type: application/json');
        echo json_encode($dadosUsuario);
        
    }
    //Recebe email 
    public function redefinirSenha(String $email){
        $filmesRepository = new FilmesRepositoryPDO();
        //Verifica se usuario (email) est?? cadastrado na base. 
        $dados = $filmesRepository->verificaCadastroExistente($email);
        if($dados === true){
            //Se o cadastro existe gera uma chave e retorna a situa????o da execucao do sql e a chave gerada
            $dadosRetorno = $filmesRepository->geraChaveParaRedefinirSenha($email);
            if($dadosRetorno['pass'] AND $dadosRetorno['chave'] !== ""){
                $confirmaEmail = new ConfirmacaoEmail();
                $result = $confirmaEmail->disparaEmailDeRecuperacao(['chave'=>$dadosRetorno['chave'], 'usuario'=>$email]);
                if($result['msg'] === "ok"){
                    $result['msg'] = 'Enviamos um email de recupera????o para o email digitado!';
                    header('Content-type: application/json');
                    echo json_encode($result['msg']);
                    
                }else{
                    $result['msg'] = 'Ocorreu um problema, tente novamente!';
                    header('Content-type: application/json');
                    echo json_encode($result['msg']);
                }
            }
        }else{
            $result['msg'] = 'N??o localizamos esse email na nossa base de dados!';
            header('Content-type: application/json');
            echo json_encode($result['msg']);
        }
    }

    public function saveUser($request){
        $filmesRepository = new FilmesRepositoryPDO();
        $usuario= (object) $request;
        $retorno = $filmesRepository->salvarUsuario($usuario);
        if($retorno['pass']){  //Executa a instru????o//
            $chave = $retorno['chave'];
            $confirmaEmail = new ConfirmacaoEmail();
            $result = $confirmaEmail->disparaEmailDeConfirmacao(['chave'=>$chave, 'usuario'=>$usuario->usuario]);
            
            if($result['msg'] === "ok")
                $_SESSION["msg"] = "Usu??rio cadastrado. Verifique sua caixa de email antes de logar.";
            else
                $_SESSION['msg'] = "Desculpe, n??o conseguimos enviar a confirma????o. \n Tente novamente.";
        }else
            $_SESSION["msg"] = "Erro ao cadastrar usu??rio";
        
        header("Location: /login"); //Redirecionamento de p??gina
    }

    public function confirmaEmail(String $chave){
        $filmesRepository = new FilmesRepositoryPDO();
        $retorno = $filmesRepository->confirmaEmail($chave);
        if($retorno['pass']){
            $_SESSION['msg'] = "E-mail Verificado. Voc?? j?? pode fazer login!";
        }else{
            $_SESSION['msg'] = "E-mail n??o Verificado. Tente novamente";
        }
        header("Location: /login");
    }

    public function atualizarSenha(Array $dados){
        if($dados['tipo'] === 'READ'){
            $filmesRepository = new FilmesRepositoryPDO();
            $retornoDadosUsuario = $filmesRepository->buscaInfoUsuarioPorChave($dados['dados']);
            return $retornoDadosUsuario;
        }else if($dados['tipo'] === 'UPDATE'){
            $filmesRepository = new FilmesRepositoryPDO();
            $retornoDadosUsuario = $filmesRepository->redefinirSenha($dados['dados']);
            return $retornoDadosUsuario;
        }
        
    }

    public function verificaUsuario($user){
        if($_SESSION['usuario'] != "")
            return true;
        else
            return false; 
    }

    public function verificaDadosUsuario($request){
        $filmesRepository = new FilmesRepositoryPDO();
        $dados= (object) $request;

        if($filmesRepository->verificaCadastroExistente($dados->usuario))
            return $dados->usuario;

        if($dados->senha1 == $dados->senha2){
            return true;
        }else{
            return $dados->senha1;
        }
    }

    public function save($tipo, $request){
       
        if($tipo === 'filme'){
            $filmesRepository = new FilmesRepositoryPDO();
            $filme = (object) $request;

            $upload = $this->savePoster($tipo, $_FILES);

            if(gettype($upload)=="string"){
                $filme->poster = $upload;
            }

            if($filmesRepository->salvar($filme))  //Executa a instru????o//
                $_SESSION["msg"] = "Filme cadastrado com sucesso";
            else
                $_SESSION["msg"] = "Erro ao cadastrar filme";

            header("Location: /"); //Redirecionamento de p??gina
        }else if($tipo === 'arquivo'){
            $filmesRepository = new FilmesRepositoryPDO();
            //$filme = (object) $request;

            $upload = $this->savePoster($tipo, $_FILES);

            if(gettype($upload)=="string"){
                if($filmesRepository->importarArquivo($upload))  //Executa a instru????o//
                    $_SESSION["msg"] = "Arquivo importado com sucesso";
                else
                    $_SESSION["msg"] = "Erro ao importar arquivo";

            }else{
                $_SESSION["msg"] = "Erro ao salvar arquivo, tente novamente.";
                header("Location: /syscontrol"); //Redirecionamento de p??gina
            }

            header("Location: /syscontrol"); //Redirecionamento de p??gina
            
        }
    }

    public function favorite(String $ids){ //id usu??rio e id filme
        $id_filme_usuario = unserialize(urldecode($ids));

        $filmesRepository = new FilmesRepositoryPDO();
        
        if(!$filmesRepository->verificaFavorito($id_filme_usuario)){
            $result = ['success' => $filmesRepository->favoritar($id_filme_usuario)];
            header('Content-type: application/json');
            echo json_encode($result);
            return $result;
        }else{
            $result = ['success' => $filmesRepository->desFavoritar($id_filme_usuario)];
            header('Content-type: application/json');
            echo json_encode($result);
            return $result;
        }
    }

    public function assistirMaisTarde(String $ids){
        $id_filme_usuario = unserialize(urldecode($ids));

        $filmesRepository = new FilmesRepositoryPDO();
        if(!$filmesRepository->verificaSalvo($id_filme_usuario)){
            $result = ['success' => $filmesRepository->adicionarAListaSalva($id_filme_usuario)];
            header('Content-type: application/json');
            echo json_encode($result);
            return $result;
        }else{
            $result = ['success' => $filmesRepository->retirarDaListaSalva($id_filme_usuario)];
            header('Content-type: application/json');
            echo json_encode($result);
            return $result;
        }
    }

    //Verifica Favorito 
    public function controlVerificaFavorito(Array $ids):bool{ //id usu??rio e id filme
        $filmesRepository = new FilmesRepositoryPDO();
        return $filmesRepository->verificaFavorito($ids);
    }

    //Verifica salvo 
    public function controlVerificaSalvo(Array $ids):bool{ //id usu??rio e id filme
        $filmesRepository = new FilmesRepositoryPDO();
        return $filmesRepository->verificaSalvo($ids);
    }

    //Deletar
    public function delete(int $id){
        $filmesRepository = new FilmesRepositoryPDO();
        $result = ['success' => $filmesRepository->delete($id)];

        header('Content-type: application/json');
        echo json_encode($result);
    }

    //Editar
    public function edit($request){
       
        $filmesRepository = new FilmesRepositoryPDO();
        $filme = (object) $request;

        $upload = $this->savePoster('filme', $_FILES);

        if(gettype($upload)=="string"){
            $filme->poster = $upload;
        }

        if($filmesRepository->editar($filme))  //Executa a instru????o//
            $_SESSION["msg"] = "Modifica????es efetuadas com sucesso";
        else
            $_SESSION["msg"] = "Erro ao modificar informa????es doe filme";

        header("Location: /syscontrol"); //Redirecionamento de p??gina

    }
    private function savePoster($tipo, $file){
        
        if($tipo === 'filme'){
            $dir = "resources/imagens/posters/";
            $path = $dir.basename($file["poster_file"]["name"]);
            $tmp = $file["poster_file"]["tmp_name"];
        }else{
            $dir = "resources/arquivos/";
            $path = $dir.basename($file["import_file"]["name"]);
            $tmp = $file["import_file"]["tmp_name"];
        }
        
        //$posterPath = $posterDir.basename($file["poster_file"]["name"]);
        //$posterTmp = $file["poster_file"]["tmp_name"];
        
        if (move_uploaded_file($tmp, $path)){
            return $path;
        }else{
            return false;
        }

    }
    //Lista favoritos por usu??rio
    public function fav(array $dados){
        if($dados['pagina'] === 'assistirMaisTarde'){
            $filmesRepository = new FilmesRepositoryPDO();
            return $filmesRepository->listarFavoritos($dados);
        }else if($dados['pagina'] === 'favoritos'){
            $filmesRepository = new FilmesRepositoryPDO();
            return $filmesRepository->listarFavoritos($dados);
        }
        
    }

    public function buscaInfoFilme(array $id){
        $filmesRepository = new FilmesRepositoryPDO();
        if(array_key_exists("pag_syscontrol", $id))
             echo json_encode($filmesRepository->listarInfoFilme($id['pag_syscontrol']));
        else
            return $filmesRepository->listarInfoFilme($id['pag_assistir']);
        //echo json_encode("teste retoornos");
    }

    /*
    P??gina de Controle
    */

    //ConsumoApi pelo titulo da m??dia
    public function ondeBuscar($titulo, $tipo){
        if($tipo == 'api'){
            $consumoApi = new ConsumoApi();
            $dadosTitulosApi = $consumoApi->buscaResultados($titulo);
            echo json_encode($dadosTitulosApi);
        }else{
            $filmesRepository = new FilmesRepositoryPDO();
            $dadosTitulosDestaque = $filmesRepository->buscaResultadosDestaque($titulo);
            //echo json_encode($dadosTitulosApi);
            echo json_encode($dadosTitulosDestaque);
        }
        
    }

    //ConsumoApi busca informa????es pelo id do TMDB
    public function getApiRegisterFilm($searchId){
        $consumoApi = new ConsumoApi();
        $dadosFilmeApi = $consumoApi->buscaInfoFilmesApi($searchId);
        echo json_encode($dadosFilmeApi);
    }

}
