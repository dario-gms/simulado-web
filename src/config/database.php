<?php
class Database {
    // Configuração para servidor local
    private $host = "localhost";
    private $db_name = "simulado";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            // Conexão sem porta específica (usará socket)
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name}",
                $this->username, 
                $this->password,
                [
                    PDO::ATTR_PERSISTENT => true,  // Conexões persistentes
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_TIMEOUT => 30,  // Tempo limite aumentado
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                ]
            );
            
            return $this->conn;
        } catch(PDOException $exception) {
            // Log detalhado do erro
            error_log("Erro de conexão MySQL: " . $exception->getMessage());
            throw new Exception("Erro ao conectar ao banco de dados. Tente novamente mais tarde.");
        }
    }
}
?>