<?php
/**
 * Production Studio Mailer & SMTP Client
 * Native PHP 8+ implementation with zero external dependencies (no PHPMailer/Composer needed).
 * Supports standard cPanel SSL (465), TLS (587), and native mail() fallback.
 */

declare(strict_types=1);

namespace Atelier\Core;

use Atelier\Models\Setting;
use Exception;

class Mailer {
    private array $config;

    public function __construct(?array $config = null) {
        if ($config !== null) {
            $this->config = $config;
            return;
        }

        // Fetch configured settings from database
        $dbSettings = [];
        try {
            $settingModel = new Setting();
            $dbSettings = $settingModel->getByGroup('smtp');
        } catch (Exception $e) {
            // Fallback gracefully if database is initializing
        }

        // Fallback to app config / .env
        $appConfig = [];
        $appConfigPath = __DIR__ . '/../config/app.php';
        if (file_exists($appConfigPath)) {
            $appConfig = require $appConfigPath;
        }
        $envMail = $appConfig['mail'] ?? [];

        $this->config = [
            'host'         => $dbSettings['smtp_host'] ?? $envMail['host'] ?? 'localhost',
            'port'         => (int)($dbSettings['smtp_port'] ?? $envMail['port'] ?? 465),
            'encryption'   => strtolower($dbSettings['smtp_encryption'] ?? $envMail['encryption'] ?? 'ssl'),
            'username'     => $dbSettings['smtp_username'] ?? $envMail['username'] ?? '',
            'password'     => $dbSettings['smtp_password'] ?? $envMail['password'] ?? '',
            'from_address' => $dbSettings['smtp_username'] ?? $envMail['from_address'] ?? 'concierge@interior.ritiverse.in',
            'from_name'    => 'RiTiVerse • Atelier Vérité',
            'admin_alert'  => $dbSettings['admin_alert_email'] ?? $envMail['admin_recipient'] ?? 'concierge@interior.ritiverse.in',
            'auto_reply'   => ($dbSettings['enable_client_autoreply'] ?? '1') === '1',
        ];
    }

