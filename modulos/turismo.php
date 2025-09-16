<?php
// Conexão já existente em $conn
$sql = "SELECT id, nome, descricao FROM turismo ORDER BY id DESC";
$result = $conn->query($sql);
?>

<table class="table">
    <thead>
        <tr>
            <th>Nome</th>
            <th>Descrição</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['nome']) ?></td>
            <td><?= htmlspecialchars($row['descricao']) ?></td>
            <td>
                <!-- Botão Alterar -->
                <a href="?modulo=turismo_alterar&id=<?= $row['id'] ?>"
                   class="btn btn-warning btn-sm">
                   Alterar
                </a>

                <!-- Botão Deletar -->
                <a href="acoes/turismo_deletar.php?id=<?= $row['id'] ?>"
                   onclick="return confirm('Tem certeza que deseja deletar este registro?');"
                   class="btn btn-danger btn-sm">
                   Deletar
                </a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
