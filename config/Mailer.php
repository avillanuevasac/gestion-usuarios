<?php
require_once __DIR__ . '/../vendor/phpmailer/Exception.php';
require_once __DIR__ . '/../vendor/phpmailer/PHPMailer.php';
require_once __DIR__ . '/../vendor/phpmailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer {
    private static function getConfig(): array {
        return require __DIR__ . '/mail.php';
    }

    public static function send(string $toEmail, string $toName, string $subject, string $body): bool {
        $cfg = self::getConfig();

        if ($cfg['driver'] === 'mail') {
            $headers = "From: {$cfg['from_name']} <{$cfg['from_email']}>\r\nContent-Type: text/html; charset=UTF-8";
            return mail($toEmail, $subject, $body, $headers);
        }

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = $cfg['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $cfg['username'];
            $mail->Password   = $cfg['password'];
            $mail->SMTPSecure = $cfg['encryption'];
            $mail->Port       = $cfg['port'];
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom($cfg['from_email'], $cfg['from_name']);
            $mail->addAddress($toEmail, $toName);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>'], "\n", $body));

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Mailer error: " . $mail->ErrorInfo);
            return false;
        }
    }

    public static function buildResetEmail(string $link): string {
        return <<<HTML
        <div style="font-family:Arial,sans-serif;max-width:520px;margin:0 auto;border:1px solid #e0e0e0;border-radius:8px;overflow:hidden">
            <div style="background:#4361ee;padding:24px;text-align:center">
                <h2 style="color:#fff;margin:0">🔐 Restablecer contraseña</h2>
            </div>
            <div style="padding:32px">
                <p style="color:#333">Hemos recibido una solicitud para restablecer tu contraseña.</p>
                <p style="color:#333">Haz clic en el botón para crear una nueva contraseña. El enlace expira en <strong>1 hora</strong>.</p>
                <div style="text-align:center;margin:32px 0">
                    <a href="$link" style="background:#4361ee;color:#fff;padding:14px 32px;border-radius:8px;text-decoration:none;font-weight:bold;font-size:15px">
                        Restablecer contraseña
                    </a>
                </div>
                <p style="color:#888;font-size:13px">Si no solicitaste esto, ignora este correo. Tu contraseña no cambiará.</p>
                <hr style="border:none;border-top:1px solid #eee;margin:24px 0">
                <p style="color:#aaa;font-size:12px;text-align:center">
                    Si el botón no funciona, copia este enlace:<br>
                    <a href="$link" style="color:#4361ee;word-break:break-all">$link</a>
                </p>
            </div>
        </div>
        HTML;
    }
}
