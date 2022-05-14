<?php
// namespace Heggdorne\repository;
require "conexao.php";
// use Heggdorne\repository\Conexao;

class FilmesRepositoryPDO{
    
    private $conexao;

    public function __construct()
    {
        $this->conexao = Conexao::criar();
    }
    
    public function validar($dados){
        $sql = "SELECT usuario, id, data_cadastro, sit_usuario_id, admin FROM usuario WHERE usuario = :usuario AND senha= :senha";    
        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(':usuario', $dados->usuario, PDO::PARAM_STR); 
        $stmt->bindValue(':senha', MD5($dados->senha),  PDO::PARAM_STR );
        $stmt->execute();
        
        $dadosRetornados = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($stmt->rowCount()){
            $sql_sit_user = "SELECT nome_situacao FROM sit_usuario WHERE id = :id_sit";
            $stmt_sit_user = $this->conexao->prepare($sql_sit_user);
            $stmt_sit_user->bindValue(':id_sit',$dadosRetornados['sit_usuario_id'], PDO::PARAM_INT);
            $stmt_sit_user->execute();
            $sit_retornada = $stmt_sit_user->fetch(PDO::FETCH_ASSOC);
            return ['situacao'=> $sit_retornada['nome_situacao'], 'dados'=>$dadosRetornados];
            
        }else{
            return ['situacao'=>false, 'dados'=>false];
        }
        
    }  

    public function verificaCadastroExistente($usuario){
        $sql = "SELECT usuario FROM usuario WHERE usuario = :usuario";    
        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(':usuario', $usuario, PDO::PARAM_STR); 
        $stmt->execute();
        
        $dadosRetornados = $stmt->fetch(PDO::FETCH_ASSOC);

        if($stmt->rowCount()){
            return true;
        }else{
            return false;
        }
    }
    
    public function geraChaveParaRedefinirSenha($usuario){
        $sql = "UPDATE usuario SET chave = :chave WHERE usuario = :usuario";    
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(':usuario', $usuario, PDO::PARAM_STR); 
        $chave = password_hash($usuario.date("Y/m/d H:i:s"), PASSWORD_DEFAULT);
        $stmt->bindValue(':chave', $chave, PDO::PARAM_STR);
        
        return ['pass'=>$stmt->execute(), 'chave'=>$chave];
    }

    public function salvarUsuario($usuario):array{
        
        $sql = "INSERT INTO usuario(id, usuario, senha, admin, data_cadastro, chave)
        VALUES(:id, :usuario, :senha1, :admin, :data_cadastro,:chave)";    
        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(':id', 0, PDO::PARAM_INT);
        $stmt->bindValue(':usuario', $usuario->usuario, PDO::PARAM_STR); 
        $stmt->bindValue(':senha1', MD5($usuario->senha1),  PDO::PARAM_STR );
        $stmt->bindValue(':admin', false,  PDO::PARAM_BOOL);
        $stmt->bindValue(':data_cadastro', date('Y/m/d'),  PDO::PARAM_STR);
        //!!! Important chave pode já existir. Ideal utilizar função recursiva para verificação antes de inserir
        $chave = password_hash($usuario->usuario.date("Y/m/d H:i:s"), PASSWORD_DEFAULT);
        $stmt->bindValue(':chave', $chave, PDO::PARAM_STR);
        
        return ['pass'=>$stmt->execute(), 'chave'=>$chave];

    }

