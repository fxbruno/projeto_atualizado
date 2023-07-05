<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '/var/www/html/php0/vendor/autoload.php';

// Verificar se o formulário de cadastro de usuário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["cadastrar_usuario"])) {
    $nome = $_POST["nome"];
    $cpf = $_POST["cpf"];
    $login = $_POST["login"];
    $senha = $_POST["senha"];

    // Criação de uma instância do usuário
    $usuario = new Usuario();

    // Cadastro do usuário no banco de dados
    $resultado = $usuario->cadastrar($nome, $cpf, $login, $senha);

    if ($resultado === true) {
        // Redirecionar para a página de login
        header("Location: gerenciar_clientes.php");
        exit();
    } else {
        echo $resultado;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sistema de Usuários - Cadastro de Usuário</title>
</head>
<body>
    <h2>Cadastrar Usuário</h2>
    <form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <label for="nome">Nome:</label>
        <input type="text" name="nome" required><br><br>

        <label for="cpf">CPF:</label>
        <input type="text" name="cpf" required><br><br>

        <label for="login">Login:</label>
        <input type="text" name="login" required><br><br>

        <label for="senha">Senha:</label>
        <input type="password" name="senha" required><br><br>

        <input type="submit" name="cadastrar_usuario" value="Cadastrar">
    </form>

    <p>Já possui uma conta? <a href="../index.php">Faça login aqui</a>.</p>
</body>
</html>
