<?php
require_once 'DEFINE_PARAM.php';
require_once 'PHPMailer_v5.1/class.phpmailer.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PortalMailer
 *
 * @author Prashant
 */
class PortalMailer {
  //put your code here
  private $_mailer;
  
  public function __construct() {
    $this->_mailer = new PHPMailer();
    $this->_mailer->IsSMTP();
    $this->_mailer->Host = SMTP_HOST;
    $this->_mailer->SMTPDebug = 0;
    $this->_mailer->SMTPAuth = SMTP_AUTH;
    $this->_mailer->SMTPSecure = SMTP_SECURE;
    $this->_mailer->Port = SMTP_PORT;
    $this->_mailer->Username = SMTP_USERNAME;
    $this->_mailer->Password = SMTP_PASSSWORD;
    $this->_mailer->SetFrom(SMTP_USERNAME,SMTP_SENDFROM);
    $this->_mailer->AddReplyTo(SMTP_USERNAME,SMTP_SENDFROM);
  }
  
  public function AddAddress($address,$name=''){
    $this->_mailer->AddAddress($address, $name);
  }
  
  public function SetSubject($subject){
    $this->_mailer->Subject = $subject;
  }
  
  public function AddMessage($message){
    $this->_mailer->MsgHTML($message);
  }
  
  public function AddAtachment($path,$name){
    $this->_mailer->AddAttachment($path, $name);
  }
  
  public function ClearAddresses(){
    $this->_mailer->ClearAddresses();
  }
  
  public function ClearAttachments(){
    $this->_mailer->ClearAttachments();
  }
  
  public function SendMail(){
    $this->_mailer->Send();
  }
}

?>