    public function confirmaEmail($chave):array{
        $sql = "SELECT id FROM usuario WHERE chave = :chave LIMIT 1";
        $stmt = $this->conexao->prepare($sql);
        
        $stmt->bindValue(':chave', $chave, PDO::PARAM_STR);
      
        $stmt->execute();
        if(($stmt) and $stmt->rowCount() != 0 ){
            $row_usuario = $stmt->fetch(PDO::PARAM_STR);
            extract($row_usuario);
            
            $sql_up_sit_usuario = "UPDATE usuario SET sit_usuario_id = 1, chave=:chave WHERE id = $id";
            
            $up_sit_usuario = $this->conexao->prepare($sql_up_sit_usuario);
            $chave = NULL;
            $up_sit_usuario->bindValue(':chave', $chave, PDO::PARAM_STR);
            
            if($up_sit_usuario->execute()){
                return ['pass'=> true];
            }else{
                return ['pass'=> false];
            }
        }else{
            return ['pass'=> false];
        }
    }

    public function listarTodos($itens, $inicio):array{
       
        $filmesLista = array(); //array vazio de filmes
        $todos = "SELECT * FROM filmes"; //Todos os registros
        $sql = "SELECT * FROM filmes LIMIT $inicio, $itens"; //Registros pela quantidade de itens da página

        //Retorna conjunto de dados
        $filmes = $this->conexao->query($sql);
        $numRegistros = $this->conexao->query($todos); //Todos Registros
        $nr = $numRegistros->rowCount(); //Retorna número de Registros

        $num_paginas= $nr/$itens; //Número de páginas
        $num_paginas=intVal(ceil($num_paginas)); //Arredonda para cima e transforma em inteiro

        array_push($filmesLista, $num_paginas); //Adiciona a primeira posição do array de filmes para ser passadp para a Galeria
      
        if(!$filmes) return false;

        while($filme = $filmes->fetchObject()){ //fetchobject cria objeto utilizando os mesos nomes no banco de dados
            array_push($filmesLista, $filme); //lista filmes 1 a 1
        }
        return $filmesLista;
    }
    //Lista filmes por genero
    public function listarPorGenero(array $dados):array{
       
        $filmesLista = array(); //array vazio de filmes
        $todosSql = "SELECT * FROM filmes WHERE genero_id= :id_gen"; //Todos os registros
        $limiteSql = "SELECT * FROM filmes WHERE genero_id=:id_gen LIMIT :inicio, :numItens"; //Registros pela quantidade de itens da página

        $todos = $this->conexao->prepare($todosSql);
        $limite = $this->conexao->prepare($limiteSql);
        $todos->bindValue(':id_gen', $dados['id'], PDO::PARAM_INT);
        $limite->bindValue(':id_gen', $dados['id'], PDO::PARAM_INT);
        $limite->bindValue(':inicio', $dados['inicio'], PDO::PARAM_INT);
        $limite->bindValue(':numItens', $dados['numItens'], PDO::PARAM_INT);

        //Retorna conjunto de dados
        $limite->execute();
        $todos->execute(); //Todos Registros
        $nr = $todos->rowCount(); //Retorna número de Registros

        $num_paginas= $nr/$dados['numItens']; //Número de páginas
        $num_paginas=intVal(ceil($num_paginas)); //Arredonda para cima e transforma em inteiro

        array_push($filmesLista, $num_paginas); //Adiciona a primeira posição do array de filmes para ser passadp para a Galeria
      
        if(!$limite) return false;

        while($filme = $limite->fetchObject()){ //fetchobject cria objeto utilizando os mesos nomes no banco de dados
            array_push($filmesLista, $filme); //lista filmes 1 a 1
        }
        return $filmesLista;
    }

    //Listar Busca
    public function listarBusca($busca):array{
        $filmesLista = array(); //array vazio de filmes
        if(array_key_exists('titulo', $busca)){ //Verifica se busca veio pelo título
            $busca = str_replace(" ", "%", $busca['titulo']);
            $sql = "SELECT * FROM filmes WHERE titulo LIKE '%$busca%'"; 
            $retornoExecucaoConsultaFilmes = $this->conexao->query($sql);
            
            if(!$retornoExecucaoConsultaFilmes) return false;
        }else if(array_key_exists('id', $busca)){//Verifica se busca veio pelo id
            $id = intval($busca['id']);
            $sql = "SELECT * FROM filmes WHERE id = $id"; 
            $retornoExecucaoConsultaFilmes = $this->conexao->query($sql);
            if(!$retornoExecucaoConsultaFilmes) return false;
        }
        while($filme = $retornoExecucaoConsultaFilmes->fetchObject()){ //fetchobject cria objeto utilizando os mesmos nomes no banco de dados
            array_push($filmesLista, $filme); //lista filmes 1 a 1
        }
        return $filmesLista;
    }

