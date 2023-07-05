<?php
require_once '/var/www/html/php0/vendor/autoload.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

$servername = "localhost";
$username = "root";
$password = "Batata.2021";
$dbname = "desafio";

// Verificar se o ID do usuário está definido na sessão
if (!isset($_SESSION["usuario_id"])) {
    echo "Acesso não autorizado!";
    exit();
}

$usuario_id = $_SESSION["usuario_id"];

try {
    // Inicializar a conexão com o banco de dados usando PDO
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obter o nome do usuário
    $sql = "SELECT nome FROM usuario WHERE id = :usuario_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':usuario_id', $usuario_id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $nomeUsuario = $row["nome"];
    } else {
        $nomeUsuario = "Usuário Desconhecido";
    }

    // Função para editar um cliente
    function editarCliente($conn, $cliente) {
        $id = $cliente->getId();
        $nome = $cliente->getNome();
        $endereco_cobranca = $cliente->getEnderecoCobranca();
        $endereco_entrega = $cliente->getEnderecoEntrega();

        $sql = "UPDATE cliente SET nome = :nome, endereco_cobranca = :endereco_cobranca, endereco_entrega = :endereco_entrega WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':endereco_cobranca', $endereco_cobranca);
        $stmt->bindParam(':endereco_entrega', $endereco_entrega);

        if ($stmt->execute()) {
            echo "Cliente atualizado com sucesso!";
        } else {
            echo "Erro ao atualizar cliente: " . $stmt->errorInfo()[2];
        }
    }

    // Função para excluir um cliente
    function excluirCliente($conn, $clienteId) {
        $sql = "DELETE FROM cliente WHERE id = :cliente_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':cliente_id', $clienteId);

        if ($stmt->execute()) {
            echo "Cliente excluído com sucesso!";
        } else {
            echo "Erro ao excluir cliente: " . $stmt->errorInfo()[2];
        }
    }

    // Verificar se o formulário de edição de cliente foi enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["editar_cliente"])) {
        $id = $_POST["id"];
        $nome = $_POST["nome"];
        $email = $_POST["email"];
        $idade = $_POST["idade"];
        $endereco_cobranca = $_POST["endereco_cobranca"];
        $endereco_entrega = $_POST["endereco_entrega"];

        $cliente = new Cliente($id, $nome,$email, $idade, $endereco_cobranca, $endereco_entrega);
        editarCliente($conn, $cliente);
    }

    // Verificar se o ID do cliente para exclusão foi fornecido
    if (isset($_GET["excluir_cliente"])) {
        $clienteId = $_GET["excluir_cliente"];
        excluirCliente($conn, $clienteId);
    }

    // Obter a lista de clientes vinculados ao usuário
    $sql = "SELECT id, nome, idade, email, endereco_cobranca, endereco_entrega FROM cliente WHERE usuario_id = :usuario_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':usuario_id', $usuario_id);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fechar a conexão com o banco de dados
    $conn = null;
} catch(PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gerenciar Clientes</title>
</head>
<body>
<?php
    echo "<p>Bem-vindo, " . $nomeUsuario . "!</p>";
    ?>
    <h1>Gerenciar Clientes</h1>
    <a href="cadastrar_cliente.php">Cadastrar Novo Cliente</a><br><br>
    <?php
    if (count($result) > 0) {
        echo "<table>";
        echo "<tr><th>Nome</th><th>Idade</th><th>Email</th><th>Endereço de Cobrança</th><th>Endereço de Entrega</th><th>Ações</th></tr>";
        foreach ($result as $row) {
            $cliente = new Cliente($row['id'], $row['nome'],$row['email'],$row['idade'], $row['endereco_cobranca'], $row['endereco_entrega']);
            echo "<tr>";
            echo "<td>".$cliente->getNome()."</td>";
            echo "<td>".$row['idade']."</td>";
            echo "<td>".$row['email']."</td>";
            echo "<td>".$cliente->getEnderecoCobranca()."</td>";
            echo "<td>".$cliente->getEnderecoEntrega()."</td>";
            echo "<td><a href='editar_cliente.php?id=".$cliente->getId()."'>Editar</a> | <a href='gerenciar_clientes.php?excluir_cliente=".$cliente->getId()."'>Excluir</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Nenhum cliente encontrado.";
    }
    ?>
</body>
</html>
