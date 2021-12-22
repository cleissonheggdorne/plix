<?php
$bd = new SQLite3("./db/filmes.db");

$sql = "ALTER TABLE filmes ADD COLUMN favorito INT DEFAULT 0";

if($bd->exec($sql)) 
    echo "\ntabela filmes foi alterada com sucesso\n";
else
    echo "\nErro ao alterar tabela filmes\n";