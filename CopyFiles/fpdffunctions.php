<?php

$lh1 = 2;
$lh2 = 4;
$lh3 = 6;
$lh4 = 8;
$lh5 = 10;
$lh6 = 12;
$lh7 = 14;

function set_font_size($style = "") {
  global $fpdf;
  switch ($style) {
    case "h1":
      $fpdf->SetFont('Times', 'B', 16);
      break;
    case "h2":
      $fpdf->SetFont('Times', 'B', 14);
      break;
    case "h3":
      $fpdf->SetFont('Times', 'B', 12);
      break;
    case "h4":
      $fpdf->SetFont('Times', 'B', 10);
      break;
    case "h5":
      $fpdf->SetFont('Times', 'B', 8);
      break;
    case "h6":
      $fpdf->SetFont('Times', 'B', 6);
      break;
    case "p1":
      $fpdf->SetFont('Times', '', 6);
      break;
    case "p2":
      $fpdf->SetFont('Times', '', 10);
      break;
    case "p3":
      $fpdf->SetFont('Times', '', 12);
      break;
    case "p4":
      $fpdf->SetFont('Times', '', 14);
      break;
    default :
      $fpdf->SetFont('Times', '', 8);
  }
}

function put_Hr($x, $w) {
  global $fpdf;
  $y = $fpdf->GetY();
  $fpdf->Line($x, $y, $x + $w, $y);
  $fpdf->Ln();
}

function PDFprintline(array $line) {
  global $fpdf, $lh3;
  $colwidth = 180 / count($line);
  $heading = true;
  $y = $fpdf->GetY();
  $x = $fpdf->GetX();
  $max_y = $y;

  foreach ($line as $cell) {
    $fpdf->SetY($y);
    $fpdf->SetX($x + 2);
    $x +=$colwidth;
    if ($heading) {
      set_font_size("h5");
    } else {
      set_font_size();
    }
    $heading = !$heading;
    $fpdf->MultiCell($colwidth - 2, $lh3, $cell, 0);
    if ($max_y < $fpdf->GetY()) {
      $max_y = $fpdf->GetY();
    }
  }
  $fpdf->SetY($max_y);
}
function PDFprintline2(array $line) {
  global $fpdf, $lh3;
  $colwidth = 180 / count($line);
  $heading = true;
  $y = $fpdf->GetY();
  $x = $fpdf->GetX();
  $max_y = $y;

  foreach ($line as $cell) {
    $fpdf->SetY($y);
    $fpdf->SetX($x + 2);
    $x +=$colwidth;
    if ($heading) {
      set_font_size("h4");
    } else {
      set_font_size("h5");
    }
    $heading = !$heading;
    $fpdf->MultiCell($colwidth - 2, $lh3, $cell, 0);
    if ($max_y < $fpdf->GetY()) {
      $max_y = $fpdf->GetY();
    }
  }
  $fpdf->SetY($max_y);
}
/*
function PDFprintdata(array $data) {
  foreach ($data as $line) {
    PDFprintline($line);
  }
}

function PDFprintTable(array $data) {
  $line = $data[0];
  global $fpdf, $lh3;
  $colwidth = 180 / count($line);
  $y = $fpdf->GetY();
  $x = $fpdf->GetX();
  $max_y = $y;
  set_font_size("h5");
  foreach ($line as $cell) {
    $fpdf->SetY($y);
    $fpdf->SetX($x + 2);
    $x +=$colwidth;
    $fpdf->MultiCell($colwidth - 2, $lh3, $cell, 0);
  }

  set_font_size();
  for ($i = 1; $i < count($data); $i++) {
    $line = $data[$i];
    $y = $fpdf->GetY();
    $x = $fpdf->GetX();
    foreach ($line as $cell) {
      $fpdf->SetY($y);
      $fpdf->SetX($x + 2);
      $x +=$colwidth;
      $fpdf->MultiCell($colwidth - 2, $lh3, $cell, 0);
      if ($max_y < $fpdf->GetY()) {
        $max_y = $fpdf->GetY();
      }
    }
    $fpdf->SetY($max_y);
  }
}
*/

