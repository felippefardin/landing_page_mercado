<?php
session_start();
$conn = new mysqli("localhost", "root", "", "mercado");



if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_SESSION['recupera_email'];
    $codigo = $_POST['codigo'];

    $sql = "SELECT * FROM recuperacao_senha 
            WHERE email = ? AND codigo = ? AND usado = 0 AND expira_em > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $codigo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $_SESSION['validado'] = true;
        header("Location: nova_senha.php");
        exit;
    } else {
        $erro = "Código inválido ou expirado.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head><meta charset="UTF-8"><title>Verificar Código</title></head>
<body>
    <h2>Digite o Código</h2>
    <?php if (isset($erro)) echo "<p style='color:red;'>$erro</p>"; ?>
    <form method="POST">
        <input type="text" name="codigo" placeholder="Código de 6 dígitos" required>
        <button type="submit">Verificar</button>
    </form>
</body>
</html>
