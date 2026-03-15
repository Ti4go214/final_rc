<?php
/**
 * @file admin_dashboard_graficos.php
 * @brief Dashboard Analítico para Administradores.
 * @details Este ecrã consome os dados de \c api_graficos.php e desenha
 *          os gráficos estatísticos usando a biblioteca \c Chart.js.
 * @author Tiago Guerra
 * @date 2026
 */

session_start();

/** 
 * @brief Segurança: Apenas administradores podem aceder ao dashboard.
 * @details Verifica se o utilizador está logado e se o seu perfil é 'admin'.
 */
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require 'db.php';
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <title>GeoDados | Dashboard Analítico</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <!-- Importar Chart.js via CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #6366f1;
            --bg: #0f172a;
            --text: #f8fafc;
            --card-bg: rgba(255, 255, 255, 0.05);
            --border: rgba(255, 255, 255, 0.1);
        }

        * {
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        header {
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(10px);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .btn-voltar {
            background: #3b82f6;
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-voltar:hover {
            background: #2563eb;
            transform: translateY(-2px);
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            backdrop-filter: blur(10px);
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.08);
        }

        .stat-card h3 {
            font-size: 1rem;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
        }

        .stat-card span {
            font-size: 3rem;
            font-weight: 600;
            color: var(--primary);
            text-shadow: 0 0 20px rgba(99, 102, 241, 0.4);
        }

        .charts-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        @media (max-width: 900px) {
            .charts-row {
                grid-template-columns: 1fr;
            }
        }

        .chart-container {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 15px;
            padding: 25px;
            backdrop-filter: blur(10px);
        }

        .chart-container h3 {
            margin-bottom: 20px;
            font-weight: 600;
            color: #e2e8f0;
            text-align: center;
        }

        /* Loading spinner */
        .loader {
            text-align: center;
            padding: 50px;
            color: #94a3b8;
            font-style: italic;
        }
    </style>
</head>

<body>

    <header>
        <h2>📊 GeoDados | Dashboard Analítico</h2>
        <a href="index.php" class="btn-voltar">← Voltar ao Mapa</a>
    </header>

    <div class="dashboard-container">

        <div id="loading" class="loader">A obter dados em tempo real...</div>

        <div id="dashboard-content" style="display: none;">
            <!-- Cartões rápidos -->
            <div class="stats-row">
                <div class="stat-card">
                    <h3>Total de Locais</h3>
                    <span id="stat_locais">-</span>
                </div>
                <div class="stat-card">
                    <h3>Total de Utilizadores</h3>
                    <span id="stat_users">-</span>
                </div>
            </div>

            <!-- Gráficos -->
            <div class="charts-row">
                <div class="chart-container">
                    <h3>Locais por Categoria</h3>
                    <canvas id="chartCategorias"></canvas>
                </div>

                <div class="chart-container">
                    <h3>Locais criados por Utilizador</h3>
                    <canvas id="chartUtilizadores"></canvas>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            fetch('api_graficos.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status !== 'ok') {
                        alert("Erro: " + data.mensagem);
                        return;
                    }

                    /** Esconder o loader e mostrar o container */
                    document.getElementById('loading').style.display = 'none';
                    document.getElementById('dashboard-content').style.display = 'block';

                    /** 1. Atualizar Estatísticas Rápidas */
                    document.getElementById('stat_locais').innerText = data.estatisticas.total_locais;
                    document.getElementById('stat_users').innerText = data.estatisticas.total_utilizadores;

                    /** Cores para os gráficos para um design atrativo e moderno */
                    const colors = [
                        'rgba(99, 102, 241, 0.8)', // Indigo
                        'rgba(236, 72, 153, 0.8)', // Pink
                        'rgba(16, 185, 129, 0.8)', // Emerald
                        'rgba(245, 158, 11, 0.8)',  // Amber
                        'rgba(14, 165, 233, 0.8)',  // Sky
                        'rgba(139, 92, 246, 0.8)', // Violet
                        'rgba(239, 68, 68, 0.8)',  // Red
                    ];

                    /** 2. Gráfico Locais por Categoria (Doughnut / Pie) */
                    const ctxCat = document.getElementById('chartCategorias').getContext('2d');

                    const labelsCat = data.categorias.map(i => i.categoria || 'Sem Categoria');
                    const valuesCat = data.categorias.map(i => i.total);

                    new Chart(ctxCat, {
                        type: 'doughnut',
                        data: {
                            labels: labelsCat,
                            datasets: [{
                                data: valuesCat,
                                backgroundColor: colors,
                                borderColor: 'rgba(15, 23, 42, 1)',
                                borderWidth: 3
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: { position: 'bottom', labels: { color: '#e2e8f0', padding: 20 } }
                            },
                            cutout: '60%' /** Faz o "buraco" no meio */
                        }
                    });

                    /** 3. Gráfico Locais por Utilizador (Bar) */
                    const ctxUser = document.getElementById('chartUtilizadores').getContext('2d');

                    const labelsUser = data.utilizadores.map(i => i.utilizador || 'Desconhecido');
                    const valuesUser = data.utilizadores.map(i => i.total);

                    new Chart(ctxUser, {
                        type: 'bar',
                        data: {
                            labels: labelsUser,
                            datasets: [{
                                label: 'Nº Locais Adicionados',
                                data: valuesUser,
                                backgroundColor: 'rgba(99, 102, 241, 0.6)',
                                borderColor: 'rgba(99, 102, 241, 1)',
                                borderWidth: 2,
                                borderRadius: 8 /** Cantos arredondados nas barras (estilo moderno) */
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: { stepSize: 1, color: '#94a3b8' },
                                    grid: { color: 'rgba(255, 255, 255, 0.05)' }
                                },
                                x: {
                                    ticks: { color: '#94a3b8' },
                                    grid: { display: false }
                                }
                            }
                        }
                    });

                })
                .catch(err => {
                    console.error('Erro de rede:', err);
                    document.getElementById('loading').innerText = "Erro ao carregar dados conectividade.";
                });
        });
    </script>
</body>

</html>