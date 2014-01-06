<?php
require_once 'Classes/fpdf.php';
require_once 'Classes/fpdffunctions.php';
require_once 'functions.php';
require_once 'Classes/PortalMailer.php';

class PDF extends FPDF {

//  function Header() {
//    // Position at 1.5 cm from bottom
//    $this->SetY(5);
//    // Arial italic 8
//    $this->SetFont('Arial', 'I', 8);
//    // Page number
//    $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
//    $this->SetX(15);
//    $this->Cell(0, 10, '01/MNIT/ESTT/2012', 0, 0 ,'L');
//    $this->Ln();
//  }
  
  function Footer() {
    global $applicant;
    // Position at 1.5 cm from bottom
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial', 'I', 8);
    // Page number
    $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    $this->SetX(15);
    $this->Cell(0, 10, APP_REF_PREFIX.$applicant.APP_REF_SUBFIX, 0, 0 ,'L');
    $this->Cell(0, 10, 'Generic Portal', 0, 0 ,'R');
  }

}

if(!$db)  { $db = new MySQLDataBase(); }
if(!isset($_SESSION)){ session_start(); }
if(!isset($_SESSION['applicant'])){ header("Location: index.php"); }
$applicant = $_SESSION['applicant'];

$fpdf = new PDF();
$fpdf->AliasNbPages('{nb}');

$fpdf->AddPage();
set_font_size("h1");
$fpdf->Cell(190, $lh3, PDF_HEADER, 0, 1, 'C');
set_font_size("h5");
$fpdf->Cell(190, $lh2, PDF_SUB_HEADER, 0, 1, 'C');

$fpdf->Cell(190, $lh6, '', 0, 1, 'C');

$x2 = $fpdf->GetX();
$y2 = $fpdf->GetY();
$photofile = "PDF_FILES/photo_$applicant.jpg";
if(file_exists($photofile)){
	$fpdf->Image($photofile, $x2 + 120, $y2, 20, 15);
}

$query = "SELECT * FROM `applicant_1` WHERE `id` = '$applicant'";
$db->query($query);
$result = $db->last_result[0];
$appemail = $result->username;

$data_line = array('','','','');
$data_line[0] = "FILE REF NO#";
$data_line[1] = APP_REF_PREFIX.$applicant.APP_REF_SUBFIX;
PDFprintline($data_line);
$data_line[0] = 'EMAIL';
$data_line[1] = $result->username;
PDFprintline($data_line);

$fpdf->SetXY($x2, $y2 + 20);

$fpdf->Cell(190, $lh4, '', 0, 1, 'C');

set_font_size('h3');
$fpdf->Write($lh3,'Personal Information');
set_font_size('h3');

$fpdf->Cell(190, $lh4, '', 0, 1, 'C');
$query = "SELECT  `first_name` ,  `middle_name` ,  `last_name` ,  DATE_FORMAT(`date_of_birth`,'%d %M, %Y') as 'date_of_birth'  FROM `tab_1_page_1` WHERE `applicant` = '$applicant'"; 
$db->query($query);


$data = array();
$result = $db->last_result[0];
$dataline = array('','','','');
$dataline[0] = 'First Name';
$dataline[1] = $result->first_name;
$dataline[2] = 'middle Name';
$dataline[3] = $result->middle_name;
$data[] = $dataline;
$dataline = array('','','','');
$dataline[0] = 'Last Name';
$dataline[1] = $result->last_name;
$dataline[2] = 'Date of Birth';
$dataline[3] = $result->date_of_birth;
$data[] = $dataline;
PDFprintdata($data);


/*
*  Printing the application form declaration
*/
$fpdf->Ln();
$fpdf->Ln();
$fpdf->Ln();
set_font_size('h3');
$fpdf->Write($lh3,'Declaration');
set_font_size('p2');
$fpdf->Ln();

$declaration = "I hereby declare that the information furnished above is true to the best of my knowledge and belief. I understand, if at any time, it is found that I have concealed any information or have given any incorrect data, my candidature/appointment may be cancelled/terminated without any notice or compensation.
";
$fpdf->Write(8,$declaration);
$fpdf->Ln();

$data = array();

$row = array();
$row[] = "";
$row[] = "";
$row[] = "";
$row[] = "";
$data[] = $row;

$row = array();
$row[] = "";
$row[] = "";
$row[] = "";
$row[] = "";
$data[] = $row;

$row = array();
$row[] = "";
$row[] = "";
$row[] = "";
$row[] = "";
$data[] = $row;


$row = array();
$row[] = "(Date)";
$row[] = "";
$row[] = "";
$row[] = "(Signature)";
$data[] = $row;

PDFprintdata($data);
set_font_size("p1");
$fpdf->Write($lh3, "(No Signatures required for electronic submission.)");
$fpdf->ln();

$fpdf->ln();
$fpdf->ln();
put_Hr($fpdf->GetX(), 190);
set_font_size("h4");
$fpdf->Write($lh3, "Instructions for Shortlisted Candidates");
$fpdf->ln();
$fpdf->ln();

set_font_size("h2");
$fpdf->Write($lh3,"Documents to be brought at the time of Personal Interaction");
$fpdf->ln();

set_font_size("h4");
$fpdf->Write($lh3, "a.) Printed copy of the electronic application, duly signed by the applicant");
$fpdf->ln();

set_font_size("h4");
$fpdf->Write($lh3, "b.) Also please ensure to attach the self attested coppy of following certificates and performae");
$fpdf->ln();
//put_Hr($fpdf->GetX(), 190);
set_font_size();

$data = array();

$row = array();
$row[] = "1. Date of Birth Certificate";
$data[] = $row;

$row = array();
$row[] = "2. Marksheets/Degree ";
$data[] = $row;

$row = array();
$row[] = "3. No Objection Certificate (for those serving in Govt/Semi Govt./PSU/Universities/Educational Institutes)";
$data[] = $row;

$row = array();
$row[] = "4. Original Documents of the published Papers/Books/Patents etc.";
$data[] = $row;

$row = array();
$row[] = "5. Details of experience in form of employers certificate.";
$data[] = $row;

$row = array();
$row[] = "";
$data[] = $row;

$row = array();
$row[] = "Originals of all relevant documents should be brought at time of discussion.";
$data[] = $row;

PDFprintdata($data);

$fpdf->Output("PDF_FILES/file$applicant.pdf");
$fpdf->Close();

$mailor = new PortalMailer();
$mailor->AddAddress($appemail);
//$mailor->AddAddress($objapplicant->getUsername()); //For the fixed mail address
$mailor->SetSubject("Submission of the application.");
$msg = "<p>Dear Applicant,</p><p></p><p>"."Your application has been successfully submitted with Reference No : ".APP_REF_PREFIX.$applicant.APP_REF_SUBFIX.". A PDF of application is attached for your future refference"."</p>";
$msg.= "<p>".""."</p>";
$mailor->AddMessage($msg);
$mailor->AddAtachment("PDF_FILES/file$applicant.pdf",  APP_REF_PREFIX.$applicant.APP_REF_SUBFIX.'.pdf');
$mailor->SendMail();


unset($_SESSION['applicant']);
$_SESSION['applicant_over'] = $applicant;
header("Location: downloadapplication.php");
?>

