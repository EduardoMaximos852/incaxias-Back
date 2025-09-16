<?php

session_start();
$host = "localhost";
$user = "root";
$pass = "";
$db   = "dbincaxias";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Erro na conexão: " . $conn->connect_error);



$local = $_GET['local'] ?? '';


// Agora você pode continuar usando $local
if ($local !== '') {

    $stmt = $conn->prepare("SELECT * FROM cultura WHERE local = ?");
    $stmt->bind_param("s", $local);
    $stmt->execute();
    $res = $stmt->get_result();
    $cultura = $res->fetch_assoc();
    $stmt->close();
} else {
    $cultura = null;
}


// Verifica login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../public/login.php");
    exit();
}

// Pega dados do usuário
$usuario_id = $_SESSION['usuario_id'];
$usuario_nome = $_SESSION['usuario_nome'] ?? '';
$usuario_email = $_SESSION['usuario_email'] ?? '';
$usuario_nivel = $_SESSION['usuario_nivel'] ?? 'usuario';

// Determina qual módulo exibir
$modulo = $_GET['modulo'] ?? 'home';

// Função para contar registros de uma tabela
function contar($conn, $tabela)
{
    $res = $conn->query("SELECT COUNT(*) AS total FROM $tabela");
    $row = $res->fetch_assoc();
    return $row['total'] ?? 0;
}

if ($modulo === 'turismo_deletar' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Remove imagem do servidor
    $res = $conn->query("SELECT ponto_imagens FROM pontos_turisticos WHERE id = $id");
    if ($row = $res->fetch_assoc()) {
        if ($row['imagem'] && file_exists("modulos/uploads/" . $row['imagem'])) {
            unlink("modulos/uploads/" . $row['imagem']);
        }
    }

    // Deleta registro do banco
    $stmt = $conn->prepare("DELETE FROM pontos_turisticos WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    echo "<p>Ponto turístico deletado com sucesso!</p>";
    echo "<a href='?modulo=turismo'>Voltar</a>";
}



?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard • InCaxias</title>
    <link rel="stylesheet" href="../css/incaxias.css">
    <style>
        /* Adições de estilo para cards e tabelas */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.03);
            font-size: 14px;
            color: #E6EDF3
        }

        th {
            color: var(--muted);
            font-size: 12px
        }

        table img {
            width: 80px;
            border-radius: 6px
        }
    </style>
</head>

