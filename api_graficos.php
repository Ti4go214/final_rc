<?php
/**
 * @file api_graficos.php
 * @brief Endpoint JSON para fornecer os dados agregados para os gráficos (Chart.js).
 * @details Este ficheiro processa queries GROUP BY e devolve Locais por Categoria
 * e Locais adicionados por Utilizador. É protegido para apenas administradores.
 * @author Tiago Guerra
 * @date 2026
 */

session_start();
require 'db.php';

header('Content-Type: application/json');

/** 
 * @brief Segurança: Apenas administradores autorizados.
 * @details Garante que o utilizador possui privilégios de 'admin' antes de aceder aos dados analíticos sensíveis.
 */
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'admin') {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Acesso não autorizado.']);
    exit;
}

try {
    /**
     * @brief 1. Locais por Categoria
     * @details Faz um RIGHT JOIN para garantir que categorias vazias também aparecem.
     * A query SQL seleciona o nome da categoria e a contagem de locais associados.
     * Os resultados são agrupados por ID da categoria e ordenados pelo total de locais em ordem decrescente.
     * @var string $sqlCat Query SQL para obter locais por categoria.
     * @var PDOStatement $stmtCat Objeto de declaração PDO para a query de categorias.
     * @var array $dadosCategoria Array associativo com os resultados da query de categorias.
     */
    $sqlCat = "
SELECT c.nome as categoria, COUNT(l.id) as total
FROM locais l
RIGHT JOIN categorias c ON l.categoria_id = c.id
GROUP BY c.id
ORDER BY total DESC
";
    $stmtCat = $pdo->query($sqlCat);
    $dadosCategoria = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

    /**
     * @brief 2. Locais por Utilizador
     * @details A query SQL seleciona o nome do utilizador e a contagem de locais criados por ele.
     * Os resultados são agrupados por ID do utilizador e ordenados pelo total de locais em ordem decrescente.
     * @var string $sqlUser Query SQL para obter locais por utilizador.
     * @var PDOStatement $stmtUser Objeto de declaração PDO para a query de utilizadores.
     * @var array $dadosUtilizador Array associativo com os resultados da query de utilizadores.
     */
    $sqlUser = "
SELECT u.nome as utilizador, COUNT(l.id) as total
FROM locais l
JOIN utilizadores u ON l.criado_por = u.id
GROUP BY u.id
ORDER BY total DESC
";
    $stmtUser = $pdo->query($sqlUser);
    $dadosUtilizador = $stmtUser->fetchAll(PDO::FETCH_ASSOC);

    /** 
     * @brief 3. Totais globais para estatísticas rápidas.
     * @details Realiza contagens simples na base de dados para apresentar no topo do dashboard.
     */
    $sqlTotalLocais = "SELECT COUNT(*) FROM locais";
    /** @var int $totalLocais Contagem total de locais. */
    $totalLocais = $pdo->query($sqlTotalLocais)->fetchColumn();

    $sqlTotalUsers = "SELECT COUNT(*) FROM utilizadores";
    /** @var int $totalUsers Contagem total de utilizadores registados. */
    $totalUsers = $pdo->query($sqlTotalUsers)->fetchColumn();

    /** 
     * @brief Envio da resposta final em formato JSON.
     * @details Agrupa as diferentes estatísticas para consumo pelo Chart.js.
     */
    echo json_encode([
        'status' => 'ok',
        'categorias' => $dadosCategoria,
        'utilizadores' => $dadosUtilizador,
        'estatisticas' => [
            'total_locais' => $totalLocais,
            'total_utilizadores' => $totalUsers
        ]
    ]);

} catch (PDOException $e) {
    /** @throw PDOException Erro de base de dados capturado. */
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro na BD: ' . $e->getMessage()]);
}
?>