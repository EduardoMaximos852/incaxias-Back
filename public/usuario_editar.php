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

$opcoes_nivel = ["usuario","editor","admin"];
$erro = "";
$sucesso = "";

$id = intval($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT id,nome,email,nivel FROM usuarios WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if(!$user){
    die("Usuário não encontrado");
}

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $nome  = trim($_POST["nome"]);
    $email = trim($_POST["email"]);
    $nivel = $_POST["nivel"];
    $senha = trim($_POST["senha"]);

    if(!in_array($nivel,$opcoes_nivel)){
        $erro = "Nível inválido!";
    } else {
        // Verifica email duplicado
        $stmt_check = $conn->prepare("SELECT id FROM usuarios WHERE email=? AND id<>?");
        $stmt_check->bind_param("si",$email,$id);
        $stmt_check->execute();
        if($stmt_check->get_result()->num_rows>0){
            $erro = "Email já cadastrado!";
        } else {
            if($senha){
                $senhaHash = password_hash($senha,PASSWORD_DEFAULT);
                $stmt_update = $conn->prepare("UPDATE usuarios SET nome=?, email=?, nivel=?, senha=? WHERE id=?");
                $stmt_update->bind_param("ssssi",$nome,$email,$nivel,$senhaHash,$id);
            } else {
                $stmt_update = $conn->prepare("UPDATE usuarios SET nome=?, email=?, nivel=? WHERE id=?");
                $stmt_update->bind_param("sssi",$nome,$email,$nivel,$id);
            }

            if($stmt_update->execute()){
                $sucesso = "Usuário atualizado com sucesso!";
            } else {
                $erro = "Erro ao atualizar usuário!";
            }
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
  <link rel="stylesheet" href="css/incaxias.css">
</head>
<body>
<div class="app">
  <?php include 'sidebar.php'; ?>
  <main class="main">
    <h2>Editar Usuário</h2>

    <?php if($erro): ?><div class="alert-error"><?php echo $erro; ?></div><?php endif; ?>
    <?php if($sucesso): ?><div class="alert-success"><?php echo $sucesso; ?></div><?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label>Nome completo</label>
        <input type="text" name="nome" value="<?php echo htmlspecialchars($user['nome']); ?>" required>
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
      </div>
      <div class="form-group">
        <label>Senha (deixe em branco para não alterar)</label>
        <input type="password" name="senha">
      </div>
      <div class="form-group">
        <label>Nível</label>
        <select name="nivel" required>
          <?php foreach($opcoes_nivel as $n): ?>
          <option value="<?php echo $n; ?>" <?php echo $n==$user['nivel']?'selected':''; ?>><?php echo ucfirst($n); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="btn">Atualizar</button>
    </form>
  </main>
</div>
</body>
</html>
