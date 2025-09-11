<?php
// dashboard.php
session_start();


// Verifica login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// 2) Conexão com o banco
include '../config/database.php';

// 3) Busca dados do usuário logado (nome e nível)
$stmt = $conn->prepare("SELECT nome, email, nivel FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$userData = $stmt->get_result()->fetch_assoc();
$nivel = $userData['nivel'];
?>
<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>InCaxias • Dashboard Seguro</title>
    <link rel="stylesheet" href="../css/incaxias.css">
</head>

<body>
    <div class="app">

        <?php include 'sidebar.php'; ?>

        <main class="main">
            <!-- Topbar com usuário e botão Sair -->
            <div class="topbar">
                <div class="search">
                    <svg style="opacity:0.8;margin-right:10px" width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <circle cx="11" cy="11" r="6" stroke="currentColor" stroke-width="1.5" />
                    </svg>
                    <input placeholder="Pesquisar no painel…" />
                </div>

                <div class="user">
                    <div class="user-info">
                        <span><?php echo htmlspecialchars($userData['nome']); ?></span>
                        <small><?php echo htmlspecialchars($userData['email']); ?> • <?php echo ucfirst($nivel); ?></small>
                    </div>
                    <div class="avatar"><?php echo strtoupper(substr($userData['nome'], 0, 1)); ?></div>
                    <a href="logout.php" class="btn ghost" style="margin-left:12px;">Sair</a>
                </div>
            </div>

            <!-- Cards principais -->
            <div class="grid">
                <div class="card">
                    <div class="kpi">
                        <div>
                            <div class="title">Usuários ativos</div>
                            <?php
                            $qtd = $conn->query("SELECT COUNT(*) AS total FROM usuarios")->fetch_assoc();
                            ?>
                            <div class="value"><?php echo $qtd['total']; ?></div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="kpi">
                        <div>
                            <div class="title">Pontos turísticos</div>
                            <?php
                            $qtdTur = $conn->query("SELECT COUNT(*) AS total FROM pontos_turisticos")->fetch_assoc();
                            ?>
                            <div class="value"><?php echo $qtdTur['total'] ?? 0; ?></div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="kpi">
                        <div>
                            <div class="title">Último acesso</div>
                            <div class="value"><?php echo date('d/m/Y H:i'); ?></div>
                        </div>
                    </div>
                </div>

                <!-- Área exclusiva para ADMIN -->
                <?php if ($nivel === 'admin'): ?>
                    <div class="card large">
                        <h3>Administração</h3>
                        <p>Somente administradores visualizam este bloco.</p>
                        <div style="display:flex;gap:12px;margin-top:12px;flex-wrap:wrap;">
                            <a href="gerenciar_usuarios.php" class="btn">Gerenciar Usuários</a>
                            <a href="turismo.php" class="btn ghost">Gerenciar Pontos Turísticos</a>
                            <!-- adicione outros links administrativos -->
                        </div>
                    </div>
                <?php endif; ?>
            </div>

        </main>
    </div>
</body>

</html>