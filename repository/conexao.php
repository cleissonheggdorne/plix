<?php
// namespace Heggdorne\repository;
class Conexao{
    public static function criar():PDO{
        $env = parse_ini_file(__DIR__.'/../.env');
        $databaseType = $env["databasetype"];
        $database = $env["database"];
        $server = $env["server"];
        $user = $env["user"];
        $pass = $env["pass"];

        if($databaseType === "mysql"){
            $database = "host=$server;dbname=$database";
        }

       // return new PDO("$databaseType:$database", $user, $pass); //PDO objeto que permite acesso a diversos bancos de dados da mesma maneira
       try {
        $conn = new PDO("$databaseType:$database", $user, $pass,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")); //PDO objeto que permite acesso a diversos bancos de dados da mesma maneira
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
        } catch(PDOException $e) {
                echo 'ERROR: ' . $e->getMessage();
        }
    }
}

