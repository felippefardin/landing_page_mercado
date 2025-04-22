<?php
session_start();
$conn = new mysqli("localhost", "root", "", "mercado");

$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $entrada = $conn->real_escape_string($_POST['email_telefone']);

    $resultado = $conn->query("SELECT * FROM usuarios WHERE email = '$entrada' OR telefone = '$entrada'");
    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
        $_SESSION['redefinir_id'] = $usuario['id'];
        header("Location: redefinir_senha.php");
        exit;
    } else {
        $mensagem = "Nenhum usuário encontrado com esse e-mail ou telefone.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Senha</title>
</head>
<body>
    <h2>Esqueci minha senha</h2>
    <?php if ($mensagem) echo "<p style='color:red;'>$mensagem</p>"; ?>
    <form method="POST" action="enviar_codigo.php">
    <label>Digite seu e-mail ou telefone:</label>
    <input type="text" name="usuario" required>
    <button type="submit">Enviar código</button>
</form>

</body>
</html>
