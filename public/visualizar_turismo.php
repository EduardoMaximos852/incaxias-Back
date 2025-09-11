<?php
session_start();

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

// Buscar todos os pontos turísticos
$result = $conn->query("SELECT * FROM turismo ORDER BY id DESC");
$pontos = [];
while ($row = $result->fetch_assoc()) {
    $pontos[] = $row;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Visualizar Pontos Turísticos • InCaxias</title>
    <link rel="stylesheet" href="../css/incaxias.css">
    <style>
        .grid-pontos {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 18px;
            margin-top: 20px;
        }

        .card-ponto {
            background: var(--panel);
            border-radius: var(--radius);
            box-shadow: var(--card-shadow);
            padding: 16px;
            color: #fff;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .card-ponto img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
        }

        .card-ponto h3 {
            margin: 0;
            font-size: 18px;
            color: var(--purple-500);
        }

        .card-ponto p {
            font-size: 14px;
            color: var(--muted);
        }
    </style>
</head>

<body>
    <div class="app">
        <?php include '../public/sidebar.php'; ?>
        <main class="main">
            <h2>Pontos Turísticos</h2>
            <div class="grid-pontos">
                <?php foreach ($pontos as $p): ?>
                    <div class="card-ponto">
                        <?php if ($p['imagem']): ?>
                            <img src="uploads/<?php echo $p['imagem']; ?>" alt="<?php echo htmlspecialchars($p['nome']); ?>">
                        <?php endif; ?>
                        <h3><?php echo htmlspecialchars($p['nome']); ?></h3>
                        <p><strong>Categoria:</strong> <?php echo htmlspecialchars($p['categoria']); ?></p>
                        <p><strong>Endereço:</strong> <?php echo htmlspecialchars($p['endereco']); ?></p>
                        <p><?php echo htmlspecialchars($p['descricao']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>
    </div>
</body>

</html>