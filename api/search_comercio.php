<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Não autorizado']);
    exit();
}

$q = $_GET['q'] ?? '';
$q = trim($q);

header('Content-Type: application/json');

if ($q === '') {
    echo json_encode([]);
    exit();
}

// Busca por nome, categoria ou endereço
$stmt = $conn->prepare("SELECT * FROM comercio WHERE nome LIKE ? OR categoria LIKE ? OR endereco LIKE ? ORDER BY id DESC");
$like = "%$q%";
$stmt->bind_param("sss", $like, $like, $like);
$stmt->execute();
$res = $stmt->get_result();

$dados = [];
while ($row = $res->fetch_assoc()) {
    $dados[] = $row;
}

echo json_encode($dados);
