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

    public function listarTodos():array{
       
        $filmesLista = array(); //array vazio de filmes
        $sql = "SELECT * FROM filmes";
        //Retorna conjunto de dados
        $filmes = $this->conexao->query($sql);
        
        if(!$filmes) return false;

        while($filme = $filmes->fetchObject()){ //fetchobject cria objeto utilizando os mesos nomes no banco de dados
            array_push($filmesLista, $filme); //lista filmes 1 a 1
        }
        return $filmesLista;
    }

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
        //Retorna conjunto de dados
       // $stmt = $this->conexao->prepare($sql);
       // $stmt->bindValue(':id_usuario', $idUsuario, PDO::PARAM_INT); 
       // $stmt->execute();
        //$filmes = $stmt->fetch(PDO::FETCH_ASSOC);
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
}
