<?php
session_start();




//Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "dbincaxias";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Erro na conexão: " . $conn->connect_error);

$msg = "";

// Senha master
$senhaMaster = "Master@123";

// Login normal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    $stmt = $conn->prepare("SELECT id, nome, senha, nivel FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if (password_verify($senha, $row['senha'])) {
            $_SESSION['usuario_id']    = $row['id'];
            $_SESSION['usuario_nome']  = $row['nome'];
            $_SESSION['usuario_email'] = $row['email'];
            $_SESSION['usuario_nivel'] = $row['nivel'];

            header("Location: dashboard.php");
            exit();
        } else {
            $msg = "Senha incorreta!";
        }
    } else {
        $msg = "Usuário não encontrado!";
    }
}

// Validação da senha master
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['senha_master'])) {
    $senhaMasterDigitada = trim($_POST['senha_master']);
    if ($senhaMasterDigitada === $senhaMaster) {
        $_SESSION['master_validado'] = true;
    } else {
        $msg = "Senha master incorreta!";
    }
}
?>
<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login • InCaxias</title>
    <link rel="stylesheet" href="../css/incaxias.css">
</head>

<body>
    <div class="login-card card" style="max-width:400px;margin:50px auto;padding:30px;">
        <h2>Login</h2>
        <?php if ($msg): ?>
            <div style="color:red;margin-bottom:10px;"><?php echo $msg; ?></div>
        <?php endif; ?>

        <!-- Formulário login -->
        <form method="post">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Senha</label>
                <input type="password" name="senha" required>
            </div>
            <button type="submit" name="login" class="btn">Entrar</button>
        </form>

        <hr style="margin:20px 0;">

        <!-- Formulário master -->
        <?php if (!isset($_SESSION['master_validado'])): ?>
            <form method="post">
                <div class="form-group">
                    <label>Senha master</label>
                    <input type="password" name="senha_master">
                </div>
                <button type="submit" class="btn ghost">Validar</button>
            </form>
        <?php else: ?>
            <a href="cadastrar_usuarios.php" class="btn" style="margin-top:10px;display:block;text-align:center;">Cadastrar Usuário Admin</a>
        <?php endif; ?>
    </div>
</body>

</html>