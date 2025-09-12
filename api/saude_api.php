<?php
header('Content-Type: application/json');
//Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "dbincaxias";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Erro na conexão: " . $conn->connect_error);

$res = $conn->query("SELECT * FROM saude ORDER BY id DESC");
$saude = [];
while ($row = $res->fetch_assoc()) {
    $row['imagem'] = $row['imagem'] ? 'http://localhost/incaxias_web/modulos/uploads/' . $row['imagem'] : null;
    $saude[] = $row;
}

echo json_encode($saude);
