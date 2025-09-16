<?php
include '../conexao.php';
$id   = intval($_POST['id']);
$nome = $_POST['nome'];
$desc = $_POST['descricao'];

$stmt = $conn->prepare("UPDATE turismo SET nome=?, descricao=? WHERE id=?");
$stmt->bind_param("ssi", $nome, $desc, $id);
$stmt->execute();

header("Location: ../index.php?modulo=turismo");
exit;
