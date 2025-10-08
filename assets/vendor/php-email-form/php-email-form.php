<?php

class PHP_Email_Form {
  public $to;
  public $from_name;
  public $from_email;
  public $subject;
  public $messages = [];
  public $ajax = false; // For compatibility, no effect in this simple version

  public $smtp = null; // Optional SMTP config array

  public function add_message($value, $label = '', $max_length = 1000) {
    $value = trim($value);
    if(strlen($value) > $max_length) {
      $value = substr($value, 0, $max_length);
    }
    $this->messages[] = ['label' => $label, 'value' => htmlspecialchars($value)];
  }

  public function send() {
    if(empty($this->to)) {
      return $this->response(false, 'No recipient email specified');
    }
    if(empty($this->from_email) || !filter_var($this->from_email, FILTER_VALIDATE_EMAIL)) {
      return $this->response(false, 'Invalid sender email');
    }
    if(empty($this->from_name)) {
      $this->from_name = 'Website Visitor';
    }
    if(empty($this->subject)) {
      $this->subject = 'New message from contact form';
    }

    $body = '';
    foreach($this->messages as $msg) {
      if($msg['label']) {
        $body .= $msg['label'] . ": ";
      }
      $body .= $msg['value'] . "\n\n";
    }

    $headers = "From: " . $this->from_name . " <" . $this->from_email . ">\r\n";
    $headers .= "Reply-To: " . $this->from_email . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    if($this->smtp) {
      // Send using SMTP with PHPMailer library (optional and not implemented here)
      // You would need PHPMailer or similar installed and configured to use SMTP
      // Return error for now as SMTP is not implemented in this simple version
      return $this->response(false, 'SMTP sending not implemented in this version');
    } else {
      // Use PHP mail()
      $mail_sent = mail($this->to, $this->subject, $body, $headers);
      if($mail_sent) {
        return $this->response(true, 'Message sent successfully');
      } else {
        return $this->response(false, 'Failed to send message');
      }
    }
  }

  private function response($success, $message) {
    if($this->ajax) {
      header('Content-Type: application/json');
      return json_encode(['success' => $success, 'message' => $message]);
    } else {
      return $message;
    }
  }
}
