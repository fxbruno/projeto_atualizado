<?php
require_once '/var/www/html/php0/vendor/autoload.php';
//require_once '/var/www/html/php0/src/DatabaseConnection.php';

class Usuario {
    private $conn;
    
    public function __construct() {
        $dbConnection = new DatabaseConnection();
        $dbConnection->connect();
        $this->conn = $dbConnection->getConnection();
    }
    
    public function cadastrar($nome, $cpf, $login, $senha) {
        // Verificar se o login já existe no banco de dados
        $sql = "SELECT id FROM usuario WHERE login = :login";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':login', $login);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return "Login já existe. Por favor, escolha outro.";
        }
        
        // Inserir novo usuário no banco de dados
        $sql = "INSERT INTO usuario (nome, cpf, login, senha) VALUES (:nome, :cpf, :login, :senha)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':cpf', $cpf);
        $stmt->bindParam(':login', $login);
        $stmt->bindParam(':senha', $senha);
        if ($stmt->execute()) {
            return true;
        } else {
            return "Erro ao cadastrar usuário: " . $stmt->errorInfo()[2];
        }
    }
    
    public function autenticar($login, $senha) {
        // Verificar se o login e a senha correspondem a um usuário no banco de dados
        $sql = "SELECT id, nome FROM usuario WHERE login = :login AND senha = :senha";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':login', $login);
        $stmt->bindParam(':senha', $senha);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return [
                'id' => $row['id'],
                'nome' => $row['nome']
            ];
        } else {
            return false;
        }
    }
    
    public function __destruct() {
        $this->conn = null;
    }
}
