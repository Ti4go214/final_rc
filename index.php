<?php
/**
 * @file index.php
 * @brief Dashboard principal com Menu Lateral e Mapa Leaflet.
 * @details Ponto de entrada da aplicação após login. Gere o layout principal,
 *          filtros de pesquisa e inicialização do mapa.
 * @author Tiago Guerra
 * @date 2026
 */

session_start();
/** 
 * @brief Segurança: Verificação de Sessão.
 * @details Redireciona para a página de login caso o utilizador não esteja autenticado.
 */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';

/** @var array $listaPaises Obtém países únicos para o filtro usando DISTINCT. */
$stmtPais = $pdo->query("SELECT DISTINCT pais FROM locais ORDER BY pais ASC");
$listaPaises = $stmtPais->fetchAll();

/** @var array $categorias Lista de categorias para o formulário. */
$stmtCat = $pdo->query("SELECT * FROM categorias ORDER BY nome ASC");
$categorias = $stmtCat->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <title>GeoDados | Sistema de Gestão</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/mapa.css">
</head>

<body>

    <header>
        <span>GeoDados | Bem-vindo, <b><?= htmlspecialchars($_SESSION['user_nome']) ?></b></span>
        <a href="logout.php" class="btn-logout">Sair do Sistema</a>
    </header>

    <div class="main-container">
        <aside class="sidebar">

            <div class="stats-row"
                style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 25px;">
                <div class="stat-card">
                    <span id="totalLocais">0</span>
                    <label>Locais</label>
                </div>
                <div class="stat-card">
                    <span id="totalCategorias">0</span>
                    <label>Categorias</label>
                </div>
            </div>

            <div style="margin-top: 10px; margin-bottom: 25px; display: flex; gap: 10px;">
                <button onclick="abrirFormularioVazio()"
                    style="flex: 1; padding: 12px; background-color: #2563eb; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.3s;">
                    ➕ Novo Local
                </button>
                <a href="locais_lista.php"
                    style="flex: 1; padding: 12px; background-color: #10b981; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; text-decoration: none; text-align: center; transition: background 0.3s; display: flex; align-items: center; justify-content: center;">
                    📋 Ver Todos
                </a>
            </div>
            <div class="filter-group">
                <h3>Pesquisa e Filtros</h3>

                <label for="searchNome">Procurar por Nome</label>
                <input type="text" id="searchNome" placeholder="Ex: Farmácia..." onkeyup="filtrarMapa()"
                    style="margin-bottom: 15px;">

                <label for="filtroPais">Filtrar por País</label>
                <select id="filtroPais" onchange="filtrarMapa()">
                    <option value="">Todos os Países</option>
                    <?php foreach ($listaPaises as $p): ?>
                        <option value="<?= htmlspecialchars($p['pais']) ?>"><?= htmlspecialchars($p['pais']) ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="filtroCategoria">Filtrar por Categoria</label>
                <select id="filtroCategoria" onchange="filtrarMapa()">
                    <option value="">Todas as Categorias</option>
                    <?php foreach ($categorias as $c): ?>
                        <option value="<?= htmlspecialchars($c['nome']) ?>"><?= htmlspecialchars($c['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <hr style="width:100%; border:0; border-top:1px solid #e2e8f0; margin: 10px 0;">

            <?php if ($_SESSION['user_tipo'] === 'admin'): ?>
                <div
                    style="background: #eff6ff; padding: 15px; border-radius: 10px; border: 1px solid #bfdbfe; margin-top: 10px;">
                    <p style="margin:0 0 10px 0; font-size: 0.8rem; color: #1e40af; font-weight: 600;">PAINEL ADMIN</p>
                    <a href="admin_dashboard_graficos.php"
                        style="display: block; width: 100%; padding: 10px; background: #10b981; color: white; text-align: center; border-radius: 8px; text-decoration: none; font-size: 0.9rem; font-weight: 600; margin-bottom: 8px; transition: background 0.2s;">
                        📊 Dashboard Analítico
                    </a>
                    <a href="admin_utilizadores.php"
                        style="display: block; width: 100%; padding: 10px; background: #2563eb; color: white; text-align: center; border-radius: 8px; text-decoration: none; font-size: 0.9rem; font-weight: 600; margin-bottom: 8px; transition: background 0.2s;">
                        👥 Gerir Utilizadores
                    </a>
                    <a href="admin_categorias.php"
                        style="display: block; width: 100%; padding: 10px; background: #6366f1; color: white; text-align: center; border-radius: 8px; text-decoration: none; font-size: 0.9rem; font-weight: 600; transition: background 0.2s;">
                        🏷️ Gerir Categorias
                    </a>
                </div>
            <?php endif; ?>

            <div class="extra-actions" style="margin-top: auto; padding-top: 20px; display: flex; gap: 10px;">
                <button onclick="minhaLocalizacao()" title="Onde estou?"
                    style="flex:1; background:#f1f5f9; color:#475569; box-shadow:none;">📍</button>
                <button onclick="toggleDarkMode()" id="btnDarkMode" title="Alternar Modo Escuro"
                    style="flex:1; background:#1e293b; color:white; box-shadow:none;">🌙</button>
            </div>
        </aside>

        <div id="map"></div>
    </div>

    <?php include 'form_local.php'; ?>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>

    <script>
        /** Variáveis de sessão passadas para o JS */
        const USER_ID = <?= $_SESSION['user_id'] ?>;
        const USER_TIPO = "<?= $_SESSION['user_tipo'] ?>";
    </script>
    <script src="js/mapa.js"></script>
</body>

</html>