    //Listar na tela de seleção de destaques
    public function buscaResultadosDestaque($titulo):array{
        //$retornoBusca = array(); //array vazio de filmes
        $busca = str_replace(" ", "%", $titulo);
        $sql = "SELECT id, titulo, poster FROM filmes WHERE titulo LIKE '%$busca%'"; 
        $informacoes = $this->conexao->query($sql);
        //$stmt = $this->conexao->prepare($sql);
        while($dado = $informacoes->fetchObject()){
            $retorno[] = [
                'id' => $dado->id,
                'titulo' => $dado->titulo,
                'poster' => "https://www.themoviedb.org/t/p/original/".$dado->poster
              ];
        }
        $retornoBusca = ['dados'=> $retorno];
        return $retornoBusca;
    }

    //salvar filme
    public function salvar($filme):bool{
        
        $sql = "INSERT INTO filmes(id, titulo, poster, sinopse, nota, trailer, url, genero_id, img_wide_1, img_wide_2, img_wide_3)
        VALUES(:id,:titulo, :poster, :sinopse, :nota, :trailer, :url, :genero_id, :back_1, :back_2, :back_3)";    
        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(':id', 0, PDO::PARAM_INT);
        $stmt->bindValue(':titulo', $filme->titulo, PDO::PARAM_STR); 
        $stmt->bindValue(':poster', $filme->poster,  PDO::PARAM_STR );
        $stmt->bindValue(':sinopse', $filme->sinopse,  PDO::PARAM_STR);
        $stmt->bindValue(':nota', $filme->nota,  PDO::PARAM_STR);
        $stmt->bindValue(':trailer', $filme->trailer,  PDO::PARAM_STR);
        $stmt->bindValue(':url', $filme->player,  PDO::PARAM_STR);
        $stmt->bindValue(':genero_id', null, PDO::PARAM_INT);
        $stmt->bindValue(':back_1', $filme->back_1,  PDO::PARAM_STR);
        $stmt->bindValue(':back_2', $filme->back_2,  PDO::PARAM_STR);
        $stmt->bindValue(':back_3', $filme->back_3,  PDO::PARAM_STR);
         try{
            $stmt->execute();
            return true;
         }catch(PDOException $e){
            echo $e->getMessage();
            return false;
         }
         //return $stmt->execute();

    }

    public function editar($filme):bool{
        
        $sql = "UPDATE filmes
                SET titulo= :titulo,
                    poster= :poster,
                    sinopse= :sinopse,
                    nota= :nota,
                    trailer= :trailer,
                    url= :url
                WHERE id= :id";    
        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(':titulo', $filme->titulo, PDO::PARAM_STR); 
        $stmt->bindValue(':poster', $filme->poster,  PDO::PARAM_STR );
        $stmt->bindValue(':sinopse', $filme->sinopse,  PDO::PARAM_STR);
        $stmt->bindValue(':nota', $filme->nota,  PDO::PARAM_STR);
        $stmt->bindValue(':trailer', $filme->trailer,  PDO::PARAM_STR);
        $stmt->bindValue(':url', $filme->player,  PDO::PARAM_STR);
        $stmt->bindValue(':id', $filme->id,  PDO::PARAM_INT);
        
        return $stmt->execute();

    }

