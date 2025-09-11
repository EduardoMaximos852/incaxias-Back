<?php
include 'verifica_sessao.php';

// Se não for admin, bloqueia
if ($_SESSION["usuario_nivel"] !== "admin") {
    echo "<h2>Acesso negado: apenas administradores.</h2>";
    exit();
}
?>