    /**
     * Send client inquiry alert to studio concierge
     */
    public function sendInquiryNotification(array $inquiry): bool {
        $to = !empty($this->config['admin_alert']) ? $this->config['admin_alert'] : 'concierge@interior.ritiverse.in';
        $inquiryId = $inquiry['id'] ?? 'N/A';
        $clientName = htmlspecialchars($inquiry['name'] ?? 'Client');
        $clientEmail = htmlspecialchars($inquiry['email'] ?? '');
        $clientPhone = htmlspecialchars($inquiry['phone'] ?? '');
        $city = htmlspecialchars($inquiry['city'] ?? 'Unspecified');
        $propertyType = htmlspecialchars($inquiry['property_type'] ?? 'Residential');
        $areaSqft = htmlspecialchars((string)($inquiry['area_sqft'] ?? 'N/A'));
        $budget = htmlspecialchars($inquiry['budget_range'] ?? 'Flexible');
        $message = nl2br(htmlspecialchars($inquiry['message'] ?? ''));
        $date = date('F j, Y, g:i a');

        $subject = "Private Commission Inquiry #{$inquiryId} — {$clientName}";

        $htmlBody = <<<HTML
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>{$subject}</title>
  <style>
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #FAF9F5; color: #1C1B19; margin: 0; padding: 40px 20px; }
    .card { max-width: 620px; margin: 0 auto; background: #FFFFFF; border: 1px solid #E5E0D8; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
    .header { background: #1C1B19; color: #FAF9F5; padding: 32px; text-align: center; }
    .header h1 { margin: 0; font-family: Georgia, serif; font-size: 24px; letter-spacing: 0.2em; text-transform: uppercase; }
    .header p { margin: 6px 0 0 0; font-size: 11px; letter-spacing: 0.25em; text-transform: uppercase; color: #8C6D53; }
    .content { padding: 36px 32px; }
    .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; background: rgba(140, 109, 83, 0.1); color: #8C6D53; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.15em; margin-bottom: 20px; }
    .table { width: 100%; border-collapse: collapse; margin-top: 16px; font-size: 13px; }
    .table td { padding: 10px 0; border-bottom: 1px solid #F0ECE6; vertical-align: top; }
    .table td.label { width: 38%; color: #6E6A63; text-transform: uppercase; font-size: 10px; letter-spacing: 0.12em; font-weight: 600; }
    .table td.val { width: 62%; color: #1C1B19; font-weight: 500; }
    .message-box { margin-top: 24px; padding: 18px; background: #FAF9F5; border-radius: 12px; border: 1px solid #EAE5DC; font-size: 13px; line-height: 1.6; color: #2C2A26; }
    .footer { padding: 24px 32px; background: #F5F3EE; text-align: center; font-size: 11px; color: #6E6A63; border-top: 1px solid #EAE5DC; }
  </style>
</head>
<body>
  <div class="card">
    <div class="header">
      <h1>RiTiVerse</h1>
      <p>Atelier Vérité &bull; Architecture & Interiors</p>
    </div>
    <div class="content">
      <span class="badge">New Consultation Dossier #{$inquiryId}</span>
      <h2 style="font-family: Georgia, serif; font-weight: normal; margin: 0 0 16px 0; font-size: 20px;">Private Commission Request</h2>
      
      <table class="table">
        <tr><td class="label">Client Name</td><td class="val">{$clientName}</td></tr>
        <tr><td class="label">Email Address</td><td class="val"><a href="mailto:{$clientEmail}" style="color: #8C6D53; text-decoration: none;">{$clientEmail}</a></td></tr>
        <tr><td class="label">Contact Phone</td><td class="val"><a href="tel:{$clientPhone}" style="color: #1C1B19; text-decoration: none;">{$clientPhone}</a></td></tr>
        <tr><td class="label">Location / City</td><td class="val">{$city}</td></tr>
        <tr><td class="label">Property Typology</td><td class="val">{$propertyType}</td></tr>
        <tr><td class="label">Approximate Area</td><td class="val">{$areaSqft} sq.ft</td></tr>
        <tr><td class="label">Budget Range</td><td class="val">{$budget}</td></tr>
        <tr><td class="label">Transmitted At</td><td class="val">{$date}</td></tr>
      </table>

      <h3 style="font-family: Georgia, serif; margin: 24px 0 8px 0; font-size: 15px; font-weight: normal;">Project Scope & Vision:</h3>
      <div class="message-box">
        {$message}
      </div>
    </div>
    <div class="footer">
      Transmitted from the Atelier Vérité web portal. Review inquiries at <a href="/admin/leads.php" style="color: #8C6D53;">Studio Admin</a>.
    </div>
  </div>
</body>
</html>
HTML;

        $sent = $this->sendRaw($to, $subject, $htmlBody, $clientEmail);

        // Optional auto-reply to client
        if ($sent && $this->config['auto_reply'] && !empty($inquiry['email'])) {
            $this->sendClientAutoReply($inquiry);
        }

        return $sent;
    }

    /**
     * Send instant aesthetic acknowledgement to client
     */
    public function sendClientAutoReply(array $inquiry): bool {
        $to = $inquiry['email'];
        $name = htmlspecialchars($inquiry['name'] ?? 'Guest');
        $subject = "Receipt of Commission Inquiry — RiTiVerse • Atelier Vérité";

        $htmlBody = <<<HTML
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #FAF9F5; color: #1C1B19; margin: 0; padding: 40px 20px; }
    .card { max-width: 580px; margin: 0 auto; background: #FFFFFF; border: 1px solid #E5E0D8; border-radius: 16px; overflow: hidden; }
    .header { background: #1C1B19; color: #FAF9F5; padding: 36px 32px; text-align: center; }
    .header h1 { margin: 0; font-family: Georgia, serif; font-size: 26px; letter-spacing: 0.25em; text-transform: uppercase; }
    .header p { margin: 8px 0 0 0; font-size: 10px; letter-spacing: 0.28em; text-transform: uppercase; color: #8C6D53; }
    .content { padding: 36px 32px; line-height: 1.7; font-size: 14px; color: #3A3834; }
    .footer { padding: 24px 32px; background: #F5F3EE; text-align: center; font-size: 11px; color: #6E6A63; border-top: 1px solid #EAE5DC; }
  </style>
</head>
<body>
  <div class="card">
    <div class="header">
      <h1>RiTiVerse</h1>
      <p>Atelier Vérité &bull; Architecture & Interior Sanctuaries</p>
    </div>
    <div class="content">
      <p>Dear {$name},</p>
      <p>Thank you for introducing your spatial aspirations to RiTiVerse • Atelier Vérité. Our senior architectural concierge and design partners have received your commission inquiry.</p>
      <p>We review every project brief with careful consideration of light, material integrity, and architectural harmony. A principal designer will reach out within 24 to 48 business hours to arrange an introductory dialogue.</p>
      <p style="margin-top: 32px; font-family: Georgia, serif; font-style: italic; color: #1C1B19;">Warmest regards,<br>The Studio Directors &bull; Atelier Vérité</p>
    </div>
    <div class="footer">
      RiTiVerse &bull; Atelier Vérité Sanctuaries &bull; Confidential &bull; All Rights Reserved
    </div>
  </div>
</body>
</html>
HTML;

        return $this->sendRaw($to, $subject, $htmlBody);
    }

    /**
     * Send password reset email with secure token link
     */
    public function sendPasswordReset(string $recipient, string $resetUrl): bool {
        $subject = "Security Notice: Password Reset Request — Atelier CMS";

        $htmlBody = <<<HTML
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background-color: #FAF9F5; color: #1C1B19; margin: 0; padding: 40px 20px; }
    .card { max-width: 540px; margin: 0 auto; background: #FFFFFF; border: 1px solid #E5E0D8; border-radius: 14px; overflow: hidden; }
    .header { background: #1C1B19; color: #FAF9F5; padding: 28px; text-align: center; }
    .content { padding: 32px; font-size: 14px; line-height: 1.6; color: #2C2A26; }
    .btn { display: inline-block; padding: 12px 28px; background: #8C6D53; color: #FFFFFF !important; text-decoration: none; border-radius: 9999px; font-weight: 600; font-size: 12px; letter-spacing: 0.12em; text-transform: uppercase; margin: 24px 0; }
    .footer { padding: 20px 32px; background: #F5F3EE; text-align: center; font-size: 11px; color: #6E6A63; }
  </style>
</head>
<body>
  <div class="card">
    <div class="header">
      <h2 style="margin:0; font-family: Georgia, serif; letter-spacing: 0.2em; text-transform: uppercase;">RiTiVerse CMS</h2>
    </div>
    <div class="content">
      <p>Hello,</p>
      <p>A request was received to reset the administrative password for your Atelier Vérité Studio account.</p>
      <div style="text-align: center;">
        <a href="{$resetUrl}" class="btn">Reset Password</a>
      </div>
      <p style="font-size: 12px; color: #6E6A63;">This link is cryptographically signed and will expire in 1 hour. If you did not request this change, you may safely ignore this message.</p>
    </div>
    <div class="footer">
      RiTiVerse &bull; Atelier Vérité Security
    </div>
  </div>
</body>
</html>
HTML;

        return $this->sendRaw($recipient, $subject, $htmlBody);
    }

    /**
     * Send test diagnostic email
     */
    public function sendTestEmail(string $recipient): array {
        $subject = "SMTP Diagnostic Test — Atelier CMS (" . date('H:i:s') . ")";
        $htmlBody = "<p>This is a diagnostic verification email sent from your <strong>RiTiVerse Atelier Vérité CMS</strong>.</p><p>SMTP credentials and socket connectivity verified successfully on " . htmlspecialchars($this->config['host']) . ":" . $this->config['port'] . " (" . $this->config['encryption'] . ").</p>";

        try {
            $success = $this->sendViaSmtpSocket($recipient, $subject, $htmlBody);
            return [
                'success' => $success,
                'method'  => 'smtp_socket',
                'message' => $success ? "Diagnostic email transmitted successfully via SMTP socket." : "SMTP transmission failed."
            ];
        } catch (Exception $e) {
            // Try standard mail() fallback
            $fallbackSuccess = $this->sendViaPhpMail($recipient, $subject, $htmlBody);
            return [
                'success' => $fallbackSuccess,
                'method'  => $fallbackSuccess ? 'php_mail_fallback' : 'failed',
                'error'   => $e->getMessage(),
                'message' => $fallbackSuccess 
                    ? "Direct SMTP socket error: " . $e->getMessage() . " (Fallback to native mail() succeeded)." 
                    : "SMTP failure: " . $e->getMessage()
            ];
        }
    }

    /**
     * Main dispatcher: tries SMTP socket first if credentials configured, then falls back to mail()
     */
    public function sendRaw(string $to, string $subject, string $htmlBody, ?string $replyTo = null): bool {
        if (!empty($this->config['host']) && $this->config['host'] !== 'localhost' && !empty($this->config['username'])) {
            try {
                return $this->sendViaSmtpSocket($to, $subject, $htmlBody, $replyTo);
            } catch (Exception $e) {
                error_log("Mailer SMTP Socket Exception: " . $e->getMessage() . ". Falling back to mail().");
            }
        }
        return $this->sendViaPhpMail($to, $subject, $htmlBody, $replyTo);
    }

    /**
     * Direct pure PHP SMTP Socket Client (supports SSL 465, STARTTLS 587, AUTH LOGIN)
     */
    private function sendViaSmtpSocket(string $to, string $subject, string $htmlBody, ?string $replyTo = null): bool {
        $host = $this->config['host'];
        $port = $this->config['port'];
        $encryption = $this->config['encryption'];
        $user = $this->config['username'];
        $pass = $this->config['password'];
        $fromEmail = $this->config['from_address'] ?: $user;
        $fromName = $this->config['from_name'];

        $socketHost = ($encryption === 'ssl') ? "ssl://{$host}" : $host;
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);

        $socket = @stream_socket_client("{$socketHost}:{$port}", $errno, $errstr, 15, STREAM_CLIENT_CONNECT, $context);
        if (!$socket) {
            throw new Exception("Could not connect to SMTP host {$host}:{$port} ({$errstr})");
        }

        $getResponse = function() use ($socket): string {
            $res = '';
            while ($line = fgets($socket, 515)) {
                $res .= $line;
                if (substr($line, 3, 1) === ' ') break;
            }
            return $res;
        };

        $sendCommand = function(string $cmd) use ($socket, $getResponse): string {
            fputs($socket, $cmd . "\r\n");
            $response = $getResponse();
            $code = (int)substr($response, 0, 3);
            if ($code >= 400) {
                throw new Exception("SMTP Error on '{$cmd}': {$response}");
            }
            return $response;
        };

        $init = $getResponse(); // Read 220 banner

        $sendCommand("EHLO " . (gethostname() ?: 'localhost'));

        if ($encryption === 'tls') {
            $sendCommand("STARTTLS");
            stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
            $sendCommand("EHLO " . (gethostname() ?: 'localhost'));
        }

        if (!empty($user) && !empty($pass)) {
            $sendCommand("AUTH LOGIN");
            $sendCommand(base64_encode($user));
            $sendCommand(base64_encode($pass));
        }

        $sendCommand("MAIL FROM: <{$fromEmail}>");
        $sendCommand("RCPT TO: <{$to}>");
        $sendCommand("DATA");

        // Prepare email headers
        $boundary = "b_" . md5(uniqid((string)time()));
        $headers = [
            "Date: " . date('r'),
            "From: =?UTF-8?B?" . base64_encode($fromName) . "?= <{$fromEmail}>",
            "To: <{$to}>",
            "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=",
            "MIME-Version: 1.0",
            "Content-Type: multipart/alternative; boundary=\"{$boundary}\"",
            "X-Mailer: Atelier-SmtpClient/1.0"
        ];
        if (!empty($replyTo)) {
            $headers[] = "Reply-To: <{$replyTo}>";
        }

        $plainText = strip_tags(str_replace(['<br>', '<br/>', '<p>'], ["\n", "\n", "\n\n"], $htmlBody));

        $emailData = implode("\r\n", $headers) . "\r\n\r\n"
            . "--{$boundary}\r\n"
            . "Content-Type: text/plain; charset=UTF-8\r\n"
            . "Content-Transfer-Encoding: base64\r\n\r\n"
            . chunk_split(base64_encode($plainText)) . "\r\n"
            . "--{$boundary}\r\n"
            . "Content-Type: text/html; charset=UTF-8\r\n"
            . "Content-Transfer-Encoding: base64\r\n\r\n"
            . chunk_split(base64_encode($htmlBody)) . "\r\n"
            . "--{$boundary}--\r\n.";

        $sendCommand($emailData);
        $sendCommand("QUIT");
        fclose($socket);

        return true;
    }

    /**
     * Fallback standard cPanel mail()
     */
    private function sendViaPhpMail(string $to, string $subject, string $htmlBody, ?string $replyTo = null): bool {
        $fromEmail = $this->config['from_address'] ?: 'no-reply@interior.ritiverse.in';
        $fromName = $this->config['from_name'];

        $boundary = md5(uniqid((string)time()));
        $headers = [
            "From: {$fromName} <{$fromEmail}>",
            "MIME-Version: 1.0",
            "Content-Type: multipart/alternative; boundary=\"{$boundary}\"",
            "X-Mailer: PHP/" . phpversion()
        ];
        if (!empty($replyTo)) {
            $headers[] = "Reply-To: <{$replyTo}>";
        }

        $plainText = strip_tags($htmlBody);
        $multipartMessage = "--{$boundary}\r\n"
                          . "Content-Type: text/plain; charset=UTF-8\r\n"
                          . "Content-Transfer-Encoding: 7bit\r\n\r\n"
                          . $plainText . "\r\n\r\n"
                          . "--{$boundary}\r\n"
                          . "Content-Type: text/html; charset=UTF-8\r\n"
                          . "Content-Transfer-Encoding: 7bit\r\n\r\n"
                          . $htmlBody . "\r\n\r\n"
                          . "--{$boundary}--";

        return @mail($to, $subject, $multipartMessage, implode("\r\n", $headers));
    }
}
