<?php
/* ============ CONFIGURAÇÃO DO BANCO ============ */
$host = "localhost";
$user = "root";
$pass = "";
$db   = "dbincaxias";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Erro na conexão: " . $conn->connect_error);

/* Criação de tabelas */
$conn->query("
CREATE TABLE IF NOT EXISTS pontos_turisticos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    descricao TEXT,
    endereco VARCHAR(255),
    categoria VARCHAR(100),
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

$conn->query("
CREATE TABLE IF NOT EXISTS ponto_imagens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ponto_id INT NOT NULL,
    caminho VARCHAR(255) NOT NULL,
    FOREIGN KEY (ponto_id) REFERENCES pontos_turisticos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

/* ============ PROCESSAMENTO DO FORM ============ */
$mensagem = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome      = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $endereco  = trim($_POST['endereco'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');

    if ($nome === '' || $categoria === '') {
        $mensagem = "Os campos Nome e Categoria são obrigatórios.";
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO pontos_turisticos (nome, descricao, endereco, categoria) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("ssss", $nome, $descricao, $endereco, $categoria);
        if (!$stmt->execute()) {
            die("Erro ao inserir: " . $stmt->error);
        } else {
            echo "Inserido com sucesso!";
        }

        // Upload das imagens (máx 6)
        $uploadDir = __DIR__ . "/uploads/pontos/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        if (!empty($_FILES['imagens']['name'][0])) {
            $total = min(count($_FILES['imagens']['name']), 6);
            for ($i = 0; $i < $total; $i++) {
                if ($_FILES['imagens']['error'][$i] === UPLOAD_ERR_OK) {
                    $ext = pathinfo($_FILES['imagens']['name'][$i], PATHINFO_EXTENSION);
                    $fileName = time() . "_{$i}." . strtolower($ext);
                    $destino = $uploadDir . $fileName;

                    if (move_uploaded_file($_FILES['imagens']['tmp_name'][$i], $destino)) {
                        $stmt = $conn->prepare(
                            "INSERT INTO ponto_imagens (ponto_id, caminho) VALUES (?, ?)"
                        );
                        $stmt->bind_param("is", $ponto_id, $fileName);
                        $stmt->execute();
                        $stmt->close();
                    }
                }
            }
        }
        $mensagem = "✅ Ponto turístico cadastrado com sucesso!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Cadastrar Turismo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            margin: 0;
            padding: 40px 0;
        }

        header {
            text-align: center;
            position: relative;
            margin-bottom: 30px;
        }

        header h1 {
            margin: 0;
            font-size: 26px;
        }

        .voltar {
            position: absolute;
            left: 20px;
            top: 0;
            padding: 8px 16px;
            background: #666;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
        }

        .voltar:hover {
            background: #444;
        }

        form {
            background: #fff;
            max-width: 500px;
            margin: 0 auto;
            padding: 25px 30px;
            border-radius: 8px;
            box-shadow: 0 0 12px rgba(0, 0, 0, .1);
        }

        form label {
            display: block;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        form input[type="text"],
        form textarea,
        form input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        form textarea {
            resize: vertical;
        }

        form button {
            margin-top: 20px;
            width: 100%;
            padding: 12px;
            background: #2b7cff;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        form button:hover {
            background: #1d5fc9;
        }

        .mensagem {
            text-align: center;
            margin: 20px auto;
            max-width: 500px;
            color: green;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <header>
        <a class="voltar" href="dashboard.php">← Voltar</a>
        <h1>Cadastrar Ponto Turístico</h1>
    </header>

    <?php if ($mensagem): ?>
        <div class="mensagem"><?php echo htmlspecialchars($mensagem); ?></div>
    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data">
        <label>Nome</label>
        <input type="text" name="nome" required>

        <label>Descrição</label>
        <textarea name="descricao" rows="4"></textarea>

        <label>Endereço</label>
        <input type="text" name="endereco">

        <label>Categoria</label>
        <input type="text" name="categoria" required>

        <label>Imagens (até 6)</label>
        <input type="file" name="imagens[]" accept="image/*" multiple>

        <button type="submit">Cadastrar</button>
    </form>

</body>

</html>