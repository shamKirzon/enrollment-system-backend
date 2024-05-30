<?php
require_once "vendor/autoload.php";
require_once "./file-manager.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function mail_test() {
  $mail = new PHPMailer(true);

  $mail->isSMTP();
  $mail->SMTPAuth = true;

  $mail->Host = "smtp.gmail.com";
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
  $mail->Port = 587;

  $from = "giordnnuz.dummy@gmail.com";
  $to = "giordnnuz27@gmail.com";

  $mail->Username = $from;
  $mail->Password = "ppokktuiytvigozr";

  $mail->setFrom($from);
  $mail->addAddress($to);

  // General directory where the file
  $storage_dir = "/storage/";

  // Sample file
  $file_path = get_root_dir() . $storage_dir . "student_5-g12-8.jpg";

  $mail->addAttachment($file_path);

  $mail->isHTML(true);                                        
  $mail->Subject = 'Here is the subject';
  $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
  $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

  $mail->send();
  echo 'Message has been sent';
}


switch ($_SERVER['REQUEST_METHOD']) {
  case 'POST':
    mail_test();
    break;
}
?>
