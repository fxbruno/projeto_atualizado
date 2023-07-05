<?php
require_once '/var/www/html/php0/vendor/autoload.php';

class Cliente {
    private $id;
    private $nome;
    private $idade;    
    private $email;   
    private $endereco_cobranca;
    private $endereco_entrega;

    public function __construct($id, $nome, $idade = null, $email = null, $endereco_cobranca, $endereco_entrega = array()) {
        $this->id = $id;
        $this->nome = $nome;
        $this->idade = $idade;
        $this->email = $email;
        $this->endereco_cobranca = $endereco_cobranca;
        $this->endereco_entrega = $endereco_entrega;
    }

    public function getId() {
        return $this->id;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getIdade() {
        return $this->idade;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getEnderecoCobranca() {
        return $this->endereco_cobranca;
    }

    public function getEnderecoEntrega() {
        return $this->endereco_entrega;
    }

    public function setNome($nome) {
        $this->nome = $nome;
    }

    public function setIdade($idade) {
        $this->idade = $idade;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setEnderecoCobranca($endereco_cobranca) {
        $this->endereco_cobranca = $endereco_cobranca;
    }

    public function setEnderecoEntrega($endereco_entrega) {
        $this->endereco_entrega = $endereco_entrega;
    }

    public function adicionarEnderecoCobranca($endereco) {
        $this->endereco_cobranca[] = $endereco;
    }

    public function adicionarEnderecoEntrega($endereco) {
        $this->endereco_entrega[] = $endereco;
    }

    public function removerEndereco($endereco) {
        $indice = array_search($endereco, $this->endereco_entrega);
        if ($indice !== false) {
            unset($this->endereco_entrega[$indice]);
        }
    }

    public function cadastrar() {
        $dbConnection = new DatabaseConnection();
        $conn = $dbConnection->getConnection();
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("INSERT INTO cliente (id, nome, idade, email, endereco_cobranca, endereco_entrega) VALUES (:id, :nome, :idade, :email, :endereco_cobranca, :endereco_entrega)");

        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':nome', $this->nome);
        $stmt->bindParam(':idade', $this->idade);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':endereco_cobranca', $this->endereco_cobranca);
        $stmt->bindParam(':endereco_entrega', implode(', ', $this->endereco_entrega));

        try {
            $stmt->execute();
            echo "Cliente cadastrado com sucesso!";
        } catch (PDOException $e) {
            echo "Erro ao cadastrar cliente: " . $e->getMessage();
        }
    }
}
