<?php

session_start();

// Se não estiver logado, redireciona para o login
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit();
}


header("Content-Type: application/json; charset=UTF-8");
require_once("../config/database.php");

$data = json_decode(file_get_contents("php://input"), true);

$nome = $data["nome"] ?? '';
$email = $data["email"] ?? '';
$senha = $data["senha"] ?? '';
$nivel = $data["nivel"] ?? 'usuario';

// Validações básicas
if (empty($nome) || empty($email) || empty($senha)) {
    echo json_encode(["status" => "error", "message" => "Preencha todos os campos"]);
    exit;
}

// Verifica se email já existe
$sql = "SELECT id FROM usuarios WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Email já cadastrado"]);
    exit;
}

// Criptografa senha
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

// Insere no banco
$sql = "INSERT INTO usuarios (nome, email, senha, nivel) VALUES (?,?,?,?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $nome, $email, $senhaHash, $nivel);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Usuário cadastrado com sucesso"]);
} else {
    echo json_encode(["status" => "error", "message" => "Erro ao cadastrar usuário"]);
}
