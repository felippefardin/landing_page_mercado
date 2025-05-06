<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer/PHPMailer-master/Exception.php';
require 'PHPMailer/PHPMailer-master/PHPMailer.php';
require 'PHPMailer/PHPMailer-master/SMTP.php';

// echo '<pre>';
// print_r($_POST);
// echo '</pre>';

// Conexão com banco
$conn = new mysqli("localhost", "root", "", "mercado");

// Dados do formulário
$email = $_POST['usuario'] ?? '';

$email = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Endereço de e-mail inválido.");
}


// Gera código e validade
$codigo = rand(100000, 999999);
$expira = date('d-m-Y H:i:s', strtotime('+10 minutes'));

// Remove códigos antigos e insere novo
$conn->query("DELETE FROM recuperacao_senha WHERE email = '$email'");
$conn->query("INSERT INTO recuperacao_senha (email, codigo, expira_em) VALUES ('$email', '$codigo', '$expira')");

// Configura o envio com PHPMailer
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';  // Servidor SMTP do Outlook (Hotmail)
    $mail->SMTPAuth = true;
    $mail->Username = 'felippefardin@gmail.com';  // Seu e-mail
    $mail->Password = 'uooypktvklcxktnb';  // Sua senha
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;                      // ou 465 se for SSL

    $mail->setFrom('felippefardin@gmail.com', 'Recuperação de Senha'); // 🔁 Altere
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = 'Seu código de verificação';
    $mail->Body = "<p>Seu código de verificação é: <strong>$codigo</strong></p><p>Ele expira em 10 minutos.</p>";

    $mail->send();

    $_SESSION['recupera_email'] = $email;
    header("Location: verificar_codigo.php");
    exit;

} catch (Exception $e) {
    echo "Erro ao enviar e-mail: {$mail->ErrorInfo}";
}
