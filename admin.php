<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "mercado");

$filtro = "";
$where = "";

if (isset($_GET['busca']) && !empty($_GET['busca'])) {
    $filtro = $conn->real_escape_string($_GET['busca']);
    $where = "WHERE nome LIKE '%$filtro%' OR email LIKE '%$filtro%' OR whatsapp LIKE '%$filtro%'";
}

$limite = 5;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$inicio = ($pagina - 1) * $limite;

$colunasValidas = ['nome', 'email', 'whatsapp', 'data_envio'];
$ordem = in_array($_GET['ordem'] ?? '', $colunasValidas) ? $_GET['ordem'] : 'data_envio';
$direcao = ($_GET['direcao'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';
$novaDirecao = $direcao === 'ASC' ? 'DESC' : 'ASC';

$sql = "SELECT * FROM emails WHERE respondida = 0 ORDER BY data_envio DESC LIMIT $inicio, $limite";

$resultado = $conn->query($sql);

$totalRegistros = $conn->query("SELECT COUNT(*) AS total FROM emails $where")->fetch_assoc()['total'];
$totalPaginas = ceil($totalRegistros / $limite);

if (isset($_GET['excluir'])) {
    $idExcluir = (int)$_GET['excluir'];
    $conn->query("DELETE FROM emails WHERE id = $idExcluir");
    header("Location: admin.php");
    exit;
}
if (isset($_GET['marcar_respondida'])) {
    $idRespondida = (int)$_GET['marcar_respondida'];
    
    // Atualiza o campo 'respondida' para 1
    if ($conn->query("UPDATE emails SET respondida = 1 WHERE id = $idRespondida")) {
        echo "Mensagem marcada como respondida!";
    } else {
        echo "Erro ao atualizar a mensagem.";
    }
    
    // Após a atualização, redireciona para a mesma página para atualizar a lista
    header("Location: admin.php?pagina=$pagina&busca=" . urlencode($filtro));

    exit;
}




?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel Administrativo</title>
    <link rel="shortcut icon" href="img/atalho.png">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            padding: 20px;
        }
        h1 {
            color: #00a859;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
            vertical-align: middle;
        }
        th {
            background: #00a859;
            color: white;
        }
        tr:nth-child(even) {
            background: #f2f2f2;
        }

        th a {
            color: inherit;
            text-decoration: none;
            font-weight: bold;
        }

        th a:hover {
            text-decoration: underline;
            color: #fff;
        }

        .logout {
            float: right;
            background: #ff4c4c;
            color: white;
            padding: 8px 12px;
            text-decoration: none;
            border-radius: 5px;
        }

        .busca {
            margin-top: 20px;
        }
        .busca input[type="text"] {
            padding: 8px;
            width: 250px;
        }
        .busca button {
            padding: 8px 12px;
            background: #00a859;
            color: white;
            border: none;
            border-radius: 4px;
        }

        .acoes {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .excluir, .responder {
            color: #00a859;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
        }

        .excluir:hover, .responder:hover {
            background-color: #f1f1f1;
        }

        .paginacao {
            margin-top: 20px;
        }
        .paginacao a {
            margin: 0 5px;
            padding: 6px 10px;
            background: #eee;
            text-decoration: none;
            color: #333;
            border-radius: 4px;
        }
        .paginacao a.ativa {
            background: #00a859;
            color: white;
        }

        /* Estilos para os botões de exportação */
        .busca button:hover {
            opacity: 0.8;
        }

        .acoes a:hover {
            background-color: #f1f1f1;
        }

        .exportar-button {
            padding: 8px 12px;
            border-radius: 4px;
            text-decoration: none;
            color: white;
        }

        .excel {
            background: #0077cc;
        }

        .excel:hover {
            background: #006bb3;
        }

        .pdf {
            background: #cc0000;
        }

        .pdf:hover {
            background: #b30000;
        }

        .csv {
            background: #00a859;
        }

        .csv:hover {
            background: #009848;
        }
        .marcar-respondida {
    color: #00a859;
    text-decoration: none;
    padding: 5px 10px;
    border-radius: 5px;
}

.marcar-respondida:hover {
    background-color: #f1f1f1;
}
/* Estilos para centralizar o botão */


.marcar-respondida2 {
    display: inline-block;
    background-color: #00a859;
    color: white;
    text-decoration: none;
    padding: 10px 20px;
    border-radius: 5px;
    text-align: center;
    font-weight: bold;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: background-color 0.3s ease, transform 0.3s ease;
}

.marcar-respondida2:hover {
    background-color: #009a4f;
    transform: translateY(-2px);
}
.marcar-respondida2:active {
    background-color: #007d3e;
    transform: translateY(0);
}
.marcar-respondida2 {
    display: inline-block;
    background-color: #00a859;
    color: white;
    padding: 8px 12px;
    text-decoration: none;
    border-radius: 5px;
    margin-top: 20px;
    text-align: center;
}

.marcar-respondida2 {
    background-color: #008c44;
}
/* Estilo para o botão "Ver Perfil" */
.btn-perfil {
    display: inline-block;
    background-color:  #007d3e; /* Cor do botão */
    color: white;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
    margin-top: 20px;
    transition: background-color 0.3s ease;
}

.btn-perfil:hover {
    background-color: #45a049; /* Cor ao passar o mouse */
}



    </style>
</head>
<body>

    <a href="logout.php" class="logout">Sair</a>
    <h1>Bem-vindo, <?= ucwords(strtolower($_SESSION['usuario'])) ?>!</h1>
    <h2>Mensagens Recebidas</h2>

    <form class="busca" method="GET" action="admin.php">
        <input type="text" name="busca" placeholder="Buscar por nome, e-mail ou WhatsApp" value="<?= htmlspecialchars($filtro) ?>">
        <button type="submit">Buscar</button>
    </form>

    <div style="margin-top: 10px;">
        <form method="GET" action="exportar_excel.php" style="display:inline;">
            <button type="submit" class="exportar-button excel">Exportar Excel</button>
        </form>

        <form method="GET" action="exportar_pdf.php" style="display:inline;">
            <button type="submit" class="exportar-button pdf">Exportar PDF</button>
        </form>

        <form method="GET" action="exportar_csv.php" style="display:inline;">
            <button type="submit" class="exportar-button csv">Exportar CSV</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th><a href="?ordem=nome&direcao=<?= $novaDirecao ?>&busca=<?= urlencode($filtro) ?>">Nome</a></th>
                <th><a href="?ordem=email&direcao=<?= $novaDirecao ?>&busca=<?= urlencode($filtro) ?>">E-mail</a></th>
                <th><a href="?ordem=whatsapp&direcao=<?= $novaDirecao ?>&busca=<?= urlencode($filtro) ?>">WhatsApp</a></th>
                <th>Mensagem</th>
                <th><a href="?ordem=data_envio&direcao=<?= $novaDirecao ?>&busca=<?= urlencode($filtro) ?>">Data</a></th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($linha = $resultado->fetch_assoc()) : ?>
                <tr>
                    <td><?= htmlspecialchars($linha['nome']) ?></td>
                    <td><?= htmlspecialchars($linha['email']) ?></td>
                    <td><?= htmlspecialchars($linha['whatsapp']) ?></td>
                    <td><?= nl2br(htmlspecialchars($linha['mensagem'])) ?></td>
                    <td><?= $linha['data_envio'] ?></td>
                    <td>
                            <div class="acoes">
                                <a href="mailto:<?= htmlspecialchars($linha['email']) ?>?subject=Resposta%20da%20mensagem%20no%20Mercado&body=Olá%20<?= urlencode($linha['nome']) ?>,%0D%0A%0D%0AObrigado%20pela%20mensagem!%20Segue%20abaixo%20nossa%20resposta:%0D%0A%0D%0A" class="responder">Responder</a>
                                <span class="divisor">|</span>
                                <?php if ($linha['respondida'] == 0): ?>
                                    <a href="admin.php?marcar_respondida=<?= $linha['id'] ?>" class="marcar-respondida">Marcar como Respondida</a>
                                <?php endif; ?>
                                <span class="divisor">|</span>
                                <a class="excluir" href="admin.php?excluir=<?= $linha['id'] ?>" onclick="return confirm('Deseja realmente excluir?')">Excluir</a>
                            </div>
                        </td>

                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="paginacao">
        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
            <a class="<?= $i === $pagina ? 'ativa' : '' ?>" href="?pagina=<?= $i ?>&busca=<?= urlencode($filtro) ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
    <!-- Botão para redirecionar para a página de Respondidas -->
<div class=".ir-para-respondidas centralizar-botao">
    <a href="respondidas.php" class="marcar-respondida2">Ir para Mensagens Respondidas</a>
</div>
<!-- Botão para acessar o perfil -->
<a href="perfil.php" class="btn-perfil">Ver Perfil</a>




</body>
</html>
