<?php
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
    $this->_mailer->Host = "smtp.gmail.com";
    $this->_mailer->SMTPDebug = 0;
    $this->_mailer->SMTPAuth = true;
    $this->_mailer->SMTPSecure = "tls";
    $this->_mailer->Port = 587;
    $this->_mailer->Username = "phd.mnit@gmail.com";
    $this->_mailer->Password = "acad.mnit";
    $this->_mailer->SetFrom("phd.mnit@gmail.com","MNIT Phd Portal");
    $this->_mailer->AddReplyTo("phd.mnit@gmail.com","MNIT Phd Portal");
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
