<?php
require_once "../vendor/autoload.php";
require_once "../env.php";
require_once "../db.php";
require_once "../file-manager.php";

use PHPMailer\PHPMailer\PHPMailer;

$pdo = getConnection();

header('Content-Type: application/json');

switch ($_SERVER['REQUEST_METHOD']) {
  case "POST":
    $json_data = json_decode(file_get_contents('php://input'), true);

    $user_id = $json_data['user_id'];
    $file_url = $json_data['file_url'];
    $subject = $json_data['subject'];
    $body = $json_data['body'];

    // Make sure it comes from the root dir
    $full_file_url = get_root_dir() . $file_url;
    $emails = get_emails($pdo, $user_id);

    // echo $full_file_url;
    
    send_mail($emails['student_email'], $subject, $body, $full_file_url);
    send_mail($emails['parent_email'], $subject, $body, $full_file_url);

    http_response_code(200); // Method Not Allowed
    echo json_encode(['message' => "Successfully sent emails."]);
    break;

  default:
    http_response_code(405); // Method Not Allowed
    echo json_encode(['message' => "Unsupported request method."]);
    break;
}

function send_mail(string $to, string $subject, string $body, ?string $file) {
  $mail = new PHPMailer(true);

  $mail->isSMTP();
  $mail->SMTPAuth = true;

  $mail->Host = "smtp.gmail.com";
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
  $mail->Port = 587;

  $from = get_from();
  $password = get_password();

  $mail->Username = $from;
  $mail->Password = $password;

  $mail->setFrom($from);
  $mail->addAddress($to);

  if($file !== null) {
    $mail->addAttachment($file);
  }

  $mail->isHTML(true);                                        
  $mail->Subject = $subject;
  $mail->Body    = $body;
  $mail->AltBody = $body;

  $mail->send();
}

function get_emails(PDO $pdo, string $user_id) {
  $sql = "
  SELECT 
    CASE 
        WHEN u.role = 'parent' THEN u.email
        ELSE parent.email
    END AS parent_email,
    CASE 
        WHEN u.role = 'student' THEN u.email
        ELSE student.email
    END AS student_email
  FROM 
    users u
  LEFT JOIN 
    parent_student_links psl_parent ON u.id = psl_parent.parent_id
  LEFT JOIN 
    parent_student_links psl_student ON u.id = psl_student.student_id
  LEFT JOIN 
    users parent ON psl_student.parent_id = parent.id
  LEFT JOIN 
    users student ON psl_parent.student_id = student.id
  WHERE 
    u.id = ?
  LIMIT 1
";

  $stmt = $pdo->prepare($sql);
  $stmt->execute([$user_id]);
  $emails = $stmt->fetch();

  return $emails;
}
?>
