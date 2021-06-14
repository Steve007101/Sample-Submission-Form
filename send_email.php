<!-- 
   Simple PHP mailer file. 
   Defines a function that uses $sendto_email, $sendto_name, $subject,
   and $body to send emails.
   Intended to be called by another PHP file which passes those
   variables
-->
<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// for using composer generated autoload.php
// for PHPMailer
//require 'C:/xampp/composer/vendor/autoload.php';

// for stand-alone PHPMailer files in ./PHPMailer
require ('./PHPMailer/src/Exception.php');
require ('./PHPMailer/src/PHPMailer.php');
require ('./PHPMailer/src/SMTP.php');

function sendEmail($sendto_email, $sendto_name, $subject, $body) {
   // assume $debug_log inherited from calling php file so as
   // not to over-write what's already there
   echo "\r\nMail to ".$sendto_email."...";
/* Create a new PHPMailer object. Passing TRUE to the constructor enables exceptions. */
   $mail = new PHPMailer(TRUE);

/* Open the try/catch block. */
   try {
   /* Set the mail sender. */
      $mail->setFrom('noreply@intercotradingco.com', 'No Reply');

   /* Add a recipient. */
      $mail->addAddress($sendto_email, $sendto_name); // inherited from calling php file

   /* Set the subject. */
      $mail->Subject = $subject; // inherited from calling php file

   /* Set the mail message body. */
   // since we send tables, it's html format
      $mail->isHTML(TRUE);
      $mail->Body = '<html>'.$body.'</html>'; 
   // inherited from calling php file


   /* Finally send the mail. */
      if ($mail->send()) {
         echo "\r\nSent...";
      } else {
         echo "\r\nNot Sent...";
      }

   }
   catch (Exception $e)
   {
   /* PHPMailer exception. */
      echo $e->errorMessage();
   }
   catch (\Exception $e)
   {
   /* PHP exception (note the backslash to select the global namespace Exception class). */
      echo $e->getMessage();
   }
      echo "\r\nsendEmail() finished...";
}


function sendEmailCC($sendto_email, $sendto_name, $subject, $body, $cc) {
   // assume $debug_log inherited from calling php file so as
   // not to over-write what's already there
   echo "\r\nMail to ".$sendto_email."...";
/* Create a new PHPMailer object. Passing TRUE to the constructor enables exceptions. */
   $mail = new PHPMailer(TRUE);

/* Open the try/catch block. */
   try {
   /* Set the mail sender. */
      $mail->setFrom('noreply@intercotradingco.com', 'No Reply');

   /* Add a recipient. */
      $mail->addAddress($sendto_email, $sendto_name); // inherited from calling php file
      if ($cc != "") {
         $mail->addCC($cc);
      }

   /* Set the subject. */
      $mail->Subject = $subject; // inherited from calling php file

   /* Set the mail message body. */
   // since we send tables, it's html format
      $mail->isHTML(TRUE);
      $mail->Body = '<html>'.$body.'</html>'; 
   // inherited from calling php file


   /* Finally send the mail. */
      if ($mail->send()) {
         echo "\r\nSent...";
      } else {
         echo "\r\nNot Sent...";
      }

   }
   catch (Exception $e)
   {
   /* PHPMailer exception. */
      echo $e->errorMessage();
   }
   catch (\Exception $e)
   {
   /* PHP exception (note the backslash to select the global namespace Exception class). */
      echo $e->getMessage();
   }
      echo "\r\nsendEmail() finished...";
}
?>
