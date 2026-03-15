<?php
/**
 * @file api_locais.php
 * @brief Devolve a lista de locais com respetivas categorias e foto de capa em formato JSON.
 *
 * Este script consulta a base de dados para obter todos os locais que possuem
 * coordenadas geográficas válidas (latitude e longitude), juntamente com
 * informação da sua categoria e uma foto de capa associada.
 *
 * @author Tiago Guerra
 * @date 2026
 */

header('Content-Type: application/json; charset=utf-8');

try {
    require __DIR__ . '/db.php';

    /**
     * @var PDOStatement $stmt
     * @brief Statement PDO responsável pela consulta à base de dados.
     *
     * A query seleciona:
     *  - ID e Nome do local
     *  - Latitude e longitude
     *  - Nome da categoria e estilos (cor, letras)
     *  - Criado_por para permissões de visualização
     *  - Morada, Cidade e País
     *  - Uma miniatura (foto)
     */
    $stmt = $pdo->query("
        SELECT 
            l.id,
            l.nome,
            l.latitude,
            l.longitude,
            l.morada,
            l.cidade,
            l.pais,
            l.criado_por,
            c.nome AS categoria,
            c.cor,
            c.letras,
            (SELECT ficheiro FROM fotos f WHERE f.local_id = l.id LIMIT 1) AS foto,
            COALESCE(AVG(a.classificacao), 0) AS media_classificacao,
            COUNT(a.id) AS total_votos
        FROM locais l
        JOIN categorias c ON l.categoria_id = c.id
        LEFT JOIN avaliacoes a ON l.id = a.local_id
        WHERE l.latitude IS NOT NULL 
          AND l.longitude IS NOT NULL
        GROUP BY l.id
    ");

    /**
     * @var array $locais
     * @brief Array associativo com todos os locais obtidos da base de dados.
     */
    $locais = $stmt->fetchAll(PDO::FETCH_ASSOC);

    /**
     * Converte o array de locais para JSON e envia para o cliente.
     */
    echo json_encode($locais, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    /** Retorna erro JSON limpo em caso de falha de ligação (ex: falta de driver DB) */
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro interno na base de dados: ' . $e->getMessage()]);
}
