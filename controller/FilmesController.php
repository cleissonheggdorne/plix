<?php 

session_start();
REQUIRE "./repository/FilmesRepositoryPDO.php";
REQUIRE "./model/Filme.php";

class FilmesController{
    public function index($itens, $inicio){
        $filmesRepository = new FilmesRepositoryPDO();
        return $filmesRepository->listarTodos($itens, $inicio);
    }

    public function busca($busca){
        $filmesRepository = new FilmesRepositoryPDO();
        return $filmesRepository->listarBusca($busca);
    }

    public function validate($request){
        $filmesRepository = new FilmesRepositoryPDO();
        $dados = (object) $request;

        $dadosUsuario = $filmesRepository->validar($dados);
        var_dump($dadosUsuario);
        if($dadosUsuario != false){  //Executa a instrução verificando se TRUE//
            return $dadosUsuario;
        }else
            return false;
           

    }

    public function saveUser($request){
        $filmesRepository = new FilmesRepositoryPDO();
        $usuario= (object) $request;

        if($filmesRepository->salvarUsuario($usuario))  //Executa a instrução//
            $_SESSION["msg"] = "Usuário cadastrado com sucesso";
        else
            $_SESSION["msg"] = "Erro ao cadastrar usuário";

        header("Location: /login"); //Redirecionamento de página
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

        header("Location: /"); //Redirecionamento de página

    }
    //Lista favoritos por usuário
    public function fav(int $idUsuario){
        $filmesRepository = new FilmesRepositoryPDO();
        return $filmesRepository->listarFavoritos($idUsuario);
    }

    public function pageFilme($id){
        $filmesRepository = new FilmesRepositoryPDO();
        return $filmesRepository->listarInfoFilme($id);
    }
}

?>