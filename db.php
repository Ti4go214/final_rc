<?php
/**
 * @file db.php
 * @brief Gere a conexão à base de dados MySQL via PDO.
 * @details Este script configura os parâmetros de acesso ao servidor local,
 * define o charset para suporte total a caracteres especiais e ativa o modo 
 * de erros por exceção para facilitar o debug.
 * @author Tiago Guerra
 * @date 2026
 */

/** @var string $host Endereço do servidor MySQL. */
$host = "localhost";
/** @var string $db Nome da base de dados definida no enunciado. */
$db   = "geo_dados";
/** @var string $user Utilizador do MySQL (padrão XAMPP). */
$user = "root";
/** @var string $pass Senha do MySQL (padrão vazia). */
$pass = "123";
/** @var string $charset Codificação de caracteres. */
$charset = "utf8mb4";

/** @var string $dsn Data Source Name para o PDO. */
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

/** @var array $options Configurações de comportamento do PDO. */
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    /** @var PDO $pdo Instância global de ligação à base de dados. */
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    /** @throw PDOException Se a ligação falhar, interrompe a execução. */
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>