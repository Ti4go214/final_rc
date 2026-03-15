<?php
/**
 * @file logout.php
 * @brief Termina a sessão de forma segura.
 * @details Este script limpa todas as variáveis de sessão e destrói a sessão ativa,
 * redirecionando o utilizador para a página de login.
 * @author Tiago Guerra
 * @date 2026
 */
session_start();
session_unset();
session_destroy();
header("Location: login.php");
exit;