function drawRow(array $line, $border = FALSE, $alternate_bold = FALSE) {
  global $fpdf;
  global $lh3;
  $countColumns = count($line);
  if ($countColumns <= 0) {
    return;
  }

  $colWidth = (190) / $countColumns;
  $count = 0;
  $iniX = $fpdf->GetX();
  $y = $fpdf->GetY();
  $maxY = 0;

  $maxLh = 1;
  
  //
  // Calculating weather to put the line on next page or not...
  //
  foreach ($line as $cell) {
    $len = $fpdf->GetStringWidth($cell);
    $len = $len / $colWidth;
    if (ceil($len) > $maxLh) {
      $maxLh = ceil($len);
    }
  }
  if (($y + ($lh3 * $maxLh)) > 250) {
    $fpdf->AddPage();
    $y = 15;
  }
  
  //
  //  Printing the table to the document
  //
  foreach ($line as $cell) {
    $x = ($count * $colWidth + $iniX);
    $fpdf->SetY($y);
    $fpdf->SetX($x);

    if ($alternate_bold) {
      if ($count % 2 == 0) {
        set_font_size("h5");
      } else {
        set_font_size();
      }
    }
    $count++;
    $fpdf->MultiCell($colWidth - 2, $lh3, $cell);
    if ($fpdf->GetY() > $maxY) {
      $maxY = $fpdf->GetY();
    }
  }

  //
  // Drawing the border of the table
  //
  if ($border) {

    $fpdf->Line($iniX, $y, ($iniX + 190), $y);
    $fpdf->Line($iniX, $maxY, ($iniX + 190), $maxY);
    $fpdf->Line($iniX, $y, $iniX, $maxY);
    $fpdf->Line($iniX + 190, $y, $iniX + 190, $maxY);

    for ($i = 1; $i <= $count; $i++) {
      $x = ($i * $colWidth + $iniX);
      $fpdf->Line($x, $y, $x, $maxY);
    }
  }
  
  $fpdf->SetY($maxY);
}

function drawRow2(array $line, $border = FALSE, $alternate_bold = FALSE) {
  global $fpdf;
  global $lh3;
  $countColumns = count($line);
  if ($countColumns <= 0) {
    return;
  }

  $colWidth = (190) / $countColumns;
  $count = 0;
  $iniX = $fpdf->GetX();
  $y = $fpdf->GetY();
  $maxY = 0;

  $maxLh = 1;
  
  //
  // Calculating weather to put the line on next page or not...
  //
  foreach ($line as $cell) {
    $len = $fpdf->GetStringWidth($cell);
    $len = $len / $colWidth;
    if (ceil($len) > $maxLh) {
      $maxLh = ceil($len);
    }
  }
  if (($y + ($lh3 * $maxLh)) > 250) {
    $fpdf->AddPage();
    $y = 15;
  }
  
  //
  //  Printing the table to the document
  //
  foreach ($line as $cell) {
    $x = ($count * $colWidth + $iniX);
    $fpdf->SetY($y);
    $fpdf->SetX($x);

    if ($alternate_bold) {
      if ($count % 2 == 0) {
        set_font_size("h4");
      } else {
        set_font_size("h3");
      }
    }
    $count++;
    $fpdf->MultiCell($colWidth - 2, $lh3, $cell);
    if ($fpdf->GetY() > $maxY) {
      $maxY = $fpdf->GetY();
    }
  }

  //
  // Drawing the border of the table
  //
  if ($border) {

    $fpdf->Line($iniX, $y, ($iniX + 190), $y);
    $fpdf->Line($iniX, $maxY, ($iniX + 190), $maxY);
    $fpdf->Line($iniX, $y, $iniX, $maxY);
    $fpdf->Line($iniX + 190, $y, $iniX + 190, $maxY);

    for ($i = 1; $i <= $count; $i++) {
      $x = ($i * $colWidth + $iniX);
      $fpdf->Line($x, $y, $x, $maxY);
    }
  }
  
  $fpdf->SetY($maxY);
}

function PDFprintdata(array $data) {
  set_font_size();
  foreach ($data as $line) {
    drawRow((array)$line, false, true);
  }
}

function PDFprintdata2(array $data) {
  set_font_size();
  foreach ($data as $line) {
    drawRow2((array)$line, false, true);
  }
}

function PDFprintTable(array $data){
  set_font_size('h4');
  drawRow($data[0],TRUE);
  set_font_size();
  
  for($i=1;$i<count($data);$i++){
    drawRow((array)$data[$i],true,false);
  }
}

?>
