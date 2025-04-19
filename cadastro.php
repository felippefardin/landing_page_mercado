<?php
session_start();

$conn = new mysqli("localhost", "root", "", "mercado");

if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

$erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = mysqli_real_escape_string($conn, $_POST['nome']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $senha = mysqli_real_escape_string($conn, $_POST['senha']);
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    $sql = "SELECT id FROM usuarios WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $erro = "Este e-mail já está cadastrado!";
    } else {
        $sql_insert = "INSERT INTO usuarios (nome, email, senha) VALUES ('$nome', '$email', '$senha_hash')";
        
        if ($conn->query($sql_insert) === TRUE) {
            $_SESSION['msg'] = "Cadastro realizado com sucesso!";
            header("Location: login.php");
            exit;
        } else {
            $erro = "Erro ao cadastrar o usuário. Tente novamente.";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Mercado</title>
    <link rel="stylesheet" href="cadastro.css">
</head>
<body>
    <div class="container">
        <h2>Cadastro</h2>

        <?php if ($erro) : ?>
            <div class="error"><?= $erro ?></div>
        <?php endif; ?>

        <form method="POST" action="cadastro.php">
            <input type="text" name="nome" placeholder="Seu nome" required>
            <input type="email" name="email" placeholder="Seu e-mail" required>
            <input type="password" name="senha" placeholder="Sua senha" required>
            <button type="submit">Cadastrar</button>
        </form>

        <p>Já tem uma conta? <a href="login.php">Faça login</a></p>
    </div>
</body>
</html>
