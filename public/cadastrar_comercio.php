<?php
session_start();


$uploadDir = __DIR__ . '/../uploads/';

$host = "localhost";
$user = "root";
$pass = "";
$db   = "dbincaxias";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Erro na conexão: " . $conn->connect_error);




$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome       = trim($_POST['nome']);
    $descricao  = trim($_POST['descricao']);
    $endereco   = trim($_POST['endereco']);
    $contato    = trim($_POST['contato']); // <- novo campo
    $categoria  = trim($_POST['categoria']);
    $imagemNome = '';

    if (!empty($_FILES['imagem']['name'])) {
        $dirUpload = __DIR__ . '/../uploads/';
        if (!is_dir($dirUpload)) mkdir($dirUpload, 0777, true);
        $imagemNome = time() . '_' . basename($_FILES['imagem']['name']);
        move_uploaded_file($_FILES['imagem']['tmp_name'], $dirUpload . $imagemNome);
    }

    $stmt = $conn->prepare(
        "INSERT INTO comercio (nome, descricao, endereco, contato, imagem, categoria, criado_em)
         VALUES (?,?,?,?,?,?,NOW())"
    );
    $stmt->bind_param('ssssss', $nome, $descricao, $endereco, $contato, $imagemNome, $categoria);

    if ($stmt->execute()) {
        $msg = "<div class='alert success'>Cadastro realizado com sucesso!</div>";
    } else {
        $msg = "<div class='alert error'>Erro ao cadastrar: " . $stmt->error . "</div>";
    }
}
?>
<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cadastrar Comércio</title>
    <link rel="stylesheet" href="../assets/incaxias.css"> <!-- mesmo CSS global -->
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


    <h2>Cadastrar Comércio</h2>
    <?= $msg ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Nome</label>
        <input type="text" name="nome" required>

        <label>Descrição</label>
        <textarea name="descricao" rows="3" required></textarea>

        <label>Endereço</label>
        <input type="text" name="endereco" required>

        <!-- Novo Campo Contato -->
        <label>Contato</label>
        <input type="text" name="contato" placeholder="(99) 99999-9999 ou email" required>

        <label>Categoria</label>
        <input type="text" name="categoria" required>

        <label>Imagem (opcional)</label>
        <input type="file" name="imagem" accept="image/*">

        <button class="btn" type="submit">Salvar</button>
    </form>
    </div>
</body>

</html>