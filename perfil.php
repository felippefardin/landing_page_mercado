<?php
session_start();
$conn = new mysqli("localhost", "root", "", "mercado");

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$usuario = $_SESSION['usuario'];

// Buscar os dados do usuário no banco de dados
$query = "SELECT * FROM usuarios WHERE nome = '$usuario'";
$result = $conn->query($query);
$dadosUsuario = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Atualizar os dados
    $novoNome = $_POST['nome'];
    $novoEmail = $_POST['email'];
    $novoWhatsapp = $_POST['whatsapp'];

    // Se o usuário quiser mudar a senha
    if (!empty($_POST['senha'])) {
        $novaSenha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
        $queryUpdate = "UPDATE usuarios SET nome = '$novoNome', email = '$novoEmail', whatsapp = '$novoWhatsapp', senha = '$novaSenha' WHERE nome = '$usuario'";
    } else {
        $queryUpdate = "UPDATE usuarios SET nome = '$novoNome', email = '$novoEmail', whatsapp = '$novoWhatsapp' WHERE nome = '$usuario'";
    }

    if ($conn->query($queryUpdate)) {
        // Atualiza a sessão com o novo nome
        $_SESSION['usuario'] = $novoNome;
        header("Location: perfil.php");
        exit;
    } else {
        $erro = "Erro ao atualizar as informações.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Perfil - Mercado</title>
    <link rel="stylesheet" href="css/perfil.css">
</head>
<body>
    <style>
        body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f9;
    margin: 0;
    padding: 0;
}

.container {
    width: 100%;
    max-width: 400px;
    margin: 50px auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 5px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

h2 {
    text-align: center;
    color: #333;
}

form {
    display: flex;
    flex-direction: column;
}

input {
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
}

button {
    background-color: #00a859;
    color: white;
    padding: 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
}

button:hover {
    background-color: #007d42;
}

.error {
    color: red;
    text-align: center;
    margin-bottom: 20px;
}
input[type="text"] {
    font-size: 16px;
}


    </style>
    <div class="container">
        <h2>Perfil de <?= htmlspecialchars($dadosUsuario['nome']) ?></h2>

        <?php if (isset($erro)) : ?>
            <p class="error"><?= $erro ?></p>
        <?php endif; ?>

        <form method="POST" action="perfil.php">
            <label for="nome">Nome:</label>
            <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($dadosUsuario['nome']) ?>" required>

            <label for="email">E-mail:</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($dadosUsuario['email']) ?>" required>

            <label for="whatsapp">WhatsApp:</label>
            <input type="text" name="whatsapp" id="whatsapp" value="<?= isset($dadosUsuario['whatsapp']) ? htmlspecialchars($dadosUsuario['whatsapp']) : '' ?>" placeholder="Ex: (11) 98765-4321">


            <label for="senha">Nova Senha (se desejar mudar):</label>
            <input type="password" name="senha" id="senha">

            <button type="submit">Salvar Alterações</button>
        </form>

        <p><a href="admin.php">Voltar ao painel</a></p>
        <!-- Botão para excluir o perfil -->
       <a href="excluir-perfil.php" class="btn-excluir">Excluir Perfil</a>

    </div>
</body>
</html>
