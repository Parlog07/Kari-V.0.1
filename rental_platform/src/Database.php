<?php 
class Database{

    private string $host = "localhost";
    private string $dbname = "rental_platform";
    private string $username = "root";
    private string $password = "";

    private PDO $pdo;

    public function __construct()
    {
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";

        try {
            $this->pdo = new PDO($dsn, $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed");
        }
    }
    public function getConnection(): PDO
    {
        return $this->pdo;
    }


};