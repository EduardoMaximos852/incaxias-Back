<?php
session_start();

//Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "dbincaxias";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Erro na conexão: " . $conn->connect_error);

// Permite acesso apenas se master validado ou admin logado
if (
    !isset($_SESSION['usuario_id']) &&
    !isset($_SESSION['master_validado'])
) {
    header("Location: login.php");
    exit();
}

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $nivel = $_POST['nivel'];

    if (empty($nome) || empty($email) || empty($senha) || empty($nivel)) {
        $msg = "Todos os campos são obrigatórios!";
    } else {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, nivel) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nome, $email, $senhaHash, $nivel);

        if ($stmt->execute()) {
            $msg = "Usuário cadastrado com sucesso!";
        } else {
            $msg = "Erro: " . $stmt->error;
        }
    }
}
?>
<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cadastrar Usuário • InCaxias</title>
    <link rel="stylesheet" href="../css/incaxias.css">
</head>

<body>
    <div class="login-card card" style="max-width:500px;margin:50px auto;padding:30px;">
        <h2>Cadastrar Usuário</h2>
        <?php if ($msg): ?>
            <div style="margin-bottom:10px;color:green;"><?php echo $msg; ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label>Nome</label>
                <input type="text" name="nome" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Senha</label>
                <input type="password" name="senha" required>
            </div>
            <div class="form-group">
                <label>Nível</label>
                <select name="nivel" required>
                    <option value="admin">Admin</option>
                    <option value="editor">Editor</option>
                    <option value="usuario">Usuário</option>
                </select>
            </div>
            <button type="submit" class="btn" style="margin-top:10px;display:block;text-align:center;">Cadastrar</button>

            <a href="logout.php" class="btn" style="margin-top:10px;display:block;text-align:center;">Login</a>

        </form>
    </div>
</body>

</html>