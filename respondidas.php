<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "mercado");

$filtro = "";
$where = "WHERE respondida = 1";

if (isset($_GET['busca']) && !empty($_GET['busca'])) {
    $filtro = $conn->real_escape_string($_GET['busca']);
    $where .= " AND (nome LIKE '%$filtro%' OR email LIKE '%$filtro%' OR whatsapp LIKE '%$filtro%')";
}

$limite = 5;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$inicio = ($pagina - 1) * $limite;

$colunasValidas = ['nome', 'email', 'whatsapp', 'data_envio'];
$ordem = in_array($_GET['ordem'] ?? '', $colunasValidas) ? $_GET['ordem'] : 'data_envio';
$direcao = ($_GET['direcao'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';
$novaDirecao = $direcao === 'ASC' ? 'DESC' : 'ASC';

$sql = "SELECT * FROM emails $where ORDER BY $ordem $direcao LIMIT $inicio, $limite";
$resultado = $conn->query($sql);

$totalRegistros = $conn->query("SELECT COUNT(*) AS total FROM emails $where")->fetch_assoc()['total'];
$totalPaginas = ceil($totalRegistros / $limite);

if (isset($_GET['retornar'])) {
    $idRetornar = (int)$_GET['retornar'];
    $conn->query("UPDATE emails SET respondida = 0 WHERE id = $idRetornar");
    header("Location: respondidas.php");
    exit;
}
if (isset($_GET['excluir'])) {
    $idExcluir = (int)$_GET['excluir'];
    $conn->query("DELETE FROM emails WHERE id = $idExcluir");
    header("Location: respondidas.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Mensagens Respondidas</title>
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

    
        .acoes a {
    color: #00a859;
    text-decoration: none;
    padding: 5px 10px;
    border-radius: 5px;    
}

.acoes a:hover {
    background-color: #f1f1f1;
}   
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

    </style>
</head>
<body>

<a href="logout.php" class="logout">Sair</a>
    <h1>Olá, <?= ucwords(strtolower($_SESSION['usuario'])) ?>!</h1>
    <h2>Mensagens Recebidas</h2>

    <form class="busca" method="GET" action="admin.php">
        <input type="text" name="busca" placeholder="Buscar por nome, e-mail ou WhatsApp" value="<?= htmlspecialchars($filtro) ?>">
        <button type="submit">Buscar</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>E-mail</th>
                <th>WhatsApp</th>
                <th>Mensagem</th>
                <th>Data</th>
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
                            <a href="respondidas.php?retornar=<?= $linha['id'] ?>" class="retornar">Não lida</a>
                            <span class="divisor">|</span>
                            <a href="respondidas.php?excluir=<?= $linha['id'] ?>" class="excluir" onclick="return confirm('Tem certeza que deseja excluir esta mensagem?')">Excluir</a>
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
    <a href="admin.php" class="marcar-respondida2">Voltar para Admin</a>
 
    <script>
    // Selecionar/deselecionar todos os checkboxes
    document.getElementById('checkAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
</script>

</body>
</html>