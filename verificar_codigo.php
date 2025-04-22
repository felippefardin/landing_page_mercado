<?php
session_start();

// Verifica se o código foi enviado via POST
if (!isset($_POST['codigo'])) {
    // Exibe o formulário para digitar o código
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <title>Verificação de Código - Mercado</title>
        <style>
            body {
                margin: 0;
                padding: 0;
                font-family: Arial, sans-serif;
                background: #f4f4f4;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }
            .verificar-codigo {
                background: white;
                padding: 40px;
                border-radius: 12px;
                box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
                text-align: center;
            }
            .verificar-codigo h2 {
                margin-bottom: 20px;
            }
            .verificar-codigo input {
                padding: 12px;
                width: 100%;
                margin-bottom: 20px;
                border: 1px solid #ccc;
                border-radius: 8px;
                font-size: 16px;
            }
            .verificar-codigo button {
                padding: 12px 24px;
                font-size: 16px;
                background-color: #2a9d8f;
                color: white;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                transition: background-color 0.3s ease;
            }
            .verificar-codigo button:hover {
                background-color: #21867a;
            }
        </style>
    </head>
    <body>
        <section class="verificar-codigo">
            <h2>Verificar Código</h2>
            <form action="verificar_codigo.php" method="POST">
                <input type="text" name="codigo" placeholder="Digite o código recebido" required>
                <button type="submit">Verificar</button>
            </form>
        </section>
    </body>
    </html>
    <?php
    exit;
}

// Se o código foi enviado, processa a verificação
$email = $_SESSION['recupera_email'] ?? null;
$codigo = $_POST['codigo'];

if (!$email) {
    echo "Sessão expirada ou e-mail não definido.";
    exit;
}

$conn = new mysqli("localhost", "root", "", "mercado");

if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

$email = $conn->real_escape_string($email);
$codigo = $conn->real_escape_string($codigo);

$result = $conn->query("SELECT * FROM recuperacoes WHERE email = '$email' AND codigo = '$codigo'");

if ($result && $result->num_rows === 1) {
    $linha = $result->fetch_assoc();
    if (strtotime($linha['expira_em']) >= time()) {
        $_SESSION['validado'] = true;
        header("Location: nova_senha.php");
        exit;
    } else {
        echo "⚠️ Código expirado. Solicite uma nova recuperação.";
    }
} else {
    echo "❌ Código inválido. Verifique e tente novamente.";
}
?>
