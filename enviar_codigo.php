<?php
session_start();
echo "Email recebido: " . $_POST['usuario'];


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer/PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer/PHPMailer.php';
require 'PHPMailer/PHPMailer/SMTP.php';

// echo '<pre>';
// print_r($_POST);
// echo '</pre>';

// Conexão com banco
$conn = new mysqli("localhost", "root", "", "mercado");

// Dados do formulário
$email = $_POST['usuario'] ?? '';

$email = trim($email);


if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Endereço de e-mail inválido.");
}


// Gera código e validade
$codigo = rand(100000, 999999);
$expira = date('d-m-Y H:i:s', strtotime('+10 minutes'));

// Remove códigos antigos e insere novo
$stmt = $conn->prepare("DELETE FROM recuperacao_senha WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();

$stmt = $conn->prepare("INSERT INTO recuperacao_senha (email, codigo, expira_em) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $email, $codigo, $expira);
$stmt->execute();


// Configura o envio com PHPMailer
$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Mostra debug completo
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'felippefardin@gmail.com';
    $mail->Password = 'uooy pktv klcx ktnb';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('felippefardin@gmail.com', 'Recuperação de Senha');
    $mail->addAddress($email); // Verifique o valor de $email
    $mail->isHTML(true);
    $mail->Subject = 'Seu código de verificação';
    $mail->Body = "<p>Seu código de verificação é: <strong>$codigo</strong></p><p>Ele expira em 10 minutos.</p>";

    echo "<pre>";
    echo "Preparando para enviar e-mail para: $email\n";
    echo "Código gerado: $codigo\n";
    echo "Expira em: $expira\n";
    echo "</pre>";

    $mail->send();

    $_SESSION['recupera_email'] = $email;
    header("Location: verificar_codigo.php");
    exit;
} catch (Exception $e) {
    echo "<p><strong>Erro ao enviar e-mail:</strong> {$mail->ErrorInfo}</p>";
    echo "<pre>";
    print_r($e);
    echo "</pre>";
}
