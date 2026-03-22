<?php
/**
 * Mail Configuration for KNHS CSS NCII TESDA Certificate Submission
 * 
 * Kauswagan National High School
 * Kauswagan, Cagayan de Oro
 * 
 * Program by: Keith Dandan - ICT 12 Magsaysay
 */

// Gmail Configuration
define('GMAIL_ADDRESS', 'keithcharlespacatangdandan@gmail.com');

// Gmail App Password (generated from https://myaccount.google.com/apppasswords)
define('GMAIL_APP_PASSWORD', 'pydb zqbc wodo ofck');

// SMTP Settings
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', GMAIL_ADDRESS);
define('SMTP_PASSWORD', GMAIL_APP_PASSWORD);
define('SMTP_ENCRYPTION', 'tls');

// From Address
define('FROM_EMAIL', 'noreply@knhs-css-ncii.local');
define('FROM_NAME', 'KNHS CSS NCII - Kauswagan NHS');

// Upload Settings
define('UPLOAD_DIR', __DIR__ . '/../assets/uploads/');
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_FILE_TYPES', ['image/jpeg', 'image/jpg', 'image/png', 'image/gif']);

// Email Subject Template
define('EMAIL_SUBJECT', 'KNHS CSS NCII Certificate Submission - {name}');

// Debug Mode (set to true to see error messages)
define('DEBUG_MODE', false);
?>
