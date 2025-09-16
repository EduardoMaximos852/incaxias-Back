<?php
session_start();

// Conexão com o banco
$host = "localhost";
$user = "root";
$pass = "";
$db   = "dbincaxias";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Erro na conexão: " . $conn->connect_error);

// Verifica login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../public/login.php");
    exit();
}

// Pega dados do usuário
$usuario_nome = $_SESSION['usuario_nome'] ?? '';
$usuario_email = $_SESSION['usuario_email'] ?? '';
$usuario_nivel = $_SESSION['usuario_nivel'] ?? 'usuario';

// ==================
// Deletar ponto turístico
// ==================
if (isset($_GET['deletar_id'])) {
    $id = (int)$_GET['deletar_id'];

    // Pega todas as imagens do ponto
    $res = $conn->query("SELECT caminho FROM ponto_imagens WHERE ponto_id = $id");
    while ($img = $res->fetch_assoc()) {
        $imgPath = 'uploads/pontos/' . $img['caminho'];
        if (file_exists($imgPath)) unlink($imgPath);
    }

    // Deleta as imagens da tabela
    $conn->query("DELETE FROM ponto_imagens WHERE ponto_id = $id");

    // Deleta o ponto turístico
    $conn->query("DELETE FROM pontos_turisticos WHERE id = $id");

    // Redireciona para a mesma página
    header("Location: visualizar_turismo.php");
    exit();
}

// ==================
// Buscar todos os pontos turísticos com imagens
// ==================
$sql = "
SELECT p.id, p.nome, p.descricao, p.endereco, p.categoria, GROUP_CONCAT(i.caminho SEPARATOR ',') AS imagens
FROM pontos_turisticos p
LEFT JOIN ponto_imagens i ON p.id = i.ponto_id
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
    <title>Visualizar Pontos Turísticos • InCaxias</title>
    <link rel="stylesheet" href="../css/incaxias.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #111827;
            /* Fundo escuro */
            color: #E6EDF3;
            /* Texto claro */
            margin: 0;
        }

        .main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 25px;
            color: #E6EDF3;
            font-size: 28px;
        }

        .voltar {
            display: inline-block;
            margin-bottom: 20px;
            padding: 8px 16px;
            background: #7C3AED;
            /* Roxo */
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: 0.3s;
        }

        .voltar:hover {
            background: #6B21A8;
            /* Roxo mais escuro */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            background: #1f2937;
            /* Fundo escuro para a tabela */
        }

        th,
        td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            vertical-align: top;
            font-size: 14px;
            color: #E6EDF3;
        }

        th {
            background: #7C3AED;
            /* Roxo */
            color: #fff;
            font-weight: 600;
            text-transform: uppercase;
        }

        tr:hover {
            background: rgba(124, 58, 236, 0.1);
            /* Roxo suave */
        }

        img {
            max-width: 80px;
            margin: 2px;
            border-radius: 6px;
            transition: transform 0.3s;
        }

        img:hover {
            transform: scale(1.25);
            cursor: pointer;
        }

        .btn-delete {
            padding: 8px 18px;
            border: none;
            border-radius: 12px;
            /* Botão arredondado */
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            background: #10B981;
            /* Verde */
            color: #fff;
            transition: 0.3s;
        }

        .btn-delete:hover {
            background: #059669;
            /* Verde mais escuro */
        }

        .btn-delete:active {
            background: #047857;
            /* Verde ainda mais escuro */
        }
    </style>
</head>

<body>
    <main class="main">

        <a class="voltar" href="dashboard.php">← Voltar</a>
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
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['nome']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($row['descricao'])); ?></td>
                            <td><?php echo htmlspecialchars($row['endereco']); ?></td>
                            <td><?php echo htmlspecialchars($row['categoria']); ?></td>
                            <td>
                                <?php
                                if ($row['imagens']) {
                                    $imgs = explode(',', $row['imagens']);
                                    foreach ($imgs as $img) {
                                        echo "<img src='uploads/pontos/" . htmlspecialchars($img) . "' alt='Imagem'>";
                                    }
                                } else {
                                    echo "Sem imagens";
                                }
                                ?>
                            </td>
                            <td>
                                <a href="?deletar_id=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Tem certeza que deseja deletar este ponto turístico?');">Deletar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center; color:#9ca3af;">Nenhum ponto turístico cadastrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

    </main>
</body>

</html>