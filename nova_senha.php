<?php
session_start();
if (!isset($_SESSION['validado']) || $_SESSION['validado'] !== true) {
    echo "Acesso não autorizado.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Nova Senha - Mercado</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .nova-senha {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .nova-senha h2 {
            margin-bottom: 24px;
        }

        .nova-senha input {
            width: 100%;
            padding: 12px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
        }

        .nova-senha button {
            padding: 12px 24px;
            font-size: 16px;
            background-color: #2a9d8f;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
        }

        .nova-senha button:hover {
            background-color: #21867a;
        }
    </style>
</head>
<body>
    <section class="nova-senha">
        <h2>Definir Nova Senha</h2>
        <form action="salvar_senha.php" method="POST">
            <input type="password" name="nova_senha" placeholder="Nova senha" required>
            <input type="password" name="confirmar_senha" placeholder="Confirme a nova senha" required>
            <button type="submit">Salvar nova senha</button>
        </form>
    </section>
</body>
</html>
