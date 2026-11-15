<?php
/**
 * Consultation Inquiry Notification Mailer
 * Sends responsive HTML + Plain text emails compatible with standard cPanel PHP mail().
 */

declare(strict_types=1);

namespace Atelier\Core;

class Mailer {
    private array $config;

    public function __construct(?array $config = null) {
        if ($config === null) {
            $appConfig = require __DIR__ . '/../config/app.php';
            $this->config = $appConfig['mail'] ?? [];
        } else {
            $this->config = $config;
        }
    }

    /**
     * Send client inquiry notification email to studio concierge
     */
    public function sendInquiryNotification(array $inquiry): bool {
        $to = $this->config['admin_recipient'] ?? 'concierge@ateliervérité.com';
        $fromEmail = $this->config['from_address'] ?? 'no-reply@ateliervérité.com';
        $fromName = $this->config['from_name'] ?? 'Atelier Vérité Studio';

        $inquiryId = $inquiry['id'] ?? 'N/A';
        $clientName = htmlspecialchars($inquiry['name'] ?? 'Client');
        $clientEmail = htmlspecialchars($inquiry['email'] ?? '');
        $clientPhone = htmlspecialchars($inquiry['phone'] ?? '');
        $city = htmlspecialchars($inquiry['city'] ?? 'Unspecified');
        $propertyType = htmlspecialchars($inquiry['property_type'] ?? 'Residential');
        $areaSqft = htmlspecialchars($inquiry['area_sqft'] ?? 'N/A');
        $budget = htmlspecialchars($inquiry['budget_range'] ?? 'Flexible');
        $message = nl2br(htmlspecialchars($inquiry['message'] ?? ''));
        $date = date('F j, Y, g:i a');

        $subject = "Private Commission Inquiry #{$inquiryId} — {$clientName}";

        // Luxury HTML Template
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
      <h1>AURION</h1>
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
        <tr><td class="label">Approximate Area</td><td class="val">{$areaSqft}</td></tr>
        <tr><td class="label">Budget Range</td><td class="val">{$budget}</td></tr>
        <tr><td class="label">Transmitted At</td><td class="val">{$date}</td></tr>
      </table>

      <h3 style="font-family: Georgia, serif; margin: 24px 0 8px 0; font-size: 15px; font-weight: normal;">Project Scope & Vision:</h3>
      <div class="message-box">
        {$message}
      </div>
    </div>
    <div class="footer">
      This inquiry was transmitted from the Atelier Vérité web portal.<br>
      Access the studio management CMS at <a href="/api/admin/" style="color: #8C6D53;">/api/admin/</a>.
    </div>
  </div>
</body>
</html>
HTML;

        // Plain text fallback
        $textBody = "NEW CONSULTATION INQUIRY #{$inquiryId}\n"
                  . "Client: {$clientName}\n"
                  . "Email: {$clientEmail}\n"
                  . "Phone: {$clientPhone}\n"
                  . "City: {$city}\n"
                  . "Property: {$propertyType}\n"
                  . "Area: {$areaSqft}\n"
                  . "Budget: {$budget}\n"
                  . "Date: {$date}\n\n"
                  . "Message:\n{$inquiry['message']}\n";

        // Construct MIME Boundary Headers
        $boundary = md5((string)time());
        $headers = [
            "From: {$fromName} <{$fromEmail}>",
            "Reply-To: {$clientName} <{$clientEmail}>",
            "MIME-Version: 1.0",
            "Content-Type: multipart/alternative; boundary=\"{$boundary}\"",
            "X-Mailer: PHP/" . phpversion()
        ];

        $multipartMessage = "--{$boundary}\r\n"
                          . "Content-Type: text/plain; charset=UTF-8\r\n"
                          . "Content-Transfer-Encoding: 7bit\r\n\r\n"
                          . $textBody . "\r\n\r\n"
                          . "--{$boundary}\r\n"
                          . "Content-Type: text/html; charset=UTF-8\r\n"
                          . "Content-Transfer-Encoding: 7bit\r\n\r\n"
                          . $htmlBody . "\r\n\r\n"
                          . "--{$boundary}--";

        // Suppress warnings in environments without a configured sendmail binary
        return @mail($to, $subject, $multipartMessage, implode("\r\n", $headers));
    }
}
