<?php
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\SMTP;
  use PHPMailer\PHPMailer\Exception;

  $root = realpath(__DIR__ . '/../../..');
  require $root . '/vendor/autoload.php';

  // Load .env values manually
  function loadEnv($file) {
    $env = [];
    if (file_exists($file)) {
      $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
      foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        [$key, $value] = explode('=', $line, 2);
        $env[trim($key)] = trim($value);
      }
    }
    return $env;
  }

  // Load environment variables
  $env = loadEnv($root . '/.env');

  // Required SMTP config
  $smtpHost = $env['MAIL_HOST'] ?? '';
  $smtpPort = (int)($env['MAIL_PORT'] ?? 465);
  $smtpUsername = $env['MAIL_USERNAME'] ?? '';
  $smtpPassword = $env['MAIL_PASSWORD'] ?? '';
  $smtpEncryption = $env['MAIL_ENCRYPTION'] ?? PHPMailer::ENCRYPTION_SMTPS;

  // Fetch form data
  $senderName = $_POST['name'] ?? '';
  $senderEmail = $_POST['email'] ?? '';
  $emailSubject = $_POST['subject'] ?? '';
  $emailMessageContent = $_POST['message'] ?? '';

  // Validate required fields
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error']);
    exit;
  }

  if (empty($smtpUsername)) {
    echo json_encode(['status' => 'error']);
    exit;
  }

  try {
    // Create PHPMailer instance
    $mail = new PHPMailer(true);

    // SMTP settings
    $mail->isSMTP();
    $mail->Host = $smtpHost;
    $mail->SMTPAuth = true;
    $mail->Username = $smtpUsername;
    $mail->Password = $smtpPassword;
    $mail->SMTPSecure = $smtpEncryption;
    $mail->Port = $smtpPort;

    // Enable debug output if needed (comment in production)
    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;

    // Sender and recipient
    $mail->setFrom($smtpUsername, $senderName ?: 'Website');
    if (!empty($senderEmail)) {
      $mail->addReplyTo($senderEmail, $senderName);
    }
    $mail->addAddress($smtpUsername);

    // Content
    $mail->isHTML(true);
    $mail->Subject = $emailSubject;
    $mail->Body = nl2br($emailMessageContent);
    $mail->AltBody = $emailMessageContent;

    // Send email
    $mail->send();
    echo json_encode(['status' => 'success']);

  } catch (Exception $e) {
    echo json_encode(['status' => 'error']);
  }
?>
