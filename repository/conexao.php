<?php

class Conexao{
    public static function criar():PDO{
        $env = parse_ini_file('.env');
        $databaseType = $env["databasetype"];
        $database = $env["database"];
        $server = $env["server"];
        $user = $env["user"];
        $pass = $env["pass"];

        if($databaseType === "mysql"){
            $database = "host=$server;dbname=$database";
        }

        return new PDO("$databaseType:$database", $user, $pass); //PDO objeto que permite acesso a diversos bancos de dados da mesma maneira
    }
}

