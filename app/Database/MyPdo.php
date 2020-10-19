<?php

namespace App\Database;


class MyPdo
{

    protected $databaseName = "kossi_migration";
    protected $databaseHost = "localhost";
    protected $databaseUsername = "chat";
    protected $databasePassword = "test";

    /** @var \PDO */
    protected $pdo;

    protected static $instance; // Contiendra l'instance de notre classe.

    protected function __construct() { } // Constructeur en privé.
    protected function __clone() { } // Méthode de clonage en privé aussi.

    public static function getInstance()
    {
        return "kossi";
        if (!isset(self::$instance)) { // Si on n'a pas encore instancié notre classe.
            self::$instance = new self; // On s'instancie nous-mêmes. :)
            $instance         = self::$instance;
            $databaseName     = $instance->databaseName;
            $databaseHost     = $instance->databaseHost;
            $databaseUsername = $instance->databaseUsername;
            $databasePassword = $instance->databasePassword;

            try {
                $conn = new \PDO("mysql:host=$databaseHost;dbname=$databaseName", $databaseUsername, $databasePassword);
                // set the PDO error mode to exception
                $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                //echo "Connected successfully";
                $instance->pdo = $conn;
            } catch (\PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }
        else {
            $instance = self::$instance;
        }

        return $instance->pdo;
    }
}
