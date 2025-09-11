<?php
session_start();



if (!isset($_SESSION['usuario_nivel']) || $_SESSION['usuario_nivel'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$uploadDir = __DIR__ . '/../uploads/';

$host = "localhost";
$user = "root";
$pass = "";
$db   = "dbincaxias";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Erro na conexão: " . $conn->connect_error);

$msg = '';

if (isset($_POST['cadastrar'])) {
    $nome      = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $endereco  = trim($_POST['endereco']);
    $categoria = trim($_POST['categoria']);

    // 1. Insere o monumento na tabela cultura
    $stmt = $conn->prepare("INSERT INTO cultura (nome, descricao, endereco, categoria, criado_em, atualizado_em)
                            VALUES (?, ?, ?, ?, NOW(), NOW())");
    $stmt->bind_param("ssss", $nome, $descricao, $endereco, $categoria);
    $stmt->execute();
    $cultura_id = $stmt->insert_id;
    $stmt->close();

    // 2. Upload múltiplo de imagens
    if (isset($_FILES['imagens']) && count($_FILES['imagens']['name']) > 0) {
        for ($i = 0; $i < count($_FILES['imagens']['name']); $i++) {
            if ($_FILES['imagens']['error'][$i] === 0) {
                $imgName = time() . '_' . $i . '_' . basename($_FILES['imagens']['name'][$i]);
                move_uploaded_file($_FILES['imagens']['tmp_name'][$i], '../uploads/' . $imgName);

                $imgStmt = $conn->prepare("INSERT INTO cultura_imagens (cultura_id, imagem) VALUES (?, ?)");
                $imgStmt->bind_param("is", $cultura_id, $imgName);
                $imgStmt->execute();
                $imgStmt->close();
            }
        }
    }

    $msg = "<div class='success'>Monumento de Cultura cadastrado com sucesso!</div>";
}

// Lista de monumentos com suas fotos
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
    <title>Cadastrar Ponto Turístico</title>

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cadastrar Cultura</title>
        <link rel="stylesheet" href="../css/incaxias.css"><!-- mesmo CSS global -->
        <style>
            :root {
                --bg: #0f0b13;
                --panel: #0f1724;
                --purple-500: #7C3AED;
                --purple-600: #6B21A8;
                --green-500: #10B981;
                --muted: #9AA4B2;
                --glass: rgba(255, 255, 255, 0.04);
                --radius: 12px;
                --card-shadow: 0 6px 18px rgba(11, 8, 20, 0.6);
            }

            * {
                box-sizing: border-box
            }

            body {
                margin: 0;
                font-family: Inter, sans-serif;
                background: linear-gradient(180deg, #07050a 0%, #0f0b13 100%);
                color: #E6EDF3;
                padding: 20px;
            }

            h2 {
                margin-bottom: 20px;
                text-align: center;
            }

            form {
                background: var(--panel);
                padding: 20px;
                border-radius: var(--radius);
                box-shadow: var(--card-shadow);
                max-width: 600px;
                margin: auto;
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            input,
            textarea {
                padding: 10px;
                border-radius: 8px;
                border: 1px solid rgba(255, 255, 255, 0.1);
                background: rgba(255, 255, 255, 0.05);
                color: #E6EDF3;
                font-size: 14px;
            }

            button {
                padding: 10px 15px;
                border: none;
                border-radius: 10px;
                background: linear-gradient(90deg, var(--purple-600), var(--purple-500));
                color: white;
                font-weight: 600;
                cursor: pointer;
                transition: 0.3s;
            }

            button:hover {
                opacity: 0.9
            }

            .success {
                color: var(--green-500);
                margin-bottom: 10px;
            }

            .error {
                color: #f87171;
                margin-bottom: 10px
            }

            /* Tabela estilo dashboard */
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }

            th,
            td {
                padding: 12px;
                text-align: left;
                border-bottom: 1px solid rgba(255, 255, 255, 0.03);
                font-size: 14px;
            }

            th {
                color: var(--muted);
                font-size: 13px;
            }
        </style>
    </head>

<body>
    <!-- Botão voltar -->
    <div style="max-width:600px; margin:auto 0 20px 0; text-align:right;">
        <a href="dashboard.php" style="
        display:inline-block;
        padding:8px 15px;
        border-radius:10px;
        background:linear-gradient(90deg,var(--purple-600),var(--purple-500));
        color:white;
        font-weight:600;
        text-decoration:none;
        transition:0.3s;
    ">← Voltar ao Dashboard</a>
    </div>

    <h2>Cadastrar Nossa Cultura</h2>
    <?php echo $msg; ?>


    <div class="form-container">
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

            <button type="submit" name="cadastrar" class="btn-primary">Cadastrar</button>
        </form>
    </div>



</body>

</html>