<?php
/**
 * @file admin_categorias.php
 * @brief Gestão de categorias para administradores.
 * @details Permite listar, criar e editar categorias no sistema.
 * @author Tiago Guerra
 * @date 2026
 */

session_start();
require 'db.php';

/** 
 * @brief Segurança: Acesso Restrito.
 * @details Apenas utilizadores com perfil de 'admin' podem gerir as categorias do sistema.
 */
if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'admin') {
    header("Location: index.php");
    exit;
}

/** 
 * @var string|null $msg 
 * @brief Armazena mensagens de feedback ou erros para exibição.
 */
$msg = $_GET['msg'] ?? null;

/**
 * @brief Lógica de Criação de Novas Categorias.
 * @details Recebe dados via POST e insere uma nova categoria na base de dados.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nova_cat'])) {
    /** @var string $nome Nome da categoria. */
    $nome = $_POST['nome_cat'] ?? '';
    /** @var string $cor Código hexadecimal da cor. */
    $cor = $_POST['cor_cat'] ?? '#000000';
    /** @var string $letras Iniciais para o pin do mapa. */
    $letras = $_POST['letras_cat'] ?? '?';

    if (!empty($nome)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO categorias (nome, cor, letras) VALUES (?, ?, ?)");
            $stmt->execute([$nome, $cor, $letras]);
            header("Location: admin_categorias.php?msg=Categoria criada com sucesso.");
            exit;
        } catch (PDOException $e) {
            $msg = "Erro: " . $e->getMessage();
        }
    }
}

/** 
 * @var array $categorias 
 * @brief Lista de todas as categorias configuradas no sistema.
 */
$stmt = $pdo->query("SELECT * FROM categorias ORDER BY nome ASC");
$categorias = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <title>GeoDados | Gestão de Categorias</title>
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
        }

        .data-table tr {
            background: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.02);
        }

        .data-table td {
            padding: 15px;
            border-top: 1px solid #f1f5f9;
            border-bottom: 1px solid #f1f5f9;
        }

        .cat-color-box {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            display: inline-block;
            vertical-align: middle;
            margin-right: 10px;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        .add-form {
            background: white;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            border: 1px solid #f1f5f9;
        }

        .form-row {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 15px;
            align-items: end;
        }

        .alert {
            padding: 15px;
            border-radius: 10px;
            background: #dcfce7;
            color: #166534;
            margin-bottom: 20px;
            border: 1px solid #bbf7d0;
        }
    </style>
</head>

<body>
    <header>
        <span>Painel Administrativo | <b>Categorias</b></span>
        <a href="index.php" class="btn-logout" style="background:rgba(99, 102, 241, 0.1); color:var(--primary);">Voltar
            ao Mapa</a>
    </header>

    <div class="admin-page">
        <a href="index.php" class="btn-back"
            style="display:inline-block; margin-bottom: 20px; color:var(--primary); text-decoration:none; font-weight:600;">←
            Voltar ao Dashboard</a>

        <?php if ($msg): ?>
            <div class="alert">
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <h1>Gestão de Categorias</h1>

        <div class="add-form">
            <h3 style="margin-top:0;">Adicionar Nova Categoria</h3>
            <form method="POST">
                <div class="form-row">
                    <div class="input-wrapper">
                        <label>Nome da Categoria</label>
                        <input type="text" name="nome_cat" placeholder="Ex: Farmácia" required>
                    </div>
                    <div class="input-wrapper">
                        <label>Cor do Pin</label>
                        <input type="color" name="cor_cat" value="#6366f1" style="height:45px; padding:2px;">
                    </div>
                    <div class="input-wrapper">
                        <label>Iniciais (2-3 Letras)</label>
                        <input type="text" name="letras_cat" placeholder="F" maxlength="3" required>
                    </div>
                    <button type="submit" name="nova_cat" class="btn-save">Adicionar</button>
                </div>
            </form>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Designação</th>
                    <th>Cor Hex</th>
                    <th>Iniciais</th>
                    <th style="text-align: right;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categorias as $c): ?>
                    <tr>
                        <td>
                            <div class="cat-color-box" style="background: <?= $c['cor'] ?>;"></div>
                            <b>
                                <?= htmlspecialchars($c['nome']) ?>
                            </b>
                        </td>
                        <td style="font-family: monospace; color: var(--text-light);">
                            <?= strtoupper($c['cor']) ?>
                        </td>
                        <td><span
                                style="background:#f1f5f9; padding:2px 8px; border-radius:4px; font-weight:600; color:<?= $c['cor'] ?>;">
                                <?= htmlspecialchars($c['letras']) ?>
                            </span></td>
                        <td style="text-align: right;">
                            <small style="color:var(--text-light);">(Edição via SQL recomendada)</small>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>