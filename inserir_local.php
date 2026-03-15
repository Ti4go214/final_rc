<?php
/**
 * @file inserir_local.php
 * @brief API interna para gravação de novos locais e ficheiros.
 * @details Recebe dados via FormData, processa a geocodificação inversa e
 *          gere o upload de imagens para a pasta /uploads.
 * @author Tiago Guerra
 * @date 2026
 */

session_start();
require 'db.php';

/** @brief Cabeçalho de resposta JSON. */
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Acesso negado']);
    exit;
}

/** @var string $nome Nome do local enviado pelo formulário. */
$nome = $_POST['nome'] ?? '';
/** @var string $cat_nome Nome da categoria (texto) para resolução de ID. */
$cat_nome = $_POST['categoria'] ?? '';
/** @var string $emailDest
 * @brief Destinatário do e-mail.
 */
$emailDest = $_POST['email'] ?? '';
/** @var string $nomeLocal
 * @brief Nome do local a ser partilhado.
 */
$nomeLocal = $_POST['nome_local'] ?? '';

try {
    $pdo->beginTransaction();

    /** 
     * @brief Resolução de Categoria: Converte o nome da categoria recebido no formulário para o seu ID numérico.
     * @details Esta etapa é necessária para manter a integridade referencial com a tabela 'categorias'.
     */
    $st = $pdo->prepare("SELECT id FROM categorias WHERE nome = ?");
    $st->execute([$cat_nome]);
    $cat_id = $st->fetchColumn();

    /** 
     * @brief Inserção do Local: Grava os dados geográficos e metadados na tabela 'locais'.
     * @details Guarda o nome, categoria, autor, coordenadas e morada resolvida via API Nominatim.
     */
    $sql = "INSERT INTO locais (nome, categoria_id, criado_por, pais, cidade, morada, latitude, longitude)
VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $nome,
        $cat_id,
        $_SESSION['user_id'],
        $_POST['pais'],
        $_POST['cidade'],
        $_POST['morada'],
        $_POST['latitude'],
        $_POST['longitude']
    ]);

    $local_id = $pdo->lastInsertId();

    /** 
     * @brief Gestão de Imagem: Processa o upload opcional de fotografia.
     * @details Gera um nome único para o ficheiro baseado no ID e timestamp para evitar colisões. 
     *          Verifica a existência da pasta 'uploads' e associa o ficheiro ao local na tabela 'fotos'.
     */
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $novo_nome = "img_" . $local_id . "_" . time() . "." . $ext;

        if (!is_dir('uploads'))
            mkdir('uploads', 0777, true);

        if (move_uploaded_file($_FILES['foto']['tmp_name'], "uploads/" . $novo_nome)) {
            $stmtFoto = $pdo->prepare("INSERT INTO fotos (local_id, ficheiro) VALUES (?, ?)");
            $stmtFoto->execute([$local_id, $novo_nome]);
        }
    }

    $pdo->commit();
    echo json_encode(['status' => 'ok', 'id' => $local_id]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['status' => 'erro', 'mensagem' => $e->getMessage()]);
}
?>