<?php
namespace src\lib\mailer;

use PHPMailer\PHPMailer\PHPMailer;

require dirname(__FILE__)."/PHPMailer/src/PHPMailer.php";
require dirname(__FILE__)."/PHPMailer/src/SMTP.php";

class Mailer
{

    public function __construct()
    {
        $this->mailer = new PHPMailer();

        $this->admin_email_username = 'ielts@programmingly.com';
        $this->admin_email_password = 'B]MI)C74mfUE';
    }

    public function send($email,$title,$subject,$message){

     

        $this->mailer->CharSet  =  "utf-8";
        $this->mailer->IsSMTP();

        // enable SMTP authentication
        $this->mailer->SMTPAuth = true;
        //  username
        $this->mailer->Username = $this->admin_email_username;
        //  password
        $this->mailer->Password = $this->admin_email_password;
        $this->mailer->SMTPSecure = "ssl";
        // sets ZOHO as the SMTP server
        $this->mailer->Host     = "mail.programmingly.com";
        // set the SMTP port for the  server
        $this->mailer->Port     = "465";

        $this->mailer->SetFrom($this->admin_email_username, $title);
        $this->mailer->AddAddress($email);
        $this->mailer->Subject  =  $subject;
        $this->mailer->IsHTML(true);
        $this->mailer->Body    = $message;

        $send = $this->mailer->Send();
        /*smtp mail */

        if ($send) {
            return true;
        }else{
            return false;
        }
    }

}