<body>
    <div class="app">
        <?php include '../public/sidebar.php'; ?>
        <main class="main">
            <!-- Topbar -->
            <div class="topbar">
                <div class="search">
                    </svg>

                </div>

                <div class="user">
                    <div class="user-info"><span><?php echo htmlspecialchars($usuario_nome); ?></span><small><?php echo htmlspecialchars($usuario_email); ?> • <?php echo htmlspecialchars($usuario_nivel); ?></small></div>
                    <div class="avatar"><?php echo strtoupper(substr($usuario_nome, 0, 1)); ?></div>
                    <a href="../public/logout.php" class="btn ghost btn-small" style="margin-left:10px;">Sair</a>
                </div>
            </div>

            <!-- Conteúdo -->
            <?php if ($modulo === 'home'): ?>
                <div class="grid">
                    <div class="card">
                        <div class="kpi">
                            <div>
                                <div class="title">Usuários ativos</div>
                                <div class="value"><?php echo contar($conn, 'usuarios'); ?></div>
                            </div>
                            <?php if ($usuario_nivel === 'admin'): ?>
                                <div><a href="?modulo=usuarios" class="btn">Gerenciar</a></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card">
                        <div class="kpi">
                            <div>
                                <div class="title">Pontos turísticos</div>
                                <div class="value"><?php echo contar($conn, 'pontos_turisticos'); ?></div>
                            </div>
                            <div><a href="visualizar_turismo.php" class="btn">Ver</a></div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="kpi">
                            <div>
                                <div class="title">Educaçao</div>
                                <div class="value"><?php echo contar($conn, 'turismo'); ?></div>
                            </div>
                            <div><a href="?modulo=turismo" class="btn">Ver</a></div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="kpi">
                            <div>
                                <div class="title">Saúde</div>
                                <div class="value"><?php echo contar($conn, 'turismo'); ?></div>
                            </div>
                            <div><a href="?modulo=turismo" class="btn">Ver</a></div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="kpi">
                            <div>
                                <div class="title">Prefeitura</div>
                                <div class="value"><?php echo contar($conn, 'turismo'); ?></div>
                            </div>
                            <div><a href="?modulo=turismo" class="btn">Ver</a></div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="kpi">
                            <div>
                                <div class="title">Jornalismo</div>
                                <div class="value"><?php echo contar($conn, 'turismo'); ?></div>
                            </div>
                            <div><a href="?modulo=turismo" class="btn">Ver</a></div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="kpi">
                            <div>
                                <div class="title">Utilidade Pública</div>
                                <div class="value"><?php echo contar($conn, 'turismo'); ?></div>
                            </div>
                            <div><a href="?modulo=turismo" class="btn">Ver</a></div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="kpi">
                            <div>
                                <div class="title">Cultura</div>
                                <div class="value"><?php echo contar($conn, 'cultura'); ?></div>
                            </div>
                            <div><a href="visualizar_cultura.php" class="btn">Ver</a></div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="kpi">
                            <div>
                                <div class="title">Comércio</div>
                                <div class="value"><?php echo contar($conn, 'comercio'); ?></div>
                            </div>
                            <div><a href="visualizar_comercio.php" class="btn">Ver</a></div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Lista de Turismo -->
            <?php if ($modulo === 'turismo'): ?>
                <h2>Pontos Turísticos</h2>
                <input type="text" id="searchTurismo" placeholder="Pesquisar por nome ou categoria..." style="margin-bottom:10px; padding:8px; width:100%; max-width:400px;">
                <div id="searchResultsTurismo">
                    <table id="turismoTable">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Categoria</th>
                                    <th>Endereço</th>
                                    <th>Imagem</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $res = $conn->query("SELECT * FROM turismo ORDER BY id DESC");
                                while ($t = $res->fetch_assoc()):
                                ?>
                                    <tr>
                                        <td><?php echo $t['id']; ?></td>
                                        <td><?php echo htmlspecialchars($t['nome']); ?></td>
                                        <td><?php echo htmlspecialchars($t['categoria']); ?></td>
                                        <td><?php echo htmlspecialchars($t['endereco']); ?></td>
                                        <td><?php if ($t['imagem']): ?><img src="modulos/uploads/<?php echo $t['imagem']; ?>"><?php endif; ?></td>
                                        <td>
                                            <a href="?modulo=turismo_editar&id=<?php echo $t['id']; ?>" class="btn btn-small" style="margin-right:5px;">Alterar</a>
                                            <a href="?modulo=turismo_deletar&id=<?php echo $t['id']; ?>" class="btn btn-small ghost" onclick="return confirm('Tem certeza que deseja deletar este ponto turístico?');">Deletar</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                        <div><a href="../public/cadastrar_turismo.php" class="btn">Cadastrar</a></div>
                    <?php endif; ?>

                    <!-- Lista de Cultura -->
                    <?php if ($modulo === 'cultura'): ?>

                        <h2>Eventos Culturais</h2>
                        <input type="text" id="searchCultura" placeholder="Pesquisar por nome ou categoria..." style="margin-bottom:10px; padding:8px; width:100%; max-width:400px;">
                        <div id="searchResultsCultura">
                            <table id="culturaTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Categoria</th>
                                        <th>Descrição</th>
                                        <th>Endereço</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $res = $conn->query("SELECT * FROM cultura ORDER BY id DESC");
                                    while ($c = $res->fetch_assoc()):
                                    ?>
                                        <tr>
                                            <td><?php echo $c['id']; ?></td>
                                            <td><?php echo htmlspecialchars($c['nome']); ?></td>
                                            <td><?php echo htmlspecialchars($c['categoria']); ?></td>
                                            <td><?php echo htmlspecialchars($c['descricao']); ?></td>
                                            <td><?php echo htmlspecialchars($c['endereco']); ?></td>
                                            <td>
                                                <a href="?modulo=cultura_editar&id=<?php echo $c['id']; ?>" class="btn btn-small" style="margin-right:5px;">Alterar</a>
                                                <a href="?modulo=cultura_deletar&id=<?php echo $c['id']; ?>" class="btn btn-small ghost" onclick="return confirm('Tem certeza que deseja deletar este evento cultural?');">Deletar</a>
                                            </td>

                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>

                    <?php endif; ?>

                    <!-- Lista de Comércio -->
                    <?php if ($modulo === 'comercio'): ?>

                        <h2>Comércios Local</h2>
                        <div class="search-container" style="margin-bottom:15px;">
                            <input type="text" id="searchComercio" placeholder="Pesquisar por nome, categoria ou endereço..." style="margin-bottom:10px; padding:8px; width:100%; max-width:400px;">
                            <div id="searchResultsComercio">
                                <table id="comercioTable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nome</th>
                                            <th>Categoria</th>
                                            <th>Endereço</th>
                                            <th>Contato</th>
                                            <th>Imagem</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $res = $conn->query("SELECT * FROM comercio ORDER BY id DESC");
                                        while ($co = $res->fetch_assoc()):
                                        ?>
                                            <tr>
                                                <td><?php echo $co['id']; ?></td>
                                                <td><?php echo htmlspecialchars($co['nome']); ?></td>
                                                <td><?php echo htmlspecialchars($co['categoria']); ?></td>
                                                <td><?php echo htmlspecialchars($co['endereco']); ?></td>
                                                <td><?php echo htmlspecialchars($co['contato']); ?></td>
                                                <td><?php if ($co['imagem']): ?><img src="modulos/uploads/<?php echo $co['imagem']; ?>"><?php endif; ?></td>
                                                <td>
                                                    <a href="?modulo=comercio_editar&id=<?php echo $co['id']; ?>" class="btn btn-small" style="margin-right:5px;">Alterar</a>
                                                    <a href="?modulo=comercio_deletar&id=<?php echo $co['id']; ?>" class="btn btn-small ghost" onclick="return confirm('Tem certeza que deseja deletar este comércio?');">Deletar</a>
                                                </td>

                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                        <!-- Lista de Educação -->

                        <?php if ($modulo === 'educacao'): ?>

                            <h2>Educação</h2>

                            <div class="search-container" style="margin-bottom:15px;">
                                <svg class="search-icon" viewBox="0 0 24 24" fill="none">
                                    <path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <circle cx="11" cy="11" r="6" stroke="currentColor" stroke-width="1.5" />
                                </svg>
                                <input type="text" id="searchEducacao" placeholder="Pesquisar por nome ou categoria..." autocomplete="off">
                            </div>

                            <div id="searchResultsEducacao">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nome</th>
                                            <th>Categoria</th>
                                            <th>Endereço</th>
                                            <th>Contato</th>
                                            <th>Imagem</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $res = $conn->query("SELECT * FROM educacao ORDER BY id DESC");
                                        while ($e = $res->fetch_assoc()):
                                        ?>
                                            <tr>
                                                <td><?php echo $e['id']; ?></td>
                                                <td><?php echo htmlspecialchars($e['nome']); ?></td>
                                                <td><?php echo htmlspecialchars($e['categoria']); ?></td>
                                                <td><?php echo htmlspecialchars($e['endereco']); ?></td>
                                                <td><?php echo htmlspecialchars($e['contato']); ?></td>
                                                <td><?php if ($e['imagem']): ?><img src="modulos/uploads/<?php echo $e['imagem']; ?>" style="width:80px; border-radius:6px;"><?php endif; ?></td>
                                                <td>
                                                    <a href="?modulo=educacao_editar&id=<?php echo $e['id']; ?>" class="btn btn-small" style="margin-right:5px;">Alterar</a>
                                                    <a href="?modulo=educacao_deletar&id=<?php echo $e['id']; ?>" class="btn btn-small ghost" onclick="return confirm('Tem certeza que deseja deletar este registro?');">Deletar</a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>

                        <?php endif; ?>
                        <!-- Lista de Saúde -->
                        <?php if ($modulo === 'saude'): ?>
                            <?php include '../saude_form.php'; ?>


                            <h2>Saúde</h2>

                            <div class="search-container" style="margin-bottom:15px;">
                                <svg class="search-icon" viewBox="0 0 24 24" fill="none">
                                    <path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <circle cx="11" cy="11" r="6" stroke="currentColor" stroke-width="1.5" />
                                </svg>
                                <input type="text" id="searchSaude" placeholder="Pesquisar por nome ou categoria..." autocomplete="off">
                            </div>

                            <div id="searchResultsSaude">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nome</th>
                                            <th>Categoria</th>
                                            <th>Endereço</th>
                                            <th>Contato</th>
                                            <th>Imagem</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $res = $conn->query("SELECT * FROM saude ORDER BY id DESC");
                                        while ($s = $res->fetch_assoc()):
                                        ?>
                                            <tr>
                                                <td><?php echo $s['id']; ?></td>
                                                <td><?php echo htmlspecialchars($s['nome']); ?></td>
                                                <td><?php echo htmlspecialchars($s['categoria']); ?></td>
                                                <td><?php echo htmlspecialchars($s['endereco']); ?></td>
                                                <td><?php echo htmlspecialchars($s['contato']); ?></td>
                                                <td><?php if ($s['imagem']): ?><img src="modulos/uploads/<?php echo $s['imagem']; ?>" style="width:80px; border-radius:6px;"><?php endif; ?></td>
                                                <td>
                                                    <a href="?modulo=saude_editar&id=<?php echo $s['id']; ?>" class="btn btn-small" style="margin-right:5px;">Alterar</a>
                                                    <a href="?modulo=saude_deletar&id=<?php echo $s['id']; ?>" class="btn btn-small ghost" onclick="return confirm('Tem certeza que deseja deletar este registro?');">Deletar</a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                        <!-- Lista de Prefeitura -->
                        <?php if ($modulo === 'prefeitura'): ?>
                            <h2>Prefeitura</h2>
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Departamento</th>
                                        <th>Responsável</th>
                                        <th>Endereço</th>
                                        <th>Contato</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $res = $conn->query("SELECT * FROM prefeitura ORDER BY id DESC");
                                    while ($p = $res->fetch_assoc()):
                                    ?>
                                        <tr>
                                            <td><?php echo $p['id']; ?></td>
                                            <td><?php echo htmlspecialchars($p['departamento']); ?></td>
                                            <td><?php echo htmlspecialchars($p['responsavel']); ?></td>
                                            <td><?php echo htmlspecialchars($p['endereco']); ?></td>
                                            <td><?php echo htmlspecialchars($p['contato']); ?></td>
                                            <td>
                                                <a href="?modulo=prefeitura_editar&id=<?php echo $p['id']; ?>" class="btn btn-small" style="margin-right:5px;">Alterar</a>
                                                <a href="?modulo=prefeitura_deletar&id=<?php echo $p['id']; ?>" class="btn btn-small ghost" onclick="return confirm('Tem certeza que deseja deletar este registro?');">Deletar</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>

                        <!-- Lista de Jornalismo -->
                        <?php if ($modulo === 'jornalismo'): ?>
                            <h2>Jornalismo</h2>

                            <div class="search-container" style="margin-bottom:15px;">
                                <svg class="search-icon" viewBox="0 0 24 24" fill="none">
                                    <path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <circle cx="11" cy="11" r="6" stroke="currentColor" stroke-width="1.5" />
                                </svg>
                                <input type="text" id="searchJornalismo" placeholder="Pesquisar por nome ou categoria..." autocomplete="off">
                            </div>

                            <div id="searchResultsJornalismo">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nome</th>
                                            <th>Categoria</th>
                                            <th>Endereço</th>
                                            <th>Contato</th>
                                            <th>Imagem</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $res = $conn->query("SELECT * FROM jornalismo ORDER BY id DESC");
                                        while ($j = $res->fetch_assoc()):
                                        ?>
                                            <tr>
                                                <td><?php echo $j['id']; ?></td>
                                                <td><?php echo htmlspecialchars($j['nome']); ?></td>
                                                <td><?php echo htmlspecialchars($j['categoria']); ?></td>
                                                <td><?php echo htmlspecialchars($j['endereco']); ?></td>
                                                <td><?php echo htmlspecialchars($j['contato']); ?></td>
                                                <td><?php if ($j['imagem']): ?><img src="modulos/uploads/<?php echo $j['imagem']; ?>" style="width:80px; border-radius:6px;"><?php endif; ?></td>
                                                <td>
                                                    <a href="?modulo=jornalismo_editar&id=<?php echo $j['id']; ?>" class="btn btn-small" style="margin-right:5px;">Alterar</a>
                                                    <a href="?modulo=jornalismo_deletar&id=<?php echo $j['id']; ?>" class="btn btn-small ghost" onclick="return confirm('Tem certeza que deseja deletar este registro?');">Deletar</a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                        <!-- Lista de Utilidades Públicas -->
                        <?php if ($modulo === 'utilidades'): ?>
                            <h2>Utilidades Públicas</h2>
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Categoria</th>
                                        <th>Endereço</th>
                                        <th>Contato</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $res = $conn->query("SELECT * FROM utilidades ORDER BY id DESC");
                                    while ($u = $res->fetch_assoc()):
                                    ?>
                                        <tr>
                                            <td><?php echo $u['id']; ?></td>
                                            <td><?php echo htmlspecialchars($u['nome']); ?></td>
                                            <td><?php echo htmlspecialchars($u['categoria']); ?></td>
                                            <td><?php echo htmlspecialchars($u['endereco']); ?></td>
                                            <td><?php echo htmlspecialchars($u['contato']); ?></td>
                                            <td>
                                                <a href="?modulo=utilidades_editar&id=<?php echo $u['id']; ?>" class="btn btn-small" style="margin-right:5px;">Alterar</a>
                                                <a href="?modulo=utilidades_deletar&id=<?php echo $u['id']; ?>" class="btn btn-small ghost" onclick="return confirm('Tem certeza que deseja deletar este registro?');">Deletar</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>


                        <!-- Lista de Usuários (apenas admin) -->
                        <?php if ($modulo === 'usuarios' && $usuario_nivel === 'admin'): ?>
                            <h2>Usuários</h2>
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Nível</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $res = $conn->query("SELECT * FROM usuarios ORDER BY id DESC");
                                    while ($u = $res->fetch_assoc()):
                                    ?>
                                        <tr>
                                            <td><?php echo $u['id']; ?></td>
                                            <td><?php echo htmlspecialchars($u['nome']); ?></td>
                                            <td><?php echo htmlspecialchars($u['email']); ?></td>
                                            <td><?php echo htmlspecialchars($u['nivel']); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>


        </main>
    </div>
    <!-- Scripts de busca -->

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // Função para busca genérica
            function setupSearch(inputId, resultsDivId, apiEndpoint) {
                const input = document.getElementById(inputId);
                const resultsDiv = document.getElementById(resultsDivId);

                if (!input) return;

                input.addEventListener("keyup", async function() {
                    const query = input.value.trim();
                    if (query.length < 1) {
                        location.reload(); // ou recarregar tabela original se preferir
                        return;
                    }

                    const res = await fetch(`../api/${apiEndpoint}.php?q=${encodeURIComponent(query)}`);
                    const data = await res.json();

                    if (!data.length) {
                        resultsDiv.innerHTML = "<p>Nenhum registro encontrado.</p>";
                        return;
                    }

                    // Monta tabela dinamicamente
                    let html = "<table><thead><tr>";
                    if (apiEndpoint === "searchCultura") {
                        html += "<th>ID</th><th>Nome</th><th>Categoria</th><th>Descrição</th><th>Endereço</th>";
                    } else if (apiEndpoint === "searchComercio") {
                        html += "<th>ID</th><th>Nome</th><th>Categoria</th><th>Endereço</th><th>Contato</th><th>Imagem</th>";
                    } else if (apiEndpoint === "searchTurismo") {
                        html += "<th>ID</th><th>Nome</th><th>Categoria</th><th>Endereço</th><th>Imagem</th>";
                    }
                    html += "</tr></thead><tbody>";

                    data.forEach(row => {
                        html += "<tr>";
                        for (const key in row) {
                            if (key === "imagem") {
                                html += `<td>${row[key] ? '<img src="modulos/uploads/'+row[key]+'">' : ''}</td>`;
                            } else {
                                html += `<td>${row[key]}</td>`;
                            }
                        }
                        html += "</tr>";
                    });

                    html += "</tbody></table>";
                    resultsDiv.innerHTML = html;
                });
            }

            // Inicializa para cada módulo
            setupSearch("searchCultura", "searchResultsCultura", "search_cultura");
            setupSearch("searchComercio", "searchResultsComercio", "search_comercio");
            setupSearch("searchTurismo", "searchResultsTurismo", "search_turismo");
        });
    </script>


</body>

</html>