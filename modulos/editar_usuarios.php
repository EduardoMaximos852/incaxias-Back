<?php
session_start();
include __DIR__ . '/../database.php';

// Acesso restrito: apenas admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_nivel'] !== 'admin') {
    header("Location: ../public/login.php");
    exit();
}

$msg = "";

// Pegar ID do usuário a editar
if (!isset($_GET['id'])) {
    header("Location: usuarios.php");
    exit();
}

$id = intval($_GET['id']);

// Pegar dados atuais do usuário
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: usuarios.php");
    exit();
}

$user = $result->fetch_assoc();

// Atualizar dados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $nivel = $_POST['nivel'];

    if (empty($nome) || empty($email) || empty($nivel)) {
        $msg = "Todos os campos são obrigatórios!";
    } else {
        $stmtUpdate = $conn->prepare("UPDATE usuarios SET nome = ?, email = ?, nivel = ? WHERE id = ?");
        $stmtUpdate->bind_param("sssi", $nome, $email, $nivel, $id);

        if ($stmtUpdate->execute()) {
            $msg = "Usuário atualizado com sucesso!";
            // Atualiza os dados da página
            $user['nome'] = $nome;
            $user['email'] = $email;
            $user['nivel'] = $nivel;
        } else {
            $msg = "Erro ao atualizar usuário: " . $stmtUpdate->error;
        }
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Editar Usuário • InCaxias</title>
<link rel="stylesheet" href="../public/css/incaxias.css">
</head>
<body>
<div class="app">
    <?php include '../public/sidebar.php'; ?>

    <main class="main">
        <h1>Editar Usuário</h1>

        <?php if($msg): ?>
            <div style="margin-bottom:10px;color:green;"><?php echo $msg; ?></div>
        <?php endif; ?>

        <form method="post" style="max-width:500px;">
            <div class="form-group">
                <label>Nome</label>
                <input type="text" name="nome" value="<?php echo htmlspecialchars($user['nome']); ?>" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="form-group">
                <label>Nível</label>
                <select name="nivel" required>
                    <option value="admin" <?php if($user['nivel']=='admin') echo 'selected'; ?>>Admin</option>
                    <option value="editor" <?php if($user['nivel']=='editor') echo 'selected'; ?>>Editor</option>
                    <option value="usuario" <?php if($user['nivel']=='usuario') echo 'selected'; ?>>Usuário</option>
                </select>
            </div>
            <button type="submit" class="btn">Salvar Alterações</button>
            <a href="usuarios.php" class="btn ghost" style="margin-left:10px;">Voltar</a>
        </form>
    </main>
</div>
</body>
</html>
