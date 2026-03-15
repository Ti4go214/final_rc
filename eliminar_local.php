<?php
/**
 * @file eliminar_local.php
 * @brief Processa a eliminação de um local da base de dados.
 * @author Tiago Guerra
 */

session_start();
require 'db.php';

header('Content-Type: application/json');

/** 
 * @brief 1. Verificar se o utilizador está logado.
 * @details Caso não exista sessão, devolve erro JSON de autorização.
 */
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Não autorizado.']);
    exit;
}

$id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'];
$user_tipo = $_SESSION['user_tipo'];

/** 
 * @brief 2. Verificar permissões antes de eliminar regisro.
 * @details Primeiro, procura o local na BD para identificar o seu criador.
 */
$stmt = $pdo->prepare("SELECT criado_por FROM locais WHERE id = ?");
$stmt->execute([$id]);
$local = $stmt->fetch();

if (!$local) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Local não encontrado.']);
    exit;
}

/** 
 * @brief Regra de Permissões:
 * @details Administradores podem eliminar qualquer local.
 *          Utilizadores comuns apenas podem eliminar os locais que eles próprios criaram.
 */
if ($user_tipo !== 'admin' && $local['criado_por'] != $user_id) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Não tens permissão para eliminar este registo.']);
    exit;
}

/** 
 * @brief 3. Executar a eliminação física do local na base de dados.
 */
try {
    $stmtDel = $pdo->prepare("DELETE FROM locais WHERE id = ?");
    $stmtDel->execute([$id]);

    echo json_encode(['status' => 'ok', 'mensagem' => 'Registo eliminado com sucesso.']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro na BD: ' . $e->getMessage()]);
}