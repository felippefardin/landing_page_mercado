<?php
$conn = new mysqli("localhost", "root", "", "mercado");

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$mensagem = $_POST['mensagem'] ?? '';

if ($nome && $email && $mensagem) {
    $stmt = $conn->prepare("INSERT INTO emails (nome, email, mensagem, data_envio) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("sss", $nome, $email, $mensagem);
    $stmt->execute();
    $stmt->close();
    header("Location: obrigado.html");
    exit;
} else {
    echo "Por favor, preencha todos os campos.";
}
?>
