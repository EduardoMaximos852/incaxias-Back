<?php
//Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "dbincaxias";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Erro na conexão: " . $conn->connect_error);

// Função para salvar
if (isset($_POST['salvar'])) {
    $nome = $_POST['nome'];
    $categoria = $_POST['categoria'];
    $endereco = $_POST['endereco'];
    $contato = $_POST['contato'];

    $imagem = $_FILES['imagem']['name'];
    $tmp = $_FILES['imagem']['tmp_name'];
    $upload_dir = "modulos/uploads/";

    if ($imagem) move_uploaded_file($tmp, $upload_dir . $imagem);

    $stmt = $conn->prepare("INSERT INTO saude (nome,categoria,endereco,contato,imagem) VALUES (?,?,?,?,?)");
    $stmt->bind_param("sssss", $nome, $categoria, $endereco, $contato, $imagem);
    $stmt->execute();
    $stmt->close();
    header("Location: dashboard.php?modulo=saude");
}

// Função para alterar
if (isset($_POST['alterar'])) {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $categoria = $_POST['categoria'];
    $endereco = $_POST['endereco'];
    $contato = $_POST['contato'];

    $imagem = $_FILES['imagem']['name'];
    $tmp = $_FILES['imagem']['tmp_name'];
    $upload_dir = "modulos/uploads/";

    if ($imagem) move_uploaded_file($tmp, $upload_dir . $imagem);

    if ($imagem) {
        $stmt = $conn->prepare("UPDATE saude SET nome=?, categoria=?, endereco=?, contato=?, imagem=? WHERE id=?");
        $stmt->bind_param("sssssi", $nome, $categoria, $endereco, $contato, $imagem, $id);
    } else {
        $stmt = $conn->prepare("UPDATE saude SET nome=?, categoria=?, endereco=?, contato=? WHERE id=?");
        $stmt->bind_param("ssssi", $nome, $categoria, $endereco, $contato, $id);
    }
    $stmt->execute();
    $stmt->close();
    header("Location: dashboard.php?modulo=saude");
}

// Função para deletar
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM saude WHERE id=$id");
    header("Location: dashboard.php?modulo=saude");
}

// Edição: carregar dados
$edit_id = $_GET['edit'] ?? null;
$saude_edit = null;
if ($edit_id) {
    $res = $conn->query("SELECT * FROM saude WHERE id=$edit_id");
    $saude_edit = $res->fetch_assoc();
}
?>

<h2><?php echo $saude_edit ? "Alterar Unidade de Saúde" : "Nova Unidade de Saúde"; ?></h2>

<form method="post" enctype="multipart/form-data">
    <?php if ($saude_edit): ?>
        <input type="hidden" name="id" value="<?php echo $saude_edit['id']; ?>">
    <?php endif; ?>

    <input type="text" name="nome" placeholder="Nome da unidade" value="<?php echo $saude_edit['nome'] ?? ''; ?>" required>
    <input type="text" name="categoria" placeholder="Categoria" value="<?php echo $saude_edit['categoria'] ?? ''; ?>" required>
    <input type="text" name="endereco" placeholder="Endereço" value="<?php echo $saude_edit['endereco'] ?? ''; ?>" required>
    <input type="text" name="contato" placeholder="Contato" value="<?php echo $saude_edit['contato'] ?? ''; ?>" required>
    <input type="file" name="imagem">
    <?php if ($saude_edit && $saude_edit['imagem']): ?>
        <img src="modulos/uploads/<?php echo $saude_edit['imagem']; ?>" style="width:100px; margin-top:5px; border-radius:6px;">
    <?php endif; ?>

    <button type="submit" name="<?php echo $saude_edit ? 'alterar' : 'salvar'; ?>" class="btn">
        <?php echo $saude_edit ? 'Alterar' : 'Salvar'; ?>
    </button>

    <?php if ($saude_edit): ?>
        <a href="?modulo=saude&delete=<?php echo $saude_edit['id']; ?>" class="btn btn-danger" onclick="return confirm('Deseja realmente deletar?')">Deletar</a>
    <?php endif; ?>
</form>

<hr>

<!-- Lista de Unidades de Saúde -->
<h3>Lista de Unidades de Saúde</h3>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Categoria</th>
            <th>Endereço</th>
            <th>Contato</th>
            <th>Imagem</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $res = $conn->query("SELECT * FROM saude ORDER BY id DESC");
        while ($s = $res->fetch_assoc()):
        ?>
            <tr>
                <td><?php echo $s['id']; ?></td>
                <td><?php echo htmlspecialchars($s['nome']); ?></td>
                <td><?php echo htmlspecialchars($s['categoria']); ?></td>
                <td><?php echo htmlspecialchars($s['endereco']); ?></td>
                <td><?php echo htmlspecialchars($s['contato']); ?></td>
                <td><?php if ($s['imagem']): ?><img src="modulos/uploads/<?php echo $s['imagem']; ?>" style="width:80px; border-radius:6px;"><?php endif; ?></td>
                <td>
                    <a href="?modulo=saude&edit=<?php echo $s['id']; ?>" class="btn">Alterar</a>
                    <a href="?modulo=saude&delete=<?php echo $s['id']; ?>" class="btn btn-danger" onclick="return confirm('Deseja realmente deletar?')">Deletar</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>