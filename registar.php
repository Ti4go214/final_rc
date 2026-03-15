<?php
/**
 * @file registar.php
 * @brief Interface de Registo de Novos Utilizadores.
 * @details Permite a novos utilizadores criarem uma conta no sistema.
 * @author Tiago Guerra
 * @date 2026
 */

require 'db.php';
session_start();

/** @var string|null $erro 
 * @brief Mensagem de erro para feedback visual em caso de falha no registo. 
 */
$erro = null;
/** @var string|null $sucesso 
 * @brief Mensagem de sucesso para feedback visual após criação de conta. 
 */
$sucesso = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['utilizador'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['password'] ?? '';
    $confirma_senha = $_POST['confirma_password'] ?? '';

    /** 
     * @brief Validações básicas de formulário.
     * @details Verifica campos vazios, formato de email e correspondência de senhas.
     */
    if (empty($nome) || empty($email) || empty($senha)) {
        $erro = "Por favor, preencha todos os campos.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "Formato de e-mail inválido.";
    } elseif ($senha !== $confirma_senha) {
        $erro = "As senhas não coincidem.";
    } else {
        /** 
         * @brief Verificação de Duplicados.
         * @details Garante que o nome de utilizador e o email são únicos na base de dados.
         */
        $stmtCheck = $pdo->prepare("SELECT id FROM utilizadores WHERE nome = ? OR email = ?");
        $stmtCheck->execute([$nome, $email]);

        if ($stmtCheck->rowCount() > 0) {
            $erro = "Este nome de utilizador ou e-mail já estão registados.";
        } else {
            /** 
             * @brief Persistência: Insere o novo utilizador com perfil 'normal'.
             */
            /** 
             * @note Segurança em Produção:
             * @details Em ambientes reais, a senha deve ser SEMPRE cifrada utilizando \c password_hash().
             */
            $stmtInsert = $pdo->prepare("INSERT INTO utilizadores (nome, senha, email, tipo) VALUES (?, ?, ?, 'normal')");

            try {
                $stmtInsert->execute([$nome, $senha, $email]);
                $sucesso = "Conta criada com sucesso! Já pode fazer login.";
            } catch (PDOException $e) {
                $erro = "Erro interno ao criar conta: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GeoDados | Criar Conta</title>
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
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: auto;
            /* Permite scroll se necessário num ecrã pequeno */
            color: white;
            padding: 20px 0;
        }

        /* Orbes decorativos no fundo */
        .orb {
            position: fixed;
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
            position: relative;
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
            margin-bottom: 25px;
            opacity: 0.5;
            font-size: 0.9rem;
        }

        .input-group {
            text-align: left;
            margin-bottom: 20px;
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
            padding: 12px 15px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: rgba(0, 0, 0, 0.2);
            color: white;
            font-size: 0.95rem;
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

        .error-msg,
        .success-msg {
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-size: 0.85rem;
        }

        .error-msg {
            background: rgba(244, 63, 94, 0.15);
            border: 1px solid #f43f5e;
            color: #fb7185;
        }

        .success-msg {
            background: rgba(34, 197, 94, 0.15);
            border: 1px solid #22c55e;
            color: #4ade80;
        }

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
        <p class="subtitle">Junte-se ao sistema de mapeamento</p>

        <?php if ($erro): ?>
            <div class="error-msg">
                <?= htmlspecialchars($erro) ?>
            </div>
        <?php endif; ?>

        <?php if ($sucesso): ?>
            <div class="success-msg">
                <?= htmlspecialchars($sucesso) ?><br><br>
                <a href="login.php" style="color:white; font-weight:bold;">Ir para o Login</a>
            </div>
        <?php else: ?>

            <form method="POST">
                <div class="input-group">
                    <label>Utilizador</label>
                    <input type="text" name="utilizador" placeholder="Como quer ser chamado?" required
                        value="<?= htmlspecialchars($_POST['utilizador'] ?? '') ?>">
                </div>

                <div class="input-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="O seu e-mail" required
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>

                <div class="input-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="••••••••" required minlength="4">
                </div>

                <div class="input-group">
                    <label>Confirmar Password</label>
                    <input type="password" name="confirma_password" placeholder="••••••••" required minlength="4">
                </div>

                <button type="submit">Criar Conta</button>
            </form>

        <?php endif; ?>

        <a href="login.php" class="link-rodape">← Já tem conta? Voltar ao Login</a>
    </div>

</body>

</html>