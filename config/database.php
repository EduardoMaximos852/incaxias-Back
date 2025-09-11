<?php
$host = "localhost";
$user = "root";      // usuário padrão do MySQL no XAMPP
$pass = "";          // senha (definir depois por segurança)
$db   = "dbincaxias";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

$conn->set_charset("utf8");
