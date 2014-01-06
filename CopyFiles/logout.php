<?php
if(!isset($_SESSION)){  session_start(); }
if(isset($_SESSION['applicant_login'])){  unset($_SESSION['applicant_login']); }
if(isset($_SESSION['applicant'])){  unset($_SESSION['applicant']); }
if(isset($_SESSION['applicant_over'])){  unset($_SESSION['applicant_over']); }
if(isset($_SESSION['admin'])){  unset($_SESSION['admin']); }

header("Location: applicant_login.php");
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
