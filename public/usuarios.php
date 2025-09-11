<?php
session_start();
if (!isset($_SESSION["usuario_id"]) || $_SESSION["usuario_nivel"] !== 'admin') {
    header("Location: login.php");
    exit();
}

$host = "localhost";
$user = "root";
$pass = "";
$db   = "dbincaxias";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Erro na conexão: " . $conn->connect_error);

// Handle deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: usuarios.php");
    exit();
}

// Fetch users
$result = $conn->query("SELECT id, nome, email, nivel, criado_em FROM usuarios ORDER BY criado_em DESC");
?>

<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Usuários • InCaxias</title>
    <link rel="stylesheet" href="../css/incaxias.css">
</head>

<body>
    <div class="app">
        <?php include 'sidebar.php'; // Mantém a mesma sidebar 
        ?>
        <main class="main">
            <h2>Gerenciar Usuários</h2>
            <a href="usuario_cadastrar.php" class="btn" style="margin-bottom:12px;">Adicionar Usuário</a>

            <div class="card">
                <table>
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Nível</th>
                            <th>Criado em</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $user['nome']; ?></td>
                                <td><?php echo $user['email']; ?></td>
                                <td><?php echo ucfirst($user['nivel']); ?></td>
                                <td><?php echo date("d/m/Y H:i", strtotime($user['criado_em'])); ?></td>
                                <td>
                                    <a href="usuario_editar.php?id=<?php echo $user['id']; ?>" class="btn ghost" style="padding:4px 8px;font-size:12px;">Editar</a>
                                    <a href="usuarios.php?delete=<?php echo $user['id']; ?>" class="btn ghost" style="padding:4px 8px;font-size:12px;" onclick="return confirm('Deseja realmente excluir este usuário?')">Excluir</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>

</html>