<?php 

session_start();

REQUIRE "./repository/FilmesRepositoryPDO.php";
// REQUIRE "./model/Filme.php";
REQUIRE "./util/ConsumoApi.php";
//REQUIRE "."
REQUIRE "./model/confirmacaoEmail.php";

//Bibblioteca para verificação de email
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'lib/vendor/autoload.php';


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

    public function atualizaDestaques($idNovo, $idAnterior){
         $filmesRepository = new FilmesRepositoryPDO();
         $result = ['success' => $filmesRepository->atualizaDestaques($idNovo, $idAnterior)];
         if($result['success'] === "ok")
            $_SESSION['msg'] = "Destaque Alterado Com Sucesso";
         else 
            $_SESSION['msg'] = "Houve Um Problema Ao Alterar Destaque";

        echo json_encode($result);
    }

    public function validate($request){
        $filmesRepository = new FilmesRepositoryPDO();
        $dados = (object) $request;

        $dadosUsuario = $filmesRepository->validar($dados);
        if(isset($dadosUsuario['situacao']) )//=== 'ativo')  //Executa a instrução verificando se TRUE//
            return $dadosUsuario;
        
            // return false;
        else{
            return false;
        }
    }

    public function saveUser($request){
        $filmesRepository = new FilmesRepositoryPDO();
        $usuario= (object) $request;
        $retorno = $filmesRepository->salvarUsuario($usuario);
        if($retorno['pass']){  //Executa a instrução//
            $chave = $retorno['chave'];
            $confirmaEmail = new ConfirmacaoEmail();
            $result = $confirmaEmail->disparaEmail(['chave'=>$chave, 'usuario'=>$usuario->usuario]);
            
            if($result['msg'] === "ok")
                $_SESSION["msg"] = "Usuário cadastrado. Verifique sua caixa de email antes de logar.";
            else
                $_SESSION['msg'] = "Desculpe, não conseguimos enviar a confirmação. \n Tente novamente.";
        }else
            $_SESSION["msg"] = "Erro ao cadastrar usuário";
        
        header("Location: /login"); //Redirecionamento de página
    }

    public function confirmaEmail($chave){
        $filmesRepository = new FilmesRepositoryPDO();
        $retorno = $filmesRepository->confirmaEmail($chave);
        if($retorno['pass']){
            $_SESSION['msg'] = "E-mail Verificado. Você já pode fazer login!";
        }else{
            $_SESSION['msg'] = "E-mail não Verificado. Tente novamente";
        }
        header("Location: /login");
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

    public function save($request){
       
        $filmesRepository = new FilmesRepositoryPDO();
        $filme = (object) $request;

        $upload = $this->savePoster($_FILES);

        if(gettype($upload)=="string"){
            $filme->poster = $upload;
        }

        if($filmesRepository->salvar($filme))  //Executa a instrução//
            $_SESSION["msg"] = "Filme cadastrado com sucesso";
        else
            $_SESSION["msg"] = "Erro ao cadastrar filme";

        header("Location: /"); //Redirecionamento de página

    }

    private function savePoster($file){
        $posterDir = "imagens/posters/";
        $posterPath = $posterDir.basename($file["poster_file"]["name"]);
        $posterTmp = $file["poster_file"]["tmp_name"];
        
        if (move_uploaded_file($posterTmp, $posterPath)){
            return $posterPath;
        }else{
            return false;
        };

    }

    public function favorite(String $ids){ //id usuário e id filme
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

    //Verifica Favorito na galeria
    public function controlVerificaFavorito(Array $ids):bool{ //id usuário e id filme
        $filmesRepository = new FilmesRepositoryPDO();
        return $filmesRepository->verificaFavorito($ids);
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

        $upload = $this->savePoster($_FILES);

        if(gettype($upload)=="string"){
            $filme->poster = $upload;
        }

        if($filmesRepository->editar($filme))  //Executa a instrução//
            $_SESSION["msg"] = "Modificações efetuadas com sucesso";
        else
            $_SESSION["msg"] = "Erro ao modificar informações doe filme";

        header("Location: /syscontrol"); //Redirecionamento de página

    }
    //Lista favoritos por usuário
    public function fav(int $idUsuario){
        $filmesRepository = new FilmesRepositoryPDO();
        return $filmesRepository->listarFavoritos($idUsuario);
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
    Página de Controle
    */

    //ConsumoApi pelo titulo da mídia
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

    // public function getDestaque($searchTitle){
    //     $filmesRepository = new FilmesRepositoryPDO();
    //     $dadosTitulosDestaque = $filmesRepository->buscaResultadosDestaque($searchTitle);
    //     echo json_encode($dadosTitulosApi);
    // }

    //ConsumoApi busca informações pelo id do TMDB
    public function getApiRegisterFilm($searchId){
        $consumoApi = new ConsumoApi();
        $dadosFilmeApi = $consumoApi->buscaInfoFilmesApi($searchId);
        echo json_encode($dadosFilmeApi);
    }

}

?>