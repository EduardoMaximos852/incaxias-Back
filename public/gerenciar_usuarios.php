<?php
session_start();

// Bloqueia acesso de não logados
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Somente admin pode gerenciar usuários
if ($_SESSION['usuario_nivel'] !== 'admin') {
    http_response_code(403);
    exit('Acesso negado.');
}

include __DIR__ . '/../database.php';

// Deletar usuário se for solicitado
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    header("Location: gerenciar_usuarios.php");
    exit();
}

// Buscar usuários
$sql = "SELECT id, nome, email, nivel, criado_em FROM usuarios ORDER BY id DESC";
$result = $conn->query($sql);
if (!$result) {
    die("Erro SQL: " . $conn->error);
}
?>
<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gerenciar Usuários • InCaxias</title>
    <link rel="stylesheet" href="css/incaxias.css">
</head>

<body>
    <div class="app">
        <?php include 'sidebar.php'; ?>

        <main class="main">
            <h1>Gerenciar Usuários</h1>

            <a href="cadastrar_usuario.php" class="btn" style="margin-bottom:20px;">Novo Usuário</a>

            <div class="card">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Nível</th>
                            <th>Criado em</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['nome']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo ucfirst($row['nivel']); ?></td>
                                <td><?php echo $row['criado_em']; ?></td>
                                <td style="display:flex;gap:8px;">
                                    <a href="cadastrar_usuario.php?id=<?php echo $row['id']; ?>" class="btn ghost">Editar</a>
                                    <a href="gerenciar_usuarios.php?delete_id=<?php echo $row['id']; ?>"
                                        class="btn ghost"
                                        onclick="return confirm('Tem certeza que deseja excluir este usuário?');">
                                        Excluir
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        <?php if ($result->num_rows == 0): ?>
                            <tr>
                                <td colspan="6" style="text-align:center;">Nenhum usuário cadastrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>

</html>