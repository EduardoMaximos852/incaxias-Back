<?php
include '../conexao.php';
$id = intval($_GET['id'] ?? 0);

$stmt = $conn->prepare("DELETE FROM turismo WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: ../index.php?modulo=turismo");
exit;