    public function favoritar(Array $id_filme_usuario){

            //$sql = "UPDATE filmes set favorito = NOT favorito WHERE id = :id"; 
            $sql = "INSERT INTO filme_favorito(id_filme, id_usuario)
            VALUES(:id_filme, :id_usuario)";  

            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(':id_filme', $id_filme_usuario['id_filme'], PDO::PARAM_INT); 
            $stmt->bindValue(':id_usuario', $id_filme_usuario['id_usuario'], PDO::PARAM_INT); 
            
            if($stmt->execute()){
                return "ok";
            }else {
                return "erro";
            }
        
    }

    public function adicionarAListaSalva(Array $id_filme_usuario){

        //$sql = "UPDATE filmes set favorito = NOT favorito WHERE id = :id"; 
        $sql = "INSERT INTO filme_salvo(id_filme, id_usuario)
        VALUES(:id_filme, :id_usuario)";  

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(':id_filme', $id_filme_usuario['id_filme'], PDO::PARAM_INT); 
        $stmt->bindValue(':id_usuario', $id_filme_usuario['id_usuario'], PDO::PARAM_INT); 
        
        if($stmt->execute()){
            return "ok";
        }else {
            return "erro";
        }
    
}

    public function verificaFavorito(Array $id_filme_usuario){
        $sql = "SELECT * FROM filme_favorito WHERE id_usuario = :id_usuario AND id_filme= :id_filme";    
        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(':id_usuario', $id_filme_usuario['id_usuario'], PDO::PARAM_INT); 
        $stmt->bindValue(':id_filme', $id_filme_usuario['id_filme'],  PDO::PARAM_INT );
        $stmt->execute();
        
        $dadosRetornados = $stmt->fetch(PDO::FETCH_ASSOC);

        if($stmt->rowCount()){
            return true;
        }else{
            return false;
        }
    }

    public function verificaSalvo(Array $id_filme_usuario){
        $sql = "SELECT * FROM filme_salvo WHERE id_usuario = :id_usuario AND id_filme= :id_filme";    
        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(':id_usuario', $id_filme_usuario['id_usuario'], PDO::PARAM_INT); 
        $stmt->bindValue(':id_filme', $id_filme_usuario['id_filme'],  PDO::PARAM_INT );
        $stmt->execute();
        
        $dadosRetornados = $stmt->fetch(PDO::FETCH_ASSOC);

        if($stmt->rowCount()){
            return true;
        }else{
            return false;
        }
    }

    public function desFavoritar(Array $id_filme_usuario){
        //$sql = "UPDATE filmes set favorito = NOT favorito WHERE id = :id"; 
        $sql = "DELETE FROM filme_favorito
                    WHERE id_filme = :id_filme and id_usuario = :id_usuario";  

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(':id_filme', $id_filme_usuario['id_filme'], PDO::PARAM_INT); 
        $stmt->bindValue(':id_usuario', $id_filme_usuario['id_usuario'], PDO::PARAM_INT); 
        if($stmt->execute()){
            return "ok";
        }else {
            return "erro";
        }
    }

    public function retirarDaListaSalva(Array $id_filme_usuario){
        //$sql = "UPDATE filmes set favorito = NOT favorito WHERE id = :id"; 
        $sql = "DELETE FROM filme_salvo
                    WHERE id_filme = :id_filme and id_usuario = :id_usuario";  

        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(':id_filme', $id_filme_usuario['id_filme'], PDO::PARAM_INT); 
        $stmt->bindValue(':id_usuario', $id_filme_usuario['id_usuario'], PDO::PARAM_INT); 
        if($stmt->execute()){
            return "ok";
        }else {
            return "erro";
        }
    }

