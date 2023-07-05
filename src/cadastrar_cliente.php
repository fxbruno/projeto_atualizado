<?php
//ini_set('display_errors', 1);
//error_reporting(E_ALL);

require_once '/var/www/html/php0/vendor/autoload.php';


session_start();

use DatabaseConnection; // Remova "YourNamespace\" do uso do namespace

// Verificar se o formulário de cadastro de cliente foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $idade = $_POST["idade"];
    $email = $_POST["email"];   
    $endereco_cobranca = $_POST["endereco_cobranca"];
    $endereco_entrega = $_POST["endereco_entrega"];

    // Verificar se o ID do usuário está definido na sessão
    if (!isset($_SESSION["usuario_id"])) {
        echo "Acesso não autorizado!";
        exit();
    }

    $usuario_id = $_SESSION["usuario_id"];

    // Crie um objeto Cliente
    $cliente = new Cliente(null, $nome, $email, $idade, $endereco_cobranca, $endereco_entrega);

    // Inserir o cliente no banco de dados relacionado ao usuário
    $dbConnection = new DatabaseConnection();
    $conn = $dbConnection->getConnection();
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("INSERT INTO cliente (nome, idade, email, endereco_cobranca, endereco_entrega, usuario_id) VALUES (?, ?, ?, ?, ?, ?)");

    $stmt->bindParam(1, $cliente->getNome());
    $stmt->bindParam(3, $cliente->getIdade());
    $stmt->bindParam(2, $cliente->getEmail());
    $stmt->bindParam(4, $cliente->getEnderecoCobranca());
    $stmt->bindParam(5, json_encode($cliente->getEnderecoEntrega()));
    $stmt->bindParam(6, $usuario_id);

    try {
        $stmt->execute();
        header("Location: gerenciar_clientes.php");
        exit();
    } catch (PDOException $e) {
        echo "Erro ao cadastrar cliente: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cadastrar Cliente</title>
</head>
<body>
    <h1>Cadastrar Cliente</h1>
    <form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <label for="nome">Nome:</label>
        <input type="text" name="nome" required><br><br>
        
        <label for="email">Email:</label>
        <input type="email" name="email" required><br><br>
        
        <label for="idade">Idade:</label>
        <input type="number" name="idade" required><br><br>
        
        <label for="endereco_cobranca">Endereço de Cobrança:</label>
        <input type="text" name="endereco_cobranca" required><br><br>
        
        <label for="endereco_entrega">Endereço de Entrega:</label>
        <input type="text" name="endereco_entrega" required><br><br>
        
        <input type="submit" value="Cadastrar">
    </form>
</body>
</html>
