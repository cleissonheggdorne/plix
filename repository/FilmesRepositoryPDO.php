<?php

require "conexao.php";

class FilmesRepositoryPDO{
    
    private $conexao;

    public function __construct()
    {
        $this->conexao = Conexao::criar();
    }
    
    public function validar($dados){
        $sql = "SELECT usuario, id, data_cadastro, admin FROM usuario WHERE usuario = :usuario AND senha= :senha";    
        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(':usuario', $dados->usuario, PDO::PARAM_STR); 
        $stmt->bindValue(':senha', MD5($dados->senha),  PDO::PARAM_STR );
        $stmt->execute();
        
        $dadosRetornados = $stmt->fetch(PDO::FETCH_ASSOC);

        if($stmt->rowCount()){
            return $dadosRetornados;
        }else{
            return false;
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

    public function salvarUsuario($usuario):bool{
        
        $sql = "INSERT INTO usuario(id, usuario, senha, admin, data_cadastro)
        VALUES(:id, :usuario, :senha1, :admin, :data_cadastro)";    
        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(':id', 0, PDO::PARAM_INT);
        $stmt->bindValue(':usuario', $usuario->usuario, PDO::PARAM_STR); 
        $stmt->bindValue(':senha1', MD5($usuario->senha1),  PDO::PARAM_STR );
        $stmt->bindValue(':admin', false,  PDO::PARAM_BOOL);
        $stmt->bindValue(':data_cadastro', date('Y/m/d'),  PDO::PARAM_STR);
        
        return $stmt->execute();

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
    //Listar Busca
    public function listarBusca($busca):array{
       $busca = str_replace(" ", "%", $busca);
        $filmesLista = array(); //array vazio de filmes
        $sql = "SELECT * FROM filmes WHERE titulo LIKE '%$busca%'";
        
        $filmes = $this->conexao->query($sql);
        
        if(!$filmes) return false;

        while($filme = $filmes->fetchObject()){ //fetchobject cria objeto utilizando os mesmos nomes no banco de dados
            array_push($filmesLista, $filme); //lista filmes 1 a 1
        }
        return $filmesLista;
    }

    //salvar filme
    public function salvar($filme):bool{
        
        $sql = "INSERT INTO filmes(titulo, poster, sinopse, nota, trailer, url)
        VALUES(:titulo, :poster, :sinopse, :nota, :trailer, :url)";    
        $stmt = $this->conexao->prepare($sql);

        $stmt->bindValue(':titulo', $filme->titulo, PDO::PARAM_STR); 
        $stmt->bindValue(':poster', $filme->poster,  PDO::PARAM_STR );
        $stmt->bindValue(':sinopse', $filme->sinopse,  PDO::PARAM_STR);
        $stmt->bindValue(':nota', $filme->nota,  PDO::PARAM_STR);
        $stmt->bindValue(':trailer', $filme->trailer,  PDO::PARAM_STR);
        $stmt->bindValue(':url', $filme->player,  PDO::PARAM_STR);
        
        return $stmt->execute();

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

    public function listarFavoritos(int $idUsuario):array{
       
        $favoritosLista = array(); //array vazio de filmes favoritos
        $sql = "SELECT * FROM filmes WHERE id IN (SELECT id_filme FROM filme_favorito WHERE id_usuario = $idUsuario)";
        
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
       
        return $filmeArray;
        
    }

    public function importarArquivo($file){

        ini_set('mas_execution_time', 0); //Arquivo executará até finalzar tempo de processamento
        $sql = "LOAD DATA INFILE '$file' INTO TABLE filmes
        FIELDS TERMINATED BY ';'
        LINES TERMINATED BY '\n'
        IGNORE 1 ROWS";

        if($this->conexao->query($sql))
             return true;
        else
            return false; 


    }
}
