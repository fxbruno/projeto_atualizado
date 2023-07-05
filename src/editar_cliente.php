<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '/var/www/html/php0/vendor/autoload.php';
$servername = "localhost";
$username = "root";
$password = "Batata.2021";
$dbname = "desafio";

session_start();

// Verificar se o ID do usuário está definido na sessão
if (!isset($_SESSION["usuario_id"])) {
    echo "Acesso não autorizado!";
    exit();
}

$usuario_id = $_SESSION["usuario_id"];

// Verificar se o ID do cliente está definido na URL
if (!isset($_GET["id"])) {
    echo "Cliente não encontrado!";
    exit();
}

$cliente_id = $_GET["id"];

try {
    // Inicializar a conexão com o banco de dados usando PDO
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar se o cliente pertence ao usuário atual
    $sql = "SELECT * FROM cliente WHERE id = :cliente_id AND usuario_id = :usuario_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':cliente_id', $cliente_id);
    $stmt->bindParam(':usuario_id', $usuario_id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo "Cliente não encontrado!";
        exit();
    }

    // Obter os dados do cliente
    $cliente = new Cliente($row["id"], $row["endereco_cobranca"], $row["endereco_entrega"], $row["nome"], $row["idade"], $row["email"]);

    // Verificar se o formulário de atualização de cliente foi enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $cliente->setNome($_POST["nome"]);
        $cliente->setIdade($_POST["idade"]);
        $cliente->setEmail($_POST["email"]);
        $cliente->setEnderecoCobranca($_POST["endereco_cobranca"]);
        $cliente->setEnderecoEntrega($_POST["endereco_entrega"]);

        editarCliente($conn, $cliente);

        // Redirecionar para editar_clientes.php
        header("Location: editar_clientes.php?id=" . $cliente->getId());
        exit();
    }
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}

// Função para editar um cliente no banco de dados
function editarCliente($conn, $cliente) {
    // Implemente aqui a lógica para atualizar o cliente no banco de dados
    // Exemplo:
    $sql = "UPDATE cliente SET nome = :nome, idade = :idade, email = :email, endereco_cobranca = :endereco_cobranca, endereco_entrega = :endereco_entrega WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nome', $cliente->getNome());
    $stmt->bindParam(':idade', $cliente->getIdade());
    $stmt->bindParam(':email', $cliente->getEmail());
    $stmt->bindParam(':endereco_cobranca', $cliente->getEnderecoCobranca());
    $stmt->bindParam(':endereco_entrega', $cliente->getEnderecoEntrega());
    $stmt->bindParam(':id', $cliente->getId());
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Cliente</title>
</head>
<body>
    <h1>Editar Cliente</h1>
    <form method="POST" action="<?php echo $_SERVER["PHP_SELF"] . "?id=" . $cliente->getId(); ?>">
        <label for="nome">Nome:</label>
        <input type="text" name="nome" value="<?php echo $cliente->getNome(); ?>" required><br><br>

        <label for="idade">Idade:</label>
        <input type="text" name="idade" value="<?php echo $cliente->getIdade(); ?>" required><br><br>

        <label for="email">Email:</label>
        <input type="email" name="email" value="<?php echo $cliente->getEmail(); ?>" required><br><br>

        <label for="endereco_cobranca">Endereço de Cobrança:</label>
        <input type="text" name="endereco_cobranca" value="<?php echo $cliente->getEnderecoCobranca(); ?>" required><br><br>

        <label for="endereco_entrega">Endereço de Entrega:</label>
        <input type="text" name="endereco_entrega" value="<?php echo $cliente->getEnderecoEntrega(); ?>" required><br><br>

        <input type="submit" value="Atualizar">
    </form>
</body>
</html>
