<?php
session_start();

// Conexão
$host = "localhost";
$user = "root";
$pass = "";
$db   = "dbincaxias";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Erro na conexão: " . $conn->connect_error);

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../public/login.php");
    exit();
}

// Deletar
if (isset($_GET['deletar_id'])) {
    $id = (int)$_GET['deletar_id'];
    $res = $conn->query("SELECT caminho FROM ponto_imagens WHERE ponto_id=$id");
    while ($img = $res->fetch_assoc()) {
        $p = 'uploads/pontos/' . $img['caminho'];
        if (file_exists($p)) unlink($p);
    }
    $conn->query("DELETE FROM ponto_imagens WHERE ponto_id=$id");
    $conn->query("DELETE FROM pontos_turisticos WHERE id=$id");
    header("Location: visualizar_turismo.php");
    exit();
}

// Filtro de busca
$search = $conn->real_escape_string($_GET['search'] ?? '');
$where  = $search ? "WHERE p.nome LIKE '%$search%' OR p.categoria LIKE '%$search%'" : '';

$sql = "
 SELECT p.id, p.nome, p.descricao, p.endereco, p.categoria,
        GROUP_CONCAT(i.caminho SEPARATOR ',') AS imagens
 FROM pontos_turisticos p
 LEFT JOIN ponto_imagens i ON p.id = i.ponto_id
 $where
 GROUP BY p.id
 ORDER BY p.id DESC
";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Visualizar Turismo • InCaxias</title>
    <style>
        body {
            font-family: Inter, Arial, sans-serif;
            background: #111827;
            color: #E6EDF3;
            margin: 0
        }

        .main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px
        }

        h1 {
            text-align: center;
            margin: 20px 0;
            color: #E6EDF3;
            font-size: 28px
        }

        /* === Barra Superior === */
        .top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 30px;
            /* espaço uniforme entre voltar / busca / + */
            margin-bottom: 25px;
            flex-wrap: wrap;
            /* se ficar estreito em telas pequenas, quebra em linhas */
        }

        .voltar {
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            background: #7C3AED;
            color: #fff;
            transition: .3s;
            white-space: nowrap;
        }

        .voltar:hover {
            background: #6B21A8
        }

        .search-box {
            flex: 1;
            min-width: 250px;
            /* garante largura mínima */
        }

        .search-box input {
            width: 100%;
            padding: 14px 14px;
            border-radius: 18px;
            border: none;
            font-size: 15px;
            background: #1f2937;
            color: #E6EDF3;
        }

        .add-btn {
            background: #10B981;
            color: #fff;
            font-size: 22px;
            padding: 10px 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            transition: .3s;
            white-space: nowrap;
        }

        .add-btn:hover {
            background: #059669
        }

        /* === Tabela === */
        table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, .3);
            background: #1f2937
        }

        th,
        td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, .1);
            font-size: 14px;
            color: #E6EDF3;
            vertical-align: top
        }

        th {
            background: #7C3AED;
            color: #fff;
            text-transform: uppercase
        }

        tr:hover {
            background: rgba(124, 58, 236, .1)
        }

        img {
            max-width: 80px;
            margin: 2px;
            border-radius: 6px;
            transition: transform .3s
        }

        img:hover {
            transform: scale(1.25)
        }

        .btn-delete {
            padding: 8px 18px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            background: #10B981;
            color: #fff;
            transition: .3s;
            text-decoration: none
        }

        .btn-delete:hover {
            background: #059669
        }
    </style>
</head>

<body>
    <main class="main">

        <div class="top-bar">
            <a class="voltar" href="dashboard.php">← Voltar</a>

            <!-- Barra de busca -->
            <div class="search-box">
                <form method="get" action="">
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Buscar">
                </form>
            </div>

            <!-- Botão de adicionar -->
            <a class="add-btn" href="cadastrar_turismo.php" title="Adicionar Ponto Turístico">+</a>
        </div>

        <h1>Visualizar Pontos Turísticos</h1>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Endereço</th>
                    <th>Categoria</th>
                    <th>Imagens</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['nome']) ?></td>
                            <td><?= nl2br(htmlspecialchars($row['descricao'])) ?></td>
                            <td><?= htmlspecialchars($row['endereco']) ?></td>
                            <td><?= htmlspecialchars($row['categoria']) ?></td>
                            <td>
                                <?php if ($row['imagens']) {
                                    foreach (explode(',', $row['imagens']) as $img) {
                                        echo "<img src='uploads/pontos/" . htmlspecialchars($img) . "' alt='Imagem'>";
                                    }
                                } else {
                                    echo "Sem imagens";
                                } ?>
                            </td>
                            <td>
                                <a href="?deletar_id=<?= $row['id'] ?>" class="btn-delete"
                                    onclick="return confirm('Tem certeza que deseja deletar este ponto turístico?');">
                                    Deletar
                                </a>
                            </td>
                        </tr>
                    <?php endwhile;
                else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center;color:#9ca3af;">Nenhum ponto encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

    </main>
</body>

</html>