<?php
/**
 * @file enviar_email.php
 * @brief Processa o envio de informação detalhada de um local por email.
 * @details Recebe o endereço de destino e o nome do local via POST, 
 *          monta a mensagem e utiliza a função nativa mail() do PHP.
 * @author Tiago Guerra
 * @date 2026
 */

session_start();

/** @brief Define o cabeçalho JSON para resposta AJAX. */
header('Content-Type: application/json');

/** 1. Verificação Simples de Segurança (Opcional mas recomendado) */
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Acesso não autorizado.']);
    exit;
}

/** @var string $emailDest Destinatário do email. */
$emailDest = $_POST['email'] ?? '';
/** @var string $nomeLocal Nome do local a partilhar. */
$nomeLocal = $_POST['nome_local'] ?? '';

/** 2. Validação Básica */
if (!filter_var($emailDest, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Formato de e-mail inválido.']);
    exit;
}

if (empty($nomeLocal)) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Nome do local em falta.']);
    exit;
}

/** 3. Integração com PHPMailer (Manual) */
require 'lib/PHPMailer/Exception.php';
require 'lib/PHPMailer/PHPMailer.php';
require 'lib/PHPMailer/SMTP.php';
require 'config_email.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    /** Configurações do Servidor */
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USER;
    $mail->Password = SMTP_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = SMTP_PORT;
    $mail->CharSet = 'UTF-8';

    /** Desativar verificação de SSL (necessário para WAMP/Localhost em muitos casos) */
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    /** Destinatários */
    $mail->setFrom(SMTP_FROM, SMTP_NAME);
    $mail->addAddress($emailDest);

    /** Conteúdo do Email */
    $mail->isHTML(true);
    $mail->Subject = "Partilha de Local - GeoDados";

    $corpo = "<h2>GeoDados - Partilha</h2>";
    $corpo .= "<p>Olá! Alguém partilhou um local interessante contigo: <b>" . htmlspecialchars($nomeLocal) . "</b></p>";
    $corpo .= "<p>Podes ver os detalhes acedendo à aplicação GeoDados no teu computador.</p>";
    $corpo .= "<br><hr><small>Enviado automaticamente pelo Sistema GeoDados.</small>";

    $mail->Body = $corpo;

    $mail->send();
    echo json_encode(['status' => 'ok', 'mensagem' => 'E-mail enviado com sucesso!']);
} catch (Exception $e) {
    echo json_encode(['status' => 'erro', 'mensagem' => "Erro no envio: {$mail->ErrorInfo}"]);
}
?>