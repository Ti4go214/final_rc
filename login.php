<?php
/**
 * @file login.php
 * @brief Interface de Autenticação Ultra-Moderna.
 * @details Implementa um design inspirado em 2026 com Glassmorphism.
 * Mapeia as colunas 'nome' e 'senha' da base de dados.
 * @author Tiago Guerra
 * @date 2026
 */

require 'db.php';
session_start();

/** @var string|null $erro 
 * @brief Mensagem de erro para exibição no formulário em caso de falha na autenticação.
 */
$erro = null;

/**
 * @brief Lógica de submissão segura.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /** @var string $u 
     * @brief Nome de utilizador introduzido no campo 'utilizador'.
     */
    $u = $_POST['utilizador'] ?? '';
    /** @var string $p 
     * @brief Senha (plain text) introduzida no campo 'password'.
     */
    $p = $_POST['password'] ?? '';

    if (!empty($u) && !empty($p)) {
        /** @var PDOStatement $stmt Consulta preparada. */
        $stmt = $pdo->prepare("SELECT * FROM utilizadores WHERE nome = ?");
        $stmt->execute([$u]);
        $user = $stmt->fetch();

        /** @note Verificação final usando a coluna 'senha' da BD. */
        if ($user && $p === $user['senha']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_tipo'] = $user['tipo'];
            $_SESSION['user_nome'] = $user['nome'];

            header("Location: index.php");
            exit;
        } else {
            $erro = "Credenciais inválidas. Tente novamente.";
        }
    } else {
        $erro = "Por favor, preencha todos os campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GeoDados | Login Profissional</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --glass: rgba(255, 255, 255, 0.1);
            --border: rgba(255, 255, 255, 0.2);
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            color: white;
        }

        /* Orbes decorativos no fundo */
        .orb {
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 70%);
            z-index: -1;
            filter: blur(60px);
        }

        .login-card {
            background: var(--glass);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--border);
            padding: 50px 40px;
            border-radius: 30px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 380px;
            text-align: center;
            animation: slideUp 0.8s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h2 {
            font-weight: 600;
            font-size: 2.2rem;
            margin-bottom: 8px;
            letter-spacing: -1px;
        }

        .subtitle {
            margin-bottom: 35px;
            opacity: 0.5;
            font-size: 0.9rem;
        }

        .input-group {
            text-align: left;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.75rem;
            font-weight: 300;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        input {
            width: 100%;
            padding: 15px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: rgba(0, 0, 0, 0.2);
            color: white;
            font-size: 1rem;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(0, 0, 0, 0.3);
            box-shadow: 0 0 15px rgba(99, 102, 241, 0.3);
        }

        button {
            width: 100%;
            padding: 16px;
            border-radius: 12px;
            border: none;
            background: var(--primary);
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            margin-top: 10px;
        }

        button:hover {
            background: #4f46e5;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
        }

        .error-msg {
            background: rgba(244, 63, 94, 0.15);
            border: 1px solid #f43f5e;
            color: #fb7185;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-size: 0.85rem;

            .link-rodape {
                display: block;
                margin-top: 25px;
                color: #94a3b8;
                font-size: 0.85rem;
                text-decoration: none;
                transition: color 0.2s;
            }

            .link-rodape:hover {
                color: var(--primary);
            }
    </style>
</head>

<body>

    <div class="orb" style="top: -150px; left: -150px;"></div>
    <div class="orb" style="bottom: -150px; right: -150px;"></div>

    <div class="login-card">
        <h2>GeoDados</h2>
        <p class="subtitle">Bem-vindo ao futuro do mapeamento</p>

        <?php if ($erro): ?>
            <div class="error-msg"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <label>Utilizador</label>
                <input type="text" name="utilizador" placeholder="O seu nome" required>
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>

            <button type="submit">Aceder à Aplicação</button>
        </form>

        <a href="registar.php" class="link-rodape">Ainda não tem conta? <b>Criar agora</b></a>
    </div>

</body>

</html>