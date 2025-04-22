<?php
$conn = new mysqli("localhost", "root", "", "mercado");
$email = $_POST['email'];
$senha = password_hash($_POST['nova_senha'], PASSWORD_DEFAULT);

// Atualiza senha
$conn->query("UPDATE usuarios SET senha = '$senha' WHERE email = '$email'");

// Marca código como usado
$conn->query("UPDATE recuperacao_senha SET usado = 1 WHERE email = '$email'");

echo "Senha atualizada com sucesso!";
?>
