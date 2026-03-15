<?php
/**
 * @file avaliar_local.php
 * @brief Endpoint para submeter ou atualizar a avaliação de um local (1 a 5 estrelas).
 * @author Tiago Guerra
 * @date 2026
 */

session_start();
require 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Tem de iniciar sessão para avaliar.']);
    exit;
}

$local_id = $_POST['local_id'] ?? null;
$classificacao = $_POST['classificacao'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$local_id || !$classificacao || $classificacao < 1 || $classificacao > 5) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Dados inválidos. A classificação deve ser de 1 a 5.']);
    exit;
}

try {
    /**
     * @brief Verifica se o utilizador já possui uma avaliação para este local.
     * @details Executa uma consulta para evitar duplicados, permitindo apenas uma avaliação por par (local, utilizador).
     */
    $stmtCheck = $pdo->prepare("SELECT id FROM avaliacoes WHERE local_id = ? AND utilizador_id = ?");
    $stmtCheck->execute([$local_id, $user_id]);

    if ($stmtCheck->rowCount() > 0) {
        /**
         * @brief Fluxo de Atualização: O utilizador já avaliou, então atualiza a nota.
         * @details Atualiza a classificação existente na tabela 'avaliacoes'.
         */
        $stmtUpdate = $pdo->prepare("UPDATE avaliacoes SET classificacao = ? WHERE local_id = ? AND utilizador_id = ?");
        $stmtUpdate->execute([$classificacao, $local_id, $user_id]);
        $mensagem = "Avaliação atualizada com sucesso!";
    } else {
        /**
         * @brief Fluxo de Inserção: Primeira avaliação deste utilizador para este local.
         * @details Insere uma nova avaliação na tabela 'avaliacoes'.
         */
        $stmtInsert = $pdo->prepare("INSERT INTO avaliacoes (local_id, utilizador_id, classificacao) VALUES (?, ?, ?)");
        $stmtInsert->execute([$local_id, $user_id, $classificacao]);
        $mensagem = "Avaliação registada com sucesso!";
    }

    echo json_encode(['status' => 'ok', 'mensagem' => $mensagem]);

} catch (PDOException $e) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro na gravação: ' . $e->getMessage()]);
}
?>