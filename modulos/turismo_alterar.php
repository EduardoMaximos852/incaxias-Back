<?php
$id = intval($_GET['id'] ?? 0);
$registro = $conn->query("SELECT * FROM turismo WHERE id=$id")->fetch_assoc();
?>
<h2>Alterar Turismo</h2>

<form action="acoes/turismo_alterar_salvar.php" method="post">
    <input type="hidden" name="id" value="<?= $registro['id'] ?>">
    <div>
        <label>Nome:</label>
        <input type="text" name="nome" value="<?= htmlspecialchars($registro['nome']) ?>" required>
    </div>
    <div>
        <label>Descrição:</label>
        <textarea name="descricao" required><?= htmlspecialchars($registro['descricao']) ?></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Salvar Alteração</button>
</form>
