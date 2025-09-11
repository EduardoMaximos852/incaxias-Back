<?php
session_start();

// Bloqueio de acesso
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$host = "localhost";
$user = "root";
$pass = "";
$db   = "dbincaxias";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Filtro de busca
$busca = isset($_GET['q']) ? trim($_GET['q']) : '';
$sql   = "SELECT id, nome, descricao, imagem FROM pontos_turisticos";
if ($busca !== '') {
    $sql .= " WHERE nome LIKE ?";
    $stmt = $conn->prepare($sql);
    $like = "%$busca%";
    $stmt->bind_param("s", $like);
} else {
    $stmt = $conn->prepare($sql);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pontos Turísticos • InCaxias</title>
    <link rel="stylesheet" href="../css/incaxias.css">
</head>

<body>
    <div class="app">

        <?php include 'sidebar.php'; ?>

        <main class="main">
            <h1>Pontos Turísticos</h1>

            <!-- Barra de busca -->
            <form method="get" class="card" style="margin-bottom:20px;max-width:500px;">
                <div class="form-group">
                    <label for="q">Buscar</label>
                    <input type="text" id="q" name="q" value="<?php echo htmlspecialchars($busca); ?>" placeholder="Digite o nome do ponto turístico...">
                </div>
                <button type="submit" class="btn">Pesquisar</button>
            </form>

            <!-- Botão de adicionar (somente admin e editor) -->
            <?php if (in_array($_SESSION['usuario_nivel'], ['admin', 'editor'])): ?>
                <a href="turismo_novo.php" class="btn" style="margin-bottom:20px;">Adicionar Novo Ponto</a>
            <?php endif; ?>

            <!-- Lista -->
            <div class="grid">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="card">
                            <?php if (!empty($row['imagem'])): ?>
                                <img src="uploads/<?php echo htmlspecialchars($row['imagem']); ?>" alt="" style="width:100%;border-radius:12px;margin-bottom:10px;">
                            <?php endif; ?>
                            <h3><?php echo htmlspecialchars($row['nome']); ?></h3>
                            <p><?php echo nl2br(htmlspecialchars($row['descricao'])); ?></p>

                            <?php if (in_array($_SESSION['usuario_nivel'], ['admin', 'editor'])): ?>
                                <div style="margin-top:10px;display:flex;gap:8px;">
                                    <a href="turismo_editar.php?id=<?php echo $row['id']; ?>" class="btn ghost">Editar</a>
                                    <a href="turismo_excluir.php?id=<?php echo $row['id']; ?>"
                                        class="btn ghost"
                                        onclick="return confirm('Excluir este ponto turístico?')">Excluir</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="card">Nenhum ponto turístico encontrado.</div>
                <?php endif; ?>
            </div>

        </main>
    </div>
</body>

</html>