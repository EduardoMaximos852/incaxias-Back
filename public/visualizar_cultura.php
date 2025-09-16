<?php
session_start();

// Conexão com banco
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

// Deletar evento cultural
if (isset($_GET['deletar_id'])) {
    $id = (int)$_GET['deletar_id'];
    $conn->query("DELETE FROM cultura WHERE id = $id");
    header("Location: visualizar_cultura.php");
    exit();
}

// Buscar eventos culturais
$res = $conn->query("SELECT * FROM cultura ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Visualizar Cultura • InCaxias</title>
    <link rel="stylesheet" href="../css/incaxias.css">
    <style>
        body {
            background: #25253C;
            color: #E6EDF3;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 10px;
        }

        h1 {
            text-align: center;
            color: #8E24AA;
            margin-bottom: 20px;
        }

        .card {
            background: #2C2C54;
            border-radius: 8px;
            padding: 15px;
            margin: 15px;
            display: inline-block;
            vertical-align: top;
            width: 280px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .card h3 {
            color: #43A047;
            margin-top: 0;
        }

        .card p {
            font-size: 14px;
            line-height: 1.4;
        }

        .btn-delete {
            display: block;
            background: #E57373;
            color: #fff;
            text-align: center;
            padding: 8px 0;
            border-radius: 4px;
            text-decoration: none;
            margin-top: 10px;
        }

        .btn-delete:hover {
            background: #F44336;
        }

        .btn-back {
            display: inline-block;
            background: #8E24AA;
            color: #fff;
            text-align: center;
            padding: 8px 12px;
            border-radius: 4px;
            text-decoration: none;
            margin-bottom: 20px;
        }

        .btn-back:hover {
            background: #7B1FA2;
        }
    </style>
</head>

<body>
    <div class="container">
        <a href="dashboard.php" class="btn-back">Voltar</a>
        <h1>Eventos Culturais</h1>

        <?php while ($c = $res->fetch_assoc()): ?>
            <div class="card">
                <h3><?php echo htmlspecialchars($c['nome']); ?></h3>
                <p><strong>Categoria:</strong> <?php echo htmlspecialchars($c['categoria']); ?></p>
                <p><strong>Descrição:</strong> <?php echo htmlspecialchars($c['descricao']); ?></p>
                <p><strong>Endereço:</strong> <?php echo htmlspecialchars($c['endereco']); ?></p>
                <a href="?deletar_id=<?php echo $c['id']; ?>" class="btn-delete" onclick="return confirm('Deseja deletar este evento cultural?');">Deletar</a>
            </div>
        <?php endwhile; ?>

    </div>
</body>

</html>