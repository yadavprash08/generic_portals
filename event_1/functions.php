<?php

require_once 'Classes/MYSQLDATABASE.php';

$db = new MySQLDataBase();

function showLimitedText($str) {
  $maxlen = 30;
  $return = "";
  if (strlen($str) > $maxlen) {
    $return = substr($str, 0, $maxlen - 3) . "...";
  } else {
    $return = $str;
  }

  return $return;
}

function drawHTMLTABLE(array $data, $tableclass = "table") {
  echo "<table class=\"$tableclass\">\n";
  echo "\t<thead>\n";
  echo "\t\t<tr>\n";
  $head = $data[0];
  foreach ($head as $cell) {
    echo "\t\t\t<th>$cell</th>\n";
  }
  echo "\t\t</tr>\n";
  echo "\t</thead>\n";
  unset($data[0]);
  $rowcls = 0;
  foreach ($data as $row) {
    echo "\t<tr class=\"row" . ($rowcls + 1) . "\">\n";
    $rowcls = ($rowcls + 1) % 2;
    $cls = 1;
    foreach ($row as $cell) {
      echo "\t\t<td class=\"cell" . $cls++ . "\">$cell</td>\n";
    }
    echo "\t</tr>\n";
  }
  echo "</table>\n";
}

function ArrayToCSV(array $data) {
  $file_data = "";
  foreach ($data as $row) {
    foreach ($row as $cell) {
      $cdata = str_replace("\n", " ", $cell);
      $cdata = str_replace(",", " ", $cdata);
      $cdata = str_replace(chr(13), " ", $cdata);
      $file_data.= "".$cdata. ",";
    }
    $file_data.="\n";
  }

  return $file_data;
}

//fileuploadfunction
function uploadFilesJPG2M() {
  //echo 'entering';
  $keys = array_keys($_FILES);
  $msg = array();
  foreach ($keys as $i) {
    if ($_FILES[$i]["name"] != "") {
      $extension = substr(strrchr($_FILES[$i]["name"], "."), 1);

      //echo $extension;
      if (strtolower($extension) != "jpg") {
        $msg[] = $_FILES[$i]["name"] . " is not a file with proper format (.jpg).";
      } else {
        //checking for the file size
        if ($_FILES[$i]["size"] > 2097152) {
          $msg[] = $_FILES[$i]["name"] . " exceeded the maximum permisible file size.";
        } else {
          move_uploaded_file($_FILES[$i]['tmp_name'], 'PDF_FILES/' . $i . ".jpg");
        }
      }
    }
  }
  return $msg;
}

//fileuploadfunction
function uploadFilesPDF2M() {
  //echo 'entering';
  $keys = array_keys($_FILES);
  $msg = array();
  foreach ($keys as $i) {
    if ($_FILES[$i]["name"] != "") {
      $extension = substr(strrchr($_FILES[$i]["name"], "."), 1);

      //echo $extension;
      if (strtolower($extension) != "pdf") {
        $msg[] = $_FILES[$i]["name"] . " is not a file with proper format (.PDF).";
      } else {
        //checking for the file size
        if ($_FILES[$i]["size"] > 2097152) {
          $msg[] = $_FILES[$i]["name"] . " exceeded the maximum permisible file size.";
        } else {
          move_uploaded_file($_FILES[$i]['tmp_name'], 'PDF_FILES/' . $i . ".pdf");
        }
      }
    }
  }
  return $msg;
}

?>
