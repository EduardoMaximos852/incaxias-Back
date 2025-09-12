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

// Busca no banco de dados: nome ou categoria
$stmt = $conn->prepare("SELECT * FROM cultura WHERE nome LIKE ? OR categoria LIKE ? ORDER BY id DESC");
$like = "%$q%";
$stmt->bind_param("ss", $like, $like);
$stmt->execute();
$res = $stmt->get_result();

$dados = [];
while ($row = $res->fetch_assoc()) {
    $dados[] = $row;
}

echo json_encode($dados);
