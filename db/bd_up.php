<?php
$bd = new SQLite3("filmes.db");

$sql = "DROP TABLE IF EXISTS filmes";

if($bd->exec($sql)) 
    echo "\ntabela filmes apagada\n";

$sql = "CREATE TABLE filmes(
    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    poster VARCHAR(200),
    sinopse TEXT,
    nota DECIMAL(2,1)
    )
";
if($bd->exec($sql)) 
    echo "\ntabela filmes criada\n";
else
    echo "\nErro ao criar tabela filmes\n"; //Executa a instrução

$sql = "INSERT INTO filmes(titulo, poster,sinopse, nota)
    VALUES('Homem Aranha - Sem Volta Pra Casa','https://www.themoviedb.org/t/p/w300/6vVRYbIjDLMMwZJ2jo6enrdN76U.jpg', '', 9.9),
          ( 'Resident Evil - Bem Vindo a Racoon City','https://www.themoviedb.org/t/p/w300/x2juEWrlen956bjp1Z8lEDw68aq.jpg', '', 9.8),
          ( 'Venon - Tempo de Carnificina','https://www.themoviedb.org/t/p/w300/h5UzYZquMwO9FVn15R2eK2itmHu.jpg', '', 9.7),
          ( 'Shang-Chi A Lenda dos 10 Anéis','https://www.themoviedb.org/t/p/original/ArrOBeio968bUuUOtEpKa1teIh4.jpg', '', 9.6)";

if($bd->exec($sql))  //Executa a instrução//
    echo "\nFilmes inseridos com sucesso\n";
else
    echo "\nErro ao inserir filmes\n";

?>