    public function delete(int $id){
        $sql = "DELETE FROM filmes WHERE id = :id";    
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT); 
        if($stmt->execute()){
            return "ok";
        }else {
            return "erro";
        }
    }

    public function buscaDestaques(){
        //$sql = "SELECT * FROM busca_destaque";
        $sql = "SELECT id, titulo, img_wide_1 FROM filmes WHERE destaque = true LIMIT 5";
        $stmt = $this->conexao->prepare($sql);
        $retornoDestaques = array();
        try{
            $destaques = $this->conexao->query($sql);
            //$stmt->execute();
         }catch(PDOException $e){
            return $e->getMessage();
         }
         while($dest = $destaques->fetchObject()){
            array_push($retornoDestaques, $dest);
         }
        //  if($stmt->rowCount())
        //     $retornoDestaques = $stmt->fetch(PDO::FETCH_ASSOC);
        return $retornoDestaques;

    }

    // Atualiza Destaque (Slides) na página inicial
    public function atualizaDestaques($idNovo, $idAntigo){
         $sql1 = "UPDATE filmes set destaque = true WHERE id = :idNovo";
         $sql2 = "UPDATE filmes set destaque = false WHERE id = :idAntigo";    
        
         $stmt = $this->conexao->prepare($sql1);
         $stmt2 = $this->conexao->prepare($sql2);
        
         $stmt->bindValue(':idNovo', $idNovo, PDO::PARAM_INT); 
         $stmt2->bindValue(':idAntigo', $idAntigo, PDO::PARAM_INT); 
    
         if($stmt->execute() && $stmt2->execute()){
            return "ok";
         }else {
             return "erro";
         }
    }

    public function listarFavoritos(array $dados):array{
       
        if($dados['pagina'] === 'favoritos'){
            $tabela = 'filme_favorito';
        }else if($dados['pagina'] === 'assistirMaisTarde'){
            $tabela = 'filme_salvo';
        }
        $id = $dados['id'];
        $favoritosLista = array(); //array vazio de filmes favoritos
        $sql = "SELECT * FROM filmes WHERE id IN (SELECT id_filme FROM $tabela WHERE id_usuario = $id)";
        
        $filmes = $this->conexao->query($sql);
        
        if(!$filmes) return false;

        while($filme = $filmes->fetchObject()){ //fetchobject cria objeto utilizando os mesmos nomes no banco de dados
            array_push($favoritosLista, $filme); //lista filmes 1 a 1
        }
        return $favoritosLista;
    }
    
    public function listarInfoFilme($id):array{
       
        $filmeArray = array(); //array vazio de filmes favoritos
        $sql = "SELECT * FROM filmes WHERE id = $id";
        //Retorna conjunto de dados
        $filme = $this->conexao->query($sql);
        
        if(!$filme) return false;

        while($film = $filme->fetchObject()){ //fetchobject cria objeto utilizando os mesmos nomes no banco de dados
            array_push($filmeArray, $film); //lista filmes 1 a 1
        }
       
        return ['dados'=>$filmeArray];
        
    }

    public function importarArquivo($file){

        ini_set('mas_execution_time', 0); //Arquivo executará até finalzar tempo de processamento
        $sql = "LOAD DATA INFILE '$file' INTO TABLE filmes
        FIELDS TERMINATED BY ';'
        LINES TERMINATED BY '\n'
        ";

        if($this->conexao->query($sql))
             return true;
        else
            return false; 
    }

    public function retornaTitulos(){
        $sql = "SELECT titulo from filmes";
        $titulosArray = array();
        $titulos = $this->conexao->query($sql);

        while($titulo = $titulos->fetchObject()){ //fetchobject cria objeto utilizando os mesmos nomes no banco de dados
            array_push($titulosArray, $titulo); //lista filmes 1 a 1
        }
       
        return $titulosArray;
    }

    public function atualizaInfoBack($backs){
        
        foreach($backs as $key => $titulo){
            $sql = "UPDATE filmes set img_wide_1 = :back WHERE titulo = :titulo";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(':back', $key, PDO::PARAM_STR);
            $stmt->bindValue(':titulo', $titulo, PDO::PARAM_STR);
            $stmt->execute();
            
        }
    }
}
