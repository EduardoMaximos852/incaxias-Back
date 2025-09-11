<?php
session_start();
include __DIR__ . '/../database.php';

// Acesso restrito
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['usuario_nivel'], ['admin', 'editor'])) {
    header("Location: ../public/login.php");
    exit();
}

$msg = "";

// Deletar comércio
if (isset($_GET['delete'])) {
    $idDelete = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM comercio WHERE id = ?");
    $stmt->bind_param("i", $idDelete);
    $stmt->execute();
    $msg = "Comércio deletado com sucesso!";
}

// Buscar
$search = "";
if (isset($_GET['search'])) {
    $search = "%" . $_GET['search'] . "%";
    $stmt = $conn->prepare("SELECT * FROM comercio WHERE nome LIKE ? OR descricao LIKE ? ORDER BY id DESC");
    $stmt->bind_param("ss", $search, $search);
} else {
    $stmt = $conn->prepare("SELECT * FROM comercio ORDER BY id DESC");
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Comércio • InCaxias</title>
    <link rel="stylesheet" href="../public/css/incaxias.css">
</head>

<body>
    <div class="app">
        <?php include '../public/sidebar.php'; ?>

        <main class="main">
            <h1>Gerenciar Comércio</h1>

            <?php if ($msg): ?>
                <div style="margin-bottom:10px;color:green;"><?php echo $msg; ?></div>
            <?php endif; ?>

            <form method="get" style="margin-bottom:20px;display:flex;gap:10px;align-items:center;">
                <input type="text" name="search" placeholder="Buscar comércio..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button class="btn">Buscar</button>
                <?php if (in_array($_SESSION['usuario_nivel'], ['admin', 'editor'])): ?>
                    <a href="cadastrar_comercio.php" class="btn">Adicionar Novo</a>
                <?php endif; ?>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['nome']); ?></td>
                            <td><?php echo htmlspecialchars($row['descricao']); ?></td>
                            <td>
                                <?php if (in_array($_SESSION['usuario_nivel'], ['admin', 'editor'])): ?>
                                    <a href="editar_comercio.php?id=<?php echo $row['id']; ?>" class="btn ghost" style="padding:4px 8px;">Editar</a>
                                    <a href="comercio.php?delete=<?php echo $row['id']; ?>" class="btn ghost" style="padding:4px 8px;color:red;" onclick="return confirm('Deseja realmente deletar este comércio?');">Excluir</a>
                                <?php else: ?>
                                    <span>Sem permissão</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>

</html>