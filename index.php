<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once '/var/www/html/php0/vendor/autoload.php';

session_start();

// Verificar se o formulário de login foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login_usario"])) {
    $login = $_POST["login"];
    $senha = $_POST["senha"];

    // Criação de uma instância do usuário
    $usuario = new Usuario($servername, $username, $password, $dbname);

    // Realizar a autenticação do usuário
    $dadosUsuario = $usuario->autenticar($login, $senha);
    if ($dadosUsuario) {
        // Definir a variável de sessão para armazenar o ID do usuário logado
        $_SESSION["usuario_id"] = $dadosUsuario['id'];

        // Redirecionar para a página de cadastro de clientes
        header("Location: src/gerenciar_clientes.php");
        exit();
    } else {
        echo "Login inválido!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sistema de usuários</title>
</head>
<body>
    <h1>Sistema de usuários</h1>

    <h2>Login</h2>
    <form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <label for="login">Login:</label>
        <input type="text" name="login" required><br><br>

        <label for="senha">Senha:</label>
        <input type="password" name="senha" required><br><br>

        <input type="submit" name="login_usario" value="Login">
    </form>

    <p>Não tem uma conta? <a href="src/cadastro_usuario.php">Cadastre-se aqui</a>.</p>
</body>
</html>
