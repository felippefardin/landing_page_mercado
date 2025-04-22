<?php
require __DIR__ . '/vendor/autoload.php'; // SDK do Mercado Pago

// Defina sua chave de acesso do Mercado Pago (Access Token)
MercadoPago\SDK::setAccessToken('SEU_ACCESS_TOKEN'); // 🔁 Substitua pela sua chave

try {
    // Crie a preferência de pagamento
    $preference = new MercadoPago\Preference();

    $item = new MercadoPago\Item();
    $item->title = 'Computador Completo';
    $item->quantity = 1;
    $item->unit_price = 3000.00;

    $preference->items = [$item];

    // URL para redirecionar após pagamento
    $preference->back_urls = array(
        "success" => "https://seudominio.com/sucesso.php",  // ✅ personalize conforme sua estrutura
        "failure" => "https://seudominio.com/falha.php",
        "pending" => "https://seudominio.com/pendente.php"
    );
    $preference->auto_return = "approved";

    // Salva a preferência
    $preference->save();

    // Redireciona para a página index com o ID gerado
    header("Location: index.html?preference_id=" . $preference->id);
    exit;

} catch (Exception $e) {
    // Em caso de erro, exibe a mensagem de erro
    echo "Erro ao criar a preferência de pagamento: " . $e->getMessage();
}
?>

