<?php
/**
 * KNHS CSS NCII - TESDA Certificate Submission Handler
 * 
 * Kauswagan National High School
 * Kauswagan, Cagayan de Oro
 * 
 * Program by: Keith Dandan - ICT 12 Magsaysay
 */

// Load configuration
require_once __DIR__ . '/config/mail-config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Response array
$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get form data
    $fullName = sanitize($_POST['fullName'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $course = sanitize($_POST['course'] ?? '');
    $certificateDate = sanitize($_POST['certificateDate'] ?? '');
    $message = sanitize($_POST['message'] ?? '');
    
    // Validation
    $errors = array();
    
    if (empty($fullName)) {
        $errors[] = 'Full name is required';
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email address is required';
    }
    
    if (empty($course)) {
        $errors[] = 'Course selection is required';
    }
    
    if (!isset($_FILES['certificate']) || $_FILES['certificate']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Certificate image is required';
    }
    
    if (!empty($errors)) {
        $response['success'] = false;
        $response['message'] = implode(', ', $errors);
        echo json_encode($response);
        exit;
    }
    
    // Handle file upload
    $file = $_FILES['certificate'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileType = $file['type'];
    $fileError = $file['error'];
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!in_array($fileType, $allowedTypes)) {
        $response['success'] = false;
        $response['message'] = 'Invalid file type. Only JPG, PNG, and GIF are allowed.';
        echo json_encode($response);
        exit;
    }
    
    // Validate file size (10MB max)
    $maxSize = 10 * 1024 * 1024;
    if ($fileSize > $maxSize) {
        $response['success'] = false;
        $response['message'] = 'File size must be less than 10MB';
        echo json_encode($response);
        exit;
    }
    
    // Check for upload errors
    if ($fileError !== UPLOAD_ERR_OK) {
        $response['success'] = false;
        $response['message'] = 'File upload error. Please try again.';
        echo json_encode($response);
        exit;
    }
    
    // Generate unique filename
    $extension = pathinfo($fileName, PATHINFO_EXTENSION);
    $cleanName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $fullName);
    $cleanName = preg_replace('/_+/', '_', $cleanName);
    $newFileName = date('YmdHis') . '_' . $cleanName . '.' . strtolower($extension);
    
    // Create upload directory
    $uploadDir = __DIR__ . '/assets/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $uploadPath = $uploadDir . $newFileName;
    
    // Move uploaded file
    if (!move_uploaded_file($fileTmpName, $uploadPath)) {
        if (!copy($fileTmpName, $uploadPath)) {
            $response['success'] = false;
            $response['message'] = 'Failed to save uploaded file.';
            echo json_encode($response);
            exit;
        }
    }
    
    // Prepare email
    $subject = "KNHS CSS NCII Certificate Submission - $fullName";
    $emailBody = buildEmailBody($fullName, $email, $course, $certificateDate, $message);
    
    // Try to send email with attachment
    $emailSent = sendEmailWithAttachment($subject, $emailBody, $uploadPath, $newFileName);
    
    // Clean up
    @unlink($uploadPath);
    
    if ($emailSent) {
        $response['success'] = true;
        $response['message'] = 'Certificate submitted successfully! Check your Gmail for the attachment.';
    } else {
        $response['success'] = false;
        $response['message'] = 'Failed to send email. Please try again.';
        $response['fallback'] = true;
    }
    
} else {
    $response['success'] = false;
    $response['message'] = 'Invalid request method';
}

echo json_encode($response);

/**
 * Build HTML email body
 */
function buildEmailBody($fullName, $email, $course, $certificateDate, $message) {
    return '
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; }
        .header { background: linear-gradient(135deg, #6366f1, #ec4899); color: white; padding: 30px; text-align: center; border-radius: 20px 20px 0 0; }
        .content { background: white; padding: 30px; border-left: 1px solid #e5e7eb; border-right: 1px solid #e5e7eb; }
        .info-row { margin-bottom: 15px; padding: 15px; background: #f9fafb; border-radius: 12px; border-left: 4px solid #6366f1; }
        .label { font-weight: bold; color: #6366f1; }
        .footer { background: #1e1b4b; color: white; padding: 25px; text-align: center; border-radius: 0 0 20px 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>🎓 KNHS CSS NCII</h2>
            <p>Kauswagan National High School</p>
            <p>Kauswagan, Cagayan de Oro</p>
            <p style="color: #ffd700; margin-top: 10px;">Program by: Keith Dandan - ICT 12 Magsaysay</p>
        </div>
        <div class="content">
            <div class="info-row"><span class="label">Full Name:</span> ' . $fullName . '</div>
            <div class="info-row"><span class="label">Email:</span> ' . $email . '</div>
            <div class="info-row"><span class="label">Course:</span> ' . $course . '</div>
            ' . ($certificateDate ? '<div class="info-row"><span class="label">Date:</span> ' . $certificateDate . '</div>' : '') . '
            ' . ($message ? '<div class="info-row"><span class="label">Message:</span><br>' . nl2br(htmlspecialchars($message)) . '</div>' : '') . '
            <div class="info-row"><span class="label">Submitted:</span> ' . date('F j, Y - g:i A') . '</div>
            <div class="info-row" style="background: #dbeafe; border-left-color: #6366f1;">
                <span class="label">📎 Certificate Attached:</span><br>
                The certificate image is attached to this email.
            </div>
        </div>
        <div class="footer">
            <p><strong>Kauswagan National High School</strong></p>
            <p>Computer Systems Servicing NC II</p>
            <p style="margin-top: 15px; opacity: 0.7;">© 2026 KNHS CSS NCII</p>
        </div>
    </div>
</body>
</html>';
}

/**
 * Send email with attachment using Swift Mailer (built-in)
 */
function sendEmailWithAttachment($subject, $body, $filePath, $fileName) {
    $to = 'keithcharlespacatangdandan@gmail.com';
    $from = 'noreply@knhs-nc2.local';
    $fromName = 'KNHS CSS NCII';
    
    // Read the file
    if (!file_exists($filePath)) {
        return false;
    }
    
    $fileContent = chunk_split(base64_encode(file_get_contents($filePath)));
    
    // Create boundaries
    $boundary = md5(uniqid(time()));
    $boundary2 = md5(uniqid(time()));
    
    // Build headers
    $headers = "From: $fromName <$from>\r\n";
    $headers .= "Reply-To: $from\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";
    
    // Build message
    $message = "--$boundary\r\n";
    $message .= "Content-Type: multipart/alternative; boundary=\"$boundary2\"\r\n\r\n";
    
    // Plain text version
    $message .= "--$boundary2\r\n";
    $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $message .= strip_tags(str_replace('<br>', "\n", $body)) . "\r\n\r\n";
    
    // HTML version
    $message .= "--$boundary2\r\n";
    $message .= "Content-Type: text/html; charset=UTF-8\r\n";
    $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $message .= $body . "\r\n\r\n";
    $message .= "--$boundary2--\r\n\r\n";
    
    // Attachment
    $message .= "--$boundary\r\n";
    $message .= "Content-Type: application/octet-stream; name=\"$fileName\"\r\n";
    $message .= "Content-Transfer-Encoding: base64\r\n";
    $message .= "Content-Disposition: attachment; filename=\"$fileName\"; size=" . filesize($filePath) . ";\r\n\r\n";
    $message .= $fileContent . "\r\n";
    $message .= "--$boundary--";
    
    // Send
    return mail($to, $subject, $message, $headers);
}

/**
 * Sanitize input
 */
function sanitize($input) {
    $input = trim($input);
    $input = stripslashes($input);
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}
?>
