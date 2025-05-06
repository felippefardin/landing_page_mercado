<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    
    exit;
}
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';


$conn = new mysqli("localhost", "root", "", "mercado");

$usuario = $_SESSION['usuario'];

// Busca os dados do usuário
$query = "SELECT * FROM usuarios WHERE nome = '$usuario'";
$result = $conn->query($query);
$dados = $result->fetch_assoc();

if (!$dados) {
    echo "Usuário não encontrado.";
    exit;
}

$email = $dados['email'];
$codigo = rand(100000, 999999);

// Salva o código temporariamente em sessão
$_SESSION['codigo_exclusao'] = $codigo;

// Envia o código por e-mail usando PHPMailer
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Servidor SMTP do Gmail
    $mail->SMTPAuth = true;
    $mail->Username = 'felippefardin@gmail.com'; // Substitua
    $mail->Password = 'hsrl msfk xesx vjjc'; // Senha do App, não a sua senha comum
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('SEUEMAIL@gmail.com', 'Mercado');
    $mail->addAddress($email, $usuario);

    $mail->isHTML(true);
    $mail->Subject = 'Código de confirmação para exclusão de conta';
    $mail->Body = "Olá <strong>$usuario</strong>,<br><br>Seu código de verificação é: <strong>$codigo</strong>.<br>Digite esse código para confirmar a exclusão do seu perfil.";

    $mail->send();

    echo "<form method='POST' action='confirmar-exclusao.php'>
            <label>Digite o código enviado para seu e-mail:</label><br>
            <input type='text' name='codigo_digitado' required>
            <button type='submit'>Confirmar Exclusão</button>
        </form>";

} catch (Exception $e) {
    echo "Erro ao enviar e-mail: {$mail->ErrorInfo}";
}
?>
