<?php
//Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "dbincaxias";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Erro na conexão: " . $conn->connect_error);


$msg = '';

if (isset($_POST['cadastrar'])) {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $endereco = $_POST['endereco'];
    $categoria = $_POST['categoria'];

    // 1. Insere o monumento
    $stmt = $conn->prepare("INSERT INTO cultura (nome, descricao, endereco, categoria, criado_em, atualizado_em)
                            VALUES (?, ?, ?, ?, NOW(), NOW())");
    $stmt->bind_param("ssss", $nome, $descricao, $endereco, $categoria);
    $stmt->execute();
    $cultura_id = $stmt->insert_id;
    $stmt->close();

    // 2. Upload de várias imagens
    if (isset($_FILES['imagens'])) {
        $total = count($_FILES['imagens']['name']);
        for ($i = 0; $i < $total; $i++) {
            if ($_FILES['imagens']['error'][$i] === 0) {
                $imgName = time() . '_' . $i . '_' . basename($_FILES['imagens']['name'][$i]);
                move_uploaded_file($_FILES['imagens']['tmp_name'][$i], 'uploads/' . $imgName);

                $imgStmt = $conn->prepare("INSERT INTO cultura_imagens (cultura_id, imagem) VALUES (?, ?)");
                $imgStmt->bind_param("is", $cultura_id, $imgName);
                $imgStmt->execute();
                $imgStmt->close();
            }
        }
    }

    $msg = "<div class='success'>Monumento de Cultura cadastrado com várias fotos!</div>";
}

// Buscar todos os monumentos e fotos
$sql = "SELECT c.*, GROUP_CONCAT(ci.imagem) AS fotos
        FROM cultura c
        LEFT JOIN cultura_imagens ci ON c.id = ci.cultura_id
        GROUP BY c.id
        ORDER BY c.criado_em DESC";
$res = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Cultura</title>
    <link rel="stylesheet" href="../css/incaxias.css"><!-- use o mesmo CSS global -->
</head>

<body>

    <a href="dashboard.php" class="back-btn">← Voltar ao Dashboard</a>
    <h2>Cadastrar Monumento de Cultura</h2>
    <?php echo $msg; ?>

    <form method="POST" enctype="multipart/form-data" class="form-card">
        <label>Nome:</label>
        <input type="text" name="nome" required>

        <label>Descrição:</label>
        <textarea name="descricao" rows="4" required></textarea>

        <label>Endereço:</label>
        <input type="text" name="endereco">

        <label>Categoria:</label>
        <input type="text" name="categoria">

        <label>Fotos (múltiplas):</label>
        <input type="file" name="imagens[]" multiple>

        <button type="submit" name="cadastrar">Cadastrar</button>
    </form>

    <h2>Monumentos Cadastrados</h2>
    <table class="table-style">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Endereço</th>
                <th>Categoria</th>
                <th>Fotos</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $res->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['nome']; ?></td>
                    <td><?php echo $row['descricao']; ?></td>
                    <td><?php echo $row['endereco']; ?></td>
                    <td><?php echo $row['categoria']; ?></td>
                    <td>
                        <?php
                        if ($row['fotos']) {
                            $fotos = explode(',', $row['fotos']);
                            foreach ($fotos as $foto) {
                                echo "<img src='uploads/" . $foto . "' width='60' style='margin:3px;border-radius:8px;'>";
                            }
                        }
                        ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</body>

</html>