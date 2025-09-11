<?php
include 'verifica_sessao.php';

// Admin pode tudo, editor tem acesso limitado
if ($_SESSION["usuario_nivel"] !== "admin" && $_SESSION["usuario_nivel"] !== "editor") {
    echo "<h2>Acesso negado: apenas administradores e editores.</h2>";
    exit();
}
?>
