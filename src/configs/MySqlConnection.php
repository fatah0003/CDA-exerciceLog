<?php

namespace src\configs;

use PDO;
use PDOException;

class MySqlConnection
{
    private static string $host = "localhost";
    private static string $db_name = "php";
    private static string $username = "root";
    private static string $password = "";
    private static ?PDO $connection = null;

    private function __construct(){
        try {
            $db = new PDO(
                "mysql:host=". self::$host. ";dbname=". self::$db_name,
                self::$username,
                self::$password
            );

            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "La connexion est établie avec notre BDD!", PHP_EOL;
        } catch (PDOException $e){
            echo "Erreur de connexion : " . $e->getMessage(), PHP_EOL;
            return; // Ne continue pas si la connexion échoue.
        }
        self::$connection = $db;
        self::initTable();
    }

    public static function getConnection(): PDO
    {
        if(self::$connection === null){
            new MySqlConnection();
        }

        return self::$connection;
    }

    private static function initTable(): void{
        $request = "CREATE TABLE IF NOT EXISTS student (
            id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
            firstname VARCHAR(50) NOT NULL, 
            lastname VARCHAR(50) NOT NULL, 
            date_of_birth DATE,
            email VARCHAR(50)
        )";

        self::$connection->exec($request);
    }
}