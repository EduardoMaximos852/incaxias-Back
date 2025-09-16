<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$db = "dbincaxias";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Erro: " . $conn->connect_error);

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../public/login.php");
    exit();
}

// Deletar registro
if (isset($_GET['deletar_id'])) {
    $id = (int)$_GET['deletar_id'];
    $conn->query("DELETE FROM cultura WHERE id=$id");
    header("Location: visualizar_cultura.php");
    exit();
}

// Busca
$search = $conn->real_escape_string($_GET['search'] ?? '');
$where = $search ? "WHERE nome LIKE '%$search%' OR categoria LIKE '%$search%'" : "";

$sql = "SELECT id,nome,descricao,endereco,categoria FROM cultura $where ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Visualizar Cultura • InCaxias</title>
    <style>
        /* --- ESTILO COMPARTILHADO --- */
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

        .top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 30px;
            margin-bottom: 25px;
            flex-wrap: wrap
        }

        .voltar,
        .add-btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            transition: .3s;
            white-space: nowrap
        }

        .voltar {
            background: #7C3AED;
            color: #fff
        }

        .voltar:hover {
            background: #6B21A8
        }

        .search-box {
            flex: 1;
            min-width: 250px
        }

        .search-box input {
            width: 100%;
            padding: 10px 14px;
            border-radius: 8px;
            border: none;
            font-size: 15px;
            background: #1f2937;
            color: #E6EDF3
        }

        .add-btn {
            background: #10B981;
            color: #fff;
            font-size: 22px;
            padding: 10px 18px;
            display: flex;
            align-items: center;
            justify-content: center
        }

        .add-btn:hover {
            background: #059669
        }

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
            <div class="search-box">
                <form method="get" action="">
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Buscar">
                </form>
            </div>
            <a class="add-btn" href="cadastrar_cultura.php" title="Adicionar Cultura">+</a>
        </div>

        <h1>Visualizar Cultura</h1>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Endereço</th>
                    <th>Categoria</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['nome']) ?></td>
                            <td><?= nl2br(htmlspecialchars($row['descricao'])) ?></td>
                            <td><?= htmlspecialchars($row['endereco']) ?></td>
                            <td><?= htmlspecialchars($row['categoria']) ?></td>
                            <td>
                                <a href="?deletar_id=<?= $row['id'] ?>" class="btn-delete"
                                    onclick="return confirm('Tem certeza que deseja deletar este registro?');">Deletar</a>
                            </td>
                        </tr>
                    <?php endwhile;
                else: ?>
                    <tr>
                        <td colspan="6" style="text-align:center;color:#9ca3af;">Nenhum registro encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

    </main>
</body>

</html>