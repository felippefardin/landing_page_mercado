<?php
session_start();
$conn = new mysqli("localhost", "root", "", "mercado");

if (!isset($_SESSION['usuario']) || !isset($_SESSION['codigo_exclusao'])) {
    header("Location: login.php");
    exit;
}

$codigo_digitado = $_POST['codigo_digitado'];
$codigo_gerado = $_SESSION['codigo_exclusao'];
$usuario = $_SESSION['usuario'];

if ($codigo_digitado == $codigo_gerado) {
    $conn->query("DELETE FROM usuarios WHERE nome = '$usuario'");
    session_destroy();
    echo "Perfil excluído com sucesso.";
    echo "<br><a href='login.php'>Voltar ao login</a>";
} else {
    echo "Código incorreto. Tente novamente.";
}
?>
