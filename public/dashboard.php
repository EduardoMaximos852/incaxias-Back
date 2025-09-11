<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "";
$db   = "dbincaxias";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Erro na conexão: " . $conn->connect_error);

// Verifica conexão
if (!$conn) die("Falha na conexão: " . mysqli_connect_error());

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
                    <svg style="opacity:0.8;margin-right:10px" width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <circle cx="11" cy="11" r="6" stroke="currentColor" stroke-width="1.5" />
                    </svg>
                    <input placeholder="Pesquisar...">
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
                            <div><a href="?modulo=cultura" class="btn">Ver</a></div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="kpi">
                            <div>
                                <div class="title">Comércio</div>
                                <div class="value"><?php echo contar($conn, 'comercio'); ?></div>
                            </div>
                            <div><a href="?modulo=comercio" class="btn">Ver</a></div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Lista de Turismo -->
            <?php if ($modulo === 'turismo'): ?>
                <h2>Pontos Turísticos</h2>
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
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <!-- Lista de Cultura -->
            <?php if ($modulo === 'cultura'): ?>
                <h2>Eventos Culturais</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Categoria</th>
                            <th>Local</th>
                            <th>Imagem</th>
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
                                <td><?php echo htmlspecialchars($c['local']); ?></td>
                                <td><?php if ($c['imagem']): ?><img src="modulos/uploads/<?php echo $c['imagem']; ?>"><?php endif; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <!-- Lista de Comércio -->
            <?php if ($modulo === 'comercio'): ?>
                <h2>Comércios</h2>
                <table>
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
</body>

</html>