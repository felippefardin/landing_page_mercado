<?php
session_start();
$conn = new mysqli("localhost", "root", "", "mercado");

if (!isset($_SESSION['recupera_email']) || !isset($_POST['nova_senha'])) {
    die("Requisição inválida.");
}

$email = $_SESSION['recupera_email'];
$senha = password_hash($_POST['nova_senha'], PASSWORD_DEFAULT);

// Atualiza a senha
$conn->query("UPDATE usuarios SET senha = '$senha' WHERE email = '$email'");

// Marca código como usado
$conn->query("UPDATE recuperacao_senha SET usado = 1 WHERE email = '$email'");

session_destroy();

echo "Senha atualizada com sucesso!";
