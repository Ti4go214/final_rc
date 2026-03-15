<?php
/**
 * @file locais_lista.php
 * @brief Lista tabular de todos os locais no sistema.
 * @details Esta página permite visualizar todos os locais registados no mapa em formato de tabela.
 * @author Tiago Guerra
 * @date 2026
 */

session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

/** 
 * @brief Extração de Dados: Obtém todos os locais com informação agregada.
 * @details Realiza JOINs com categorias e utilizadores, além de sub-query para média de estrelas.
 */
$sql = "
    SELECT 
        l.*, 
        c.nome AS categoria_nome, 
        c.cor AS categoria_cor,
        u.nome AS utilizador_nome,
        (SELECT ROUND(AVG(classificacao), 1) FROM avaliacoes a WHERE a.local_id = l.id) AS media_estrelas
    FROM locais l 
    JOIN categorias c ON l.categoria_id = c.id
    LEFT JOIN utilizadores u ON l.criado_por = u.id
    ORDER BY l.nome ASC
";
$stmt = $pdo->query($sql);
$locais = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <title>GeoDados | Lista de Locais</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/mapa.css">
    <style>
        .admin-page {
            padding: 40px;
            overflow-y: auto;
            flex: 1;
        }

        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
            margin-top: 20px;
        }

        .data-table th {
            text-align: left;
            padding: 15px;
            color: var(--text-light);
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 1px;
        }

        .data-table tr {
            background: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.02);
            transition: transform 0.2s;
        }

        .data-table tr:hover {
            transform: scale(1.005);
        }

        .data-table td {
            padding: 15px;
            border-top: 1px solid #f1f5f9;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.9rem;
        }

        .data-table td:first-child {
            border-left: 1px solid #f1f5f9;
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
        }

        .data-table td:last-child {
            border-right: 1px solid #f1f5f9;
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
        }

        .badge {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            color: white;
        }

        .btn-back {
            display: inline-block;
            margin-bottom: 20px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <header>
        <span>Listagem de <b>Locais</b></span>
        <a href="index.php" class="btn-logout" style="background:rgba(99, 102, 241, 0.1); color:var(--primary);">Voltar
            ao Mapa</a>
    </header>

    <div class="admin-page">
        <a href="index.php" class="btn-back">← Voltar ao Mapa</a>

        <div style="display:flex; justify-content: space-between; align-items: center;">
            <h1 style="margin:0;">Todos os Locais Regressados</h1>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Nome do Local</th>
                    <th>Categoria</th>
                    <th>Localidade</th>
                    <th>Avaliação</th>
                    <th>Adicionado Por</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($locais as $l): ?>
                    <tr>
                        <td><b>
                                <?= htmlspecialchars($l['nome']) ?>
                            </b></td>
                        <td>
                            <span class="badge" style="background: <?= htmlspecialchars($l['categoria_cor']) ?>">
                                <?= htmlspecialchars($l['categoria_nome']) ?>
                            </span>
                        </td>
                        <td>
                            <?= htmlspecialchars($l['cidade']) ?>,
                            <?= htmlspecialchars($l['pais']) ?>
                        </td>
                        <td>
                            <span style="color:#eab308; font-weight:bold;">
                                ★
                                <?= $l['media_estrelas'] ? $l['media_estrelas'] : 'S/A' ?>
                            </span>
                        </td>
                        <td style="color: var(--text-light);">
                            <?= htmlspecialchars($l['utilizador_nome'] ?? 'Sistema') ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($locais)): ?>
                    <tr>
                        <td colspan="5" style="text-align:center; color: var(--text-light);">Nenhum local inserido no mapa
                            ainda.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>