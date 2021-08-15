<?php

namespace DressApi\Core\Mail;

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class CMail
{
    protected $mailer = null;

    public function __construct()
    {
        try
        {
            $this->mailer = new PHPMailer(true);

            //Server settings
            $this->mailer->SMTPDebug = SMTP::DEBUG_OFF;                  //Enable verbose debug output
            //    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                  //Enable verbose debug output
            $this->mailer->isSMTP();                                        //Send using SMTP
    
            // Configuration
            $this->mailer->Host       = MAIL_HOST;                 //Set the SMTP server to send through
            $this->mailer->SMTPAuth   = true;                            //Enable SMTP authentication
            $this->mailer->Username   = MAIL_USERNAME;             //SMTP username
            $this->mailer->Password   = MAIL_PASSWORD;             //SMTP password
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;  //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $this->mailer->Port       = MAIL_PORT;                 //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
        }
        catch(\Exception $ex)
        {
            print $ex->getMessage();
        }
    }

    public function setFrom($email, $name = ''){
       
        $this->mailer->setFrom($email, $name);
    }

    public function setReplyTo($email, $name = ''){
       
        $this->mailer->addReplyTo($email, $name);
    }

    public function addCC($email, $name = ''){
       
    //    $mail->addCC('cc@example.com');
        $this->mailer->addCC($email);
    }

    public function addAttachment($filePath){
       
        $this->mailer->addAttachment($filePath);         //Add attachments
    }
    
    public function send($toEmail, $toName, $subject, $body_html, $body_text = ''){
       
        $ret = '';
        try
        {
            //Recipients
            $this->mailer->addAddress($toEmail, $toName);     //Add a recipient

            //Content
            $this->mailer->isHTML(true);                                  //Set email format to HTML
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $body_html;
            $this->mailer->AltBody = $body_text;

            $sent = $this->mailer->send();
            
            $ret = 'Message has '.($sent?'':'not ').'been sent'; 

        }
        catch(\Exception $e)
        {
            $ret = 'Message has not been sent';
        }
        return $ret;
    }

    public function sendFromtemplate($toEmail, $toName, $template, $replaces = null){

        $subject = 'No Subject';
        $body = $body_text = 'No Body';

        if ($replaces!==null){
            $labels = array_map(function($a) { return "[".$a."]"; }, array_keys($replaces) );
            $values = array_values($replaces);
        }

        $fileSubject = realpath(__DIR__.'/templates/'.$template.'.txt');
        if ($fileSubject){
            $body = str_replace($labels, $values, file_get_contents($fileSubject));
        
            $body = utf8_decode ( $body );

            $res = preg_match("/<title>(.*)<\/title>/siU", $body, $title_matches);
            if ($res) 
                $subject = trim($title_matches[1]); 

            $res = preg_match("/<body>(.*)<\/body>/siU", $body, $body_matches);
            if ($res) 
                $body_text = strip_tags(str_replace(['<br/>','<br>'],"\n",$body[1]));     
        }

        return $this->send($toEmail, $toName, $subject, $body, $body_text);
    }

}
