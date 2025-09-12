<?php
session_start();
//Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "dbincaxias";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Erro na conexão: " . $conn->connect_error);

// Verifica login
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

// Pesquisar nas tabelas Turismo, Cultura e Comércio
$sqlTurismo = "SELECT 'Turismo' AS tipo, id, nome AS titulo, categoria FROM turismo WHERE categoria LIKE ?";
$sqlCultura = "SELECT 'Cultura' AS tipo, id, nome AS titulo, categoria FROM cultura WHERE categoria LIKE ?";
$sqlComercio = "SELECT 'Comércio' AS tipo, id, nome AS titulo, categoria FROM comercio WHERE categoria LIKE ?";

$stmt = $conn->prepare("$sqlTurismo UNION ALL $sqlCultura UNION ALL $sqlComercio ORDER BY tipo, titulo");
$like = "%$q%";
$stmt->bind_param("sss", $like, $like, $like);
$stmt->execute();
$res = $stmt->get_result();

$dados = [];
while ($row = $res->fetch_assoc()) {
    $dados[] = $row;
}

echo json_encode($dados);
