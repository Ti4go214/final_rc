<?php
/**
 * @file admin_utilizadores.php
 * @brief Gestão de utilizadores para administradores.
 * @details Permite listar, editar e eliminar utilizadores da base de dados.
 * @author Tiago Guerra
 * @date 2026
 */

session_start();
require 'db.php';

/** 
 * @brief Segurança: Apenas administradores podem aceder a esta página.
 * @details Verifica a existência de uma sessão ativa e se o tipo de utilizador é 'admin'.
 */
if (!isset($_SESSION['user_id']) || $_SESSION['user_tipo'] !== 'admin') {
    header("Location: index.php");
    exit;
}

/**
 * @var string|null $msg
 * @brief Armazena mensagens de feedback passadas via URL para exibição ao utilizador.
 */
$msg = $_GET['msg'] ?? null;

/** 
 * @brief Lógica de Eliminação de Utilizadores.
 * @details Processa o pedido de remoção de um utilizador via parâmetro GET 'eliminar'.
 */
if (isset($_GET['eliminar'])) {
    /** @var int $id_del ID do utilizador a eliminar, convertido para inteiro para segurança. */
    $id_del = (int) $_GET['eliminar'];

    /** 
     * @brief Impedir que um administrador elimine a sua própria conta por segurança.
     * @details Esta regra de negócio garante que sempre exista pelo menos um admin no sistema para evitar o bloqueio de acesso administrativo.
     */
    if ($id_del !== $_SESSION['user_id']) {
        $stmtDel = $pdo->prepare("DELETE FROM utilizadores WHERE id = ?");
        $stmtDel->execute([$id_del]);
        header("Location: admin_utilizadores.php?msg=Utilizador removido com sucesso.");
        exit;
    } else {
        $msg = "Erro: Não podes eliminar a tua própria conta.";
    }
}


/**
 * @var array $utilizadores
 * @brief Lista de todos os utilizadores registados, ordenada por nome.
 */
$stmt = $pdo->query("SELECT * FROM utilizadores ORDER BY nome ASC");
$utilizadores = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <title>GeoDados | Gestão de Utilizadores</title>
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
        }

        .badge-admin {
            background: #fee2e2;
            color: #b91c1c;
        }

        .badge-normal {
            background: #f0fdf4;
            color: #166534;
        }

        .btn-back {
            display: inline-block;
            margin-bottom: 20px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
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
        <span>Painel Administrativo | <b>Utilizadores</b></span>
        <a href="index.php" class="btn-logout" style="background:rgba(99, 102, 241, 0.1); color:var(--primary);">Voltar
            ao Mapa</a>
    </header>

    <div class="admin-page">
        <a href="index.php" class="btn-back">← Voltar ao Dashboard</a>

        <?php if ($msg): ?>
            <div class="alert">
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <div style="display:flex; justify-content: space-between; align-items: center;">
            <h1 style="margin:0;">Gestão de Utilizadores</h1>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Tipo</th>
                    <th>Criado em</th>
                    <th style="text-align: right;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($utilizadores as $u): ?>
                    <tr>
                        <td><b>
                                <?= htmlspecialchars($u['nome']) ?>
                            </b></td>
                        <td>
                            <?= htmlspecialchars($u['email']) ?>
                        </td>
                        <td>
                            <span class="badge <?= $u['tipo'] === 'admin' ? 'badge-admin' : 'badge-normal' ?>">
                                <?= strtoupper($u['tipo']) ?>
                            </span>
                        </td>
                        <td style="color: var(--text-light); font-size: 0.85rem;">
                            <?= date('d/m/Y H:i', strtotime($u['criado_em'])) ?>
                        </td>
                        <td style="text-align: right;">
                            <?php if ($u['id'] !== $_SESSION['user_id']): ?>
                                <a href="?eliminar=<?= $u['id'] ?>" onclick="return confirm('Tem a certeza?')"
                                    style="color: #ef4444; font-size: 0.9rem; font-weight: 600; text-decoration: none; margin-left: 10px;">
                                    🗑️ Eliminar
                                </a>
                            <?php else: ?>
                                <small style="color:var(--text-light); font-style:italic;">(Pópria Conta)</small>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>