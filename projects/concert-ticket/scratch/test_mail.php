<?php
// scratch/test_mail.php
require_once __DIR__ . '/../vendor/autoload.php';

$mail_config_file = __DIR__ . '/../config/mail.php';
$config = require $mail_config_file;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);
try {
    $mail->SMTPDebug = 2; // Output debugging verbose
    $mail->isSMTP();
    $mail->Host       = $config['smtp_host'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $config['smtp_user'];
    $mail->Password   = $config['smtp_pass'];
    $mail->SMTPSecure = $config['smtp_secure'] === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = $config['smtp_port'];
    $mail->CharSet    = 'UTF-8';
    
    $mail->setFrom($config['from_email'], $config['from_name']);
    $mail->addAddress('budiarif396@gmail.com', 'Budi Test');
    
    $mail->isHTML(true);
    $mail->Subject = 'Uji Coba Koneksi SMTP AidFest';
    $mail->Body    = 'Jika Anda melihat email ini, konfigurasi SMTP Anda bekerja 100%!';
    
    echo "Mencoba mengirim email...\n";
    if ($mail->send()) {
        echo "\nSUKSES! Email terkirim ke budiarif396@gmail.com\n";
    } else {
        echo "\nGAGAL mengirim email.\n";
    }
} catch (Exception $e) {
    echo "\nTERJADI ERROR: " . $mail->ErrorInfo . "\n";
}
