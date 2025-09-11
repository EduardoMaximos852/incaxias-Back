<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "dbincaxias";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Erro na conexão: " . $conn->connect_error);

$msg = '';

// Inserção do ponto comercio
if (isset($_POST['cadastrar'])) {
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $endereco = $_POST['endereco'];
    $categoria = $_POST['categoria'];

    $imagem = '';
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $imagem = time() . '_' . basename($_FILES['imagem']['name']);
        move_uploaded_file($_FILES['imagem']['tmp_name'], 'uploads/' . $imagem);
    }

    $stmt = $conn->prepare("INSERT INTO comercio (nome, descricao, endereco, imagem, categoria, criado_em, atualizado_em) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
    $stmt->bind_param("sssss", $nome, $descricao, $endereco, $imagem, $categoria);

    if ($stmt->execute()) {
        $msg = "<div class='success'>Ponto turístico cadastrado com sucesso!</div>";
    } else {
        $msg = "<div class='error'>Erro ao cadastrar: " . $conn->error . "</div>";
    }
    $stmt->close();
}

// Buscar todos os pontos turísticos
$res = $conn->query("SELECT * FROM comercio ORDER BY criado_em DESC");
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Ponto Turístico</title>
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

        a.back-btn {
            display: inline-block;
            padding: 10px 18px;
            border-radius: 10px;
            background: linear-gradient(90deg, var(--purple-600), var(--purple-500));
            color: white;
            font-weight: 600;
            text-decoration: none;
            transition: 0.3s;
            margin-bottom: 20px;
        }

        a.back-btn:hover {
            opacity: 0.9
        }

        /* Formulário */
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
            text-align: center;
        }

        .error {
            color: #f87171;
            margin-bottom: 10px;
            text-align: center;
        }

        /* Tabela estilo dashboard */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 40px;
            background: var(--panel);
            border-radius: var(--radius);
            overflow: hidden;
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

        img {
            border-radius: 8px;
        }

        @media (max-width:768px) {
            form {
                padding: 15px;
            }

            table,
            th,
            td {
                font-size: 12px;
            }
        }
    </style>
</head>

<body>

    <!-- Botão voltar -->
    <div style="max-width:600px; margin:auto 0 20px 0; text-align:left;">
        <a href="dashboard.php" class="back-btn">← Voltar ao Dashboard</a>
    </div>

    <h2>Cadastrar Ponto Comércio</h2>
    <?php echo $msg; ?>
    <form method="POST" enctype="multipart/form-data">
        <label>Nome:</label>
        <input type="text" name="nome" required>

        <label>Descrição:</label>
        <textarea name="descricao" rows="4" required></textarea>

        <label>Endereço:</label>
        <input type="text" name="endereco">

        <label>Categoria:</label>
        <input type="text" name="categoria">

        <label>Imagem:</label>
        <input type="file" name="imagem">

        <button type="submit" name="cadastrar">Cadastrar</button>
    </form>



</body>

</html>