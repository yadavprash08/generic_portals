<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'functions.php';
require_once 'Classes/EVENTS.php';
require_once 'Classes/DirectoryTraversal.php';

if (!isset($_GET['event'])) {
    header("Location: index.php");
}
if (!isset($db)) {
    $db = new MySQLDataBase();
}
if (!$db) {
    $db = new MySQLDataBase();
}

$event = $_GET['event'];

function download_Portal_Code() {
    global $event;

    exec("tar cvzf event.tgz event_" . $event);
    $file_content = file_get_contents('event.tgz');
    $file_size = filesize('event.tgz');
    unlink("event.tgz");
    rmdir("event_" . $event);

    //starting the download option
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="Application.pdf"');
    header('Content-Length:' . $file_size);
    echo $file_content;
    die();
}

if (isset($_POST['downloadportal'])) {
    if (PHP_OS == "Linux") {
        download_Portal_Code();
    }
}

require_once 'header.php';
?>
<pre>
    <?php
    $objevent = new EVENTS($event, $db);
    $base_dir = "event_$event";
    mkdir($base_dir, 0777);
    if (is_dir($base_dir)) {
        echo "Directory Created\n";
    } else {
        echo "Failed to create the directory\n";
    }

    mkdir($base_dir . "/Classes", 0777);
    mkdir($base_dir . "/admin", 0777);
    mkdir($base_dir . "/PDF_FILES", 0777);
    $curdir = getcwd();

//$filename = "CopyFiles/DEFINE_PARAM.php";
//if(!copy($filename, $base_dir."/Classes/DEFINE_PARAM.php")){echo "Failed to copy $filename\n";}
    $filename = "CopyFiles/logout.php";
    if (!copy($filename, $base_dir . "/logout.php")) {
        echo "Failed to copy $filename\n";
    }
    $filename = "CopyFiles/downloadapplication.php";
    if (!copy($filename, $base_dir . "/downloadapplication.php")) {
        echo "Failed to copy $filename\n";
    }
    $filename = "CopyFiles/fpdffunctions.php";
    if (!copy($filename, $base_dir . "/Classes/fpdffunctions.php")) {
        echo "Failed to copy $filename\n";
    }
    $filename = "CopyFiles/MYSQLDATABASE.php";
    if (!copy($filename, $base_dir . "/Classes/MYSQLDATABASE.php")) {
        echo "Failed to copy $filename\n";
    }
    $filename = "CopyFiles/functions.php";
    if (!copy($filename, $base_dir . "/functions.php")) {
        echo "Failed to copy $filename\n";
    }
    $filename = "CopyFiles/site.css";
    if (!copy($filename, $base_dir . "/site.css")) {
        echo "Failed to copy $filename\n";
    }
    if (!copy($filename, $base_dir . "/admin/site.css")) {
        echo "Failed to copy $filename\n";
    }
    $filename = "CopyFiles/script.js";
    if (!copy($filename, $base_dir . "/script.js")) {
        echo "Failed to copy $filename\n";
    }
    if (!copy($filename, $base_dir . "/admin/script.js")) {
        echo "Failed to copy $filename\n";
    }
//$filename = "CopyFiles/header.php";
//if(!copy($filename, $base_dir."/header.php")){echo "Failed to copy $filename\n";}
    $filename = "CopyFiles/admin_header.php";
    if (!copy($filename, $base_dir . "/admin/header.php")) {
        echo "Failed to copy $filename\n";
    }
    $filename = "CopyFiles/footer.php";
    if (!copy($filename, $base_dir . "/footer.php")) {
        echo "Failed to copy $filename\n";
    }
    if (!copy($filename, $base_dir . "/admin/footer.php")) {
        echo "Failed to copy $filename\n";
    }

//Copying the JS FILES
    mkdir($base_dir . "/js");
    $objdir = new DirectoryTraversal("CopyFiles/js");
    $files = $objdir->showallfiles(TRUE);
    foreach ($files as $file) {
        $save_file = str_replace("CopyFiles/", $base_dir . "/", $file);
        $dirname = dirname($save_file);
        if (!is_dir($dirname)) {
            mkdir($dirname, 0777);
        }
        if (!copy($file, $save_file)) {
            echo "Failed to copy $file to $save_file\n";
        }
    }

//Copying the CSS FILES
    mkdir($base_dir . "/css");
    $objdir = new DirectoryTraversal("CopyFiles/css");
    $files = $objdir->showallfiles(TRUE);
    foreach ($files as $file) {
        $save_file = str_replace("CopyFiles/", $base_dir . "/", $file);
        $dirname = dirname($save_file);
        if (!is_dir($dirname)) {
            mkdir($dirname, 0777);
        }
        if (!copy($file, $save_file)) {
            echo "Failed to copy $file to $save_file\n";
        }
    }

//Copying the Portal Mailor class
    $filename = "CopyFiles/PortalMailer.php";
    if (!copy($filename, $base_dir . "/Classes/PortalMailer.php")) {
        echo "Failed to copy $filename\n";
    }

    mkdir($base_dir . "/Classes/PHPMailer_v5.1");
    $objdir = new DirectoryTraversal("CopyFiles/PHPMailer_v5.1");
    $files = $objdir->showallfiles(TRUE);
    foreach ($files as $file) {
        $save_file = str_replace("CopyFiles/", $base_dir . "/Classes/", $file);
        $dirname = dirname($save_file);
        if (!is_dir($dirname)) {
            mkdir($dirname, 0777);
        }
        if (!copy($file, $save_file)) {
            echo "Failed to copy $file to $save_file\n";
        }
    }

//Copying The FPDF FILES 
    $filename = "CopyFiles/fpdf.php";
    if (!copy($filename, $base_dir . "/Classes/fpdf.php")) {
        echo "Failed to copy $filename\n";
    }
    $objdir = new DirectoryTraversal("CopyFiles/font");
    $files = $objdir->showallfiles(TRUE);
    foreach ($files as $file) {
        $save_file = str_replace("CopyFiles/", $base_dir . "/Classes/", $file);
        $dirname = dirname($save_file);
        if (!is_dir($dirname)) {
            mkdir($dirname, 0777);
        }
        if (!copy($file, $save_file)) {
            echo "Failed to copy $file to $save_file\n";
        }
    }

//Copying the images folder
    $objdir = new DirectoryTraversal("CopyFiles/images");
    $files = $objdir->showallfiles(TRUE);
    foreach ($files as $file) {
        $save_file = str_replace("CopyFiles/", $base_dir . "/", $file);
        $dirname = dirname($save_file);
        if (!is_dir($dirname)) {
            mkdir($dirname, 0777);
        }
        if (!copy($file, $save_file)) {
            echo "Failed to copy $file to $save_file\n";
        }
    }

    $filename = "CopyFiles/header.php";
    $content = file_get_contents($filename);
    $content = str_replace("%applicationname%", $objevent->getName(), $content);
    $fpw = fopen("$base_dir/header.php", "w");
    fwrite($fpw, $content);
    fclose($fpw);

    $codepage = "<h1>" . $objevent->getName() . "</h2>\n";
    $codepage.= "<p>" . $objevent->getRemarks() . "</p>\n";
    $filename = "CopyFiles/about.php";
    $content = file_get_contents($filename);
    $content = str_replace("%codepage%", $codepage, $content);
    $fpw = fopen("$base_dir/about.php", "w");
    fwrite($fpw, $content);
    fclose($fpw);

    $filename = "CopyFiles/applicant_login.php";
    $content = file_get_contents($filename);
    $content = str_replace("%tablename%", "applicant_$event", $content);
    $fpw = fopen("$base_dir/applicant_login.php", "w");
    fwrite($fpw, $content);
    fclose($fpw);

    $filename = "CopyFiles/password.php";
    $content = file_get_contents($filename);
    $content = str_replace("%tablename%", "applicant_$event", $content);
    $fpw = fopen("$base_dir/password.php", "w");
    fwrite($fpw, $content);
    fclose($fpw);

    $filename = "CopyFiles/admin_index.php";
    $content = file_get_contents($filename);
    $content = str_replace("%tablename%", "applicant_$event", $content);
    $fpw = fopen("$base_dir/admin/index.php", "w");
    fwrite($fpw, $content);
    fclose($fpw);

    $filename = "CopyFiles/portal_index.php";
    $content = file_get_contents($filename);
    $content = str_replace("%tablename%", "applicant_$event", $content);
    $content = str_replace("%pagename%", "", $content);
    $content = str_replace("%event_description%", $objevent->getRemarks(), $content);
    $content = str_replace("%event_name%", $objevent->getName(), $content);
    $fpw = fopen("$base_dir/index.php", "w");
    fwrite($fpw, $content);
    fclose($fpw);

    $table_name = array("applicant_$event", "report_$event");
    $sql_queries = array();

    $sql_queries[] = "CREATE TABLE `report_$event` ( `id` int(11) NOT NULL AUTO_INCREMENT, `query` text NOT NULL, `no_of_cols` int(11) NOT NULL, `order` int(11) NOT NULL DEFAULT '1', PRIMARY KEY(`id`) ) ENGINE=INNODB";

    $sql_queries[] = "CREATE  TABLE `applicant_$event` (
\t`id` INT NOT NULL AUTO_INCREMENT ,
\t`username` VARCHAR(100) NOT NULL ,
\t`password` VARCHAR(100) NOT NULL ,
\t`status` VARCHAR(45) NOT NULL DEFAULT 'registered' ,
\t`cr_time` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
\tPRIMARY KEY (`id`) ,
\tUNIQUE INDEX `username_UNIQUE` (`username` ASC) 
\t) ENGINE = InnoDB;\n";

    $query = "SELECT * FROM `pages` WHERE `event` = '$event' ORDER BY `order`";
    $db->query($query);
    $pages = $db->last_result;

    $filename = "CopyFiles/portal_index.php";
    $content = file_get_contents($filename);
    $content = str_replace("%tablename%", "applicant_$event", $content);
    $content = str_replace("%pagename%", "PAGE_" . strtoupper(str_replace(" ", "_", $pages[0]->name)) . ".php", $content);
    $content = str_replace("%event_description%", $objevent->getRemarks(), $content);
    $content = str_replace("%event_name%", $objevent->getName(), $content);
    $fpw = fopen("$base_dir/index.php", "w");
    fwrite($fpw, $content);
    fclose($fpw);

    foreach ($pages as $row) {
        $table_name[$row->id] = "tab_$event" . "_page_$row->id";
    }
    $datatype = array('Date' => 'DATETIME', 'String' => 'varchar', 'Integer' => 'bigint', 'Decimal' => 'NUMERIC(18,2)');

    foreach ($pages as $page) {
        $query = "SELECT * FROM fields where `page` = '$page->id'";
        $db->query($query);
        $str = "CREATE TABLE " . $table_name[$page->id] . "\n\t( `id` INT NOT NULL AUTO_INCREMENT ,\n";
        $str.= "\t`applicant` INT(11) NOT NULL ,\n";
        foreach ($db->last_result as $row) {
            //print_r($row);
            $str.="\t`$row->name` ";
            if ($row->type == "String" && $row->max_length > 500) {
                $str.=" TEXT";
            } else {
                $str.=$datatype[$row->type];
                if ($row->type != "Decimal" && $row->type != "Date") {
                    if ($row->max_length != "" && $row->max_length != "0") {
                        $str.="($row->max_length)";
                    }
                }
            }
            if ($row->mandatory == "Yes") {
                $str.=" NOT NULL";
            }
            $str.=" ,\n";
        }

        $str.="\tPRIMARY KEY(`id`)) ENGINE = InnoDB ;\n";
        $sql_queries[] = $str;
    }

    foreach ($sql_queries as $query) {
        $db->query($query);
    }

    foreach ($table_name as $table) {
        $clsCode = getGenericClassCode($table);
        $filename = $base_dir . "/Classes/" . strtoupper($table) . ".php";
        $fp = fopen($filename, "w");
        fwrite($fp, "<?php \n");
        fwrite($fp, $clsCode);
        fwrite($fp, "?> \n");
        fclose($fp);
    }

    foreach ($pages as $page) {
        if ($page->max_records == 1) {
            $pageCode = getSinglePageGenericPageCode("TAB_$event" . "_PAGE_" . $page->id, $page->id);
        } else {
            $pageCode = getGenericPageCode("TAB_$event" . "_PAGE_" . $page->id, $page->id);
        }
        $filename = $base_dir . "/PAGE_" . strtoupper(str_replace("/", "_", str_replace(" ", "_", $page->name))) . ".php";
        $fp = fopen($filename, "w");
        fwrite($fp, $pageCode);
        fclose($fp);
    }

//generating the submitapplication.pdf file
    $min_reqt_check_code = "";
    foreach ($pages as $page) {
        $min_reqt_check_code.="\$query = \"SELECT count(*) as 'count' FROM `tab_$event" . "_page_$page->id` WHERE `applicant` = '\$applicant' \"; \n";
        $min_reqt_check_code.="\$db->query(\$query);\n";
        $min_reqt_check_code.="if(\$db->last_result[0]->count < $page->min_records){ \$status_code = FALSE; \$url_links[] = 'PAGE_" . strtoupper(str_replace("/", "_", str_replace(" ", "_", $page->name))) . ".php'; }\n";
    }
    $filename = "CopyFiles/submitapplication.php";
    $content = file_get_contents($filename);
    $content = str_replace("%tablename%", "applicant_$event", $content);
    $content = str_replace("//%codepage%", $min_reqt_check_code, $content);
    $fpw = fopen("$base_dir/submitapplication.php", "w");
    fwrite($fpw, $content);
    fclose($fpw);

//Generating the code for the pdf of the application
    $pdfcode = "";
    foreach ($pages as $page) {

        $pdfcode.= "set_font_size('h3');\n";
        $pdfcode.= "\$fpdf->Write(\$lh3,'" . str_replace("'", "\'", $page->name) . "');\n";
        $pdfcode.= "set_font_size('h3');\n";

        $query = "SELECT * FROM `fields` WHERE `page` = '$page->id' ORDER BY `order`";
        $db->query($query);
        $cols = $db->last_result;
        $selectcols = "";
        foreach ($cols as $column) {

            if ($column->type == "Date") {
                $selectcols.=" DATE_FORMAT(`$column->name`,'%d %M, %Y') as '$column->name' , ";
            } else {
                $selectcols.=" `$column->name` , ";
            }
        }
        $selectcols = substr($selectcols, 0, strlen($selectcols) - 2);
        if ($selectcols == "") {
            $selectcols = " * ";
        }
        $pdfcode.= "\n\$fpdf->Cell(190, \$lh4, '', 0, 1, 'C');\n";
        $pdfcode.= "\$query = \"SELECT $selectcols FROM `tab_$event" . "_page_$page->id` WHERE `applicant` = '\$applicant'\"; \n";
        $pdfcode.= "\$db->query(\$query);\n";
        $pdfcode.= "\n";
        $pdfcode.= "\n";
        $pdfcode.= "\$data = array();";
        $pdfcode.= "\n";
        if ($page->max_records == 1) {


            $pdfcode.= "\$result = \$db->last_result[0];\n";
            $sno = 0;
            $addarray = FALSE;

            $textcols = array();
            foreach ($cols as $column) {

                if ($column->max_length > 500) {
                    $textcols[] = $column;
                } else {

                    if ($sno == 0) {
                        if ($addarray) {
                            $pdfcode.= "\$data[] = \$dataline;\n";
                            $addarray = TRUE;
                        }
                        $pdfcode.= "\$dataline = array('','','','');\n";
                    }

                    $pdfcode.= "\$dataline[$sno] = '" . str_replace("'", "\'", $column->display_name) . "';\n";
                    $sno++;
                    $pdfcode.= "\$dataline[$sno] = \$result->$column->name;\n";
                    $sno++;

                    $sno = $sno % 4;
                    $addarray = TRUE;
                }
            }


            if ($addarray) {
                $pdfcode.= "\$data[] = \$dataline;\n";
                $addarray = TRUE;
            }
            $pdfcode.= "PDFprintdata(\$data);\n";

            foreach ($textcols as $column) {
                $pdfcode.="\nset_font_size(\"h5\");\n";
                $pdfcode.="\$fpdf->Write(\$lh3,\"" . str_replace("\"", "\\\"", $column->display_name) . " :\");\n";
                $pdfcode.="\$fpdf->ln();\n";
                $pdfcode.="set_font_size();\n";
                $pdfcode.="\$fpdf->Write(\$lh3,\".\$result->" . $column->name . ".\");\n";
                $pdfcode.="\$fpdf->ln();\n";
            }
        } else {

            $pdfcode.= "\$results = \$db->last_result;\n";
            $pdfcode.= "\$datarow = array();\n";
            foreach ($cols as $column) {
                $pdfcode.= "\$datarow[] = '" . str_replace("'", "\'", $column->display_name) . "';\n";
            }
            $pdfcode.= "\$data[] = \$datarow;\n";
            $pdfcode.= "\n";
            $pdfcode.= "foreach(\$results as \$row) {\n";
            $pdfcode.= "\t\$data[] = \$row;\n";
            $pdfcode.= "}\n";
            $pdfcode.= "PDFprintTable(\$data);\n";
        }
    }
    $filename = "CopyFiles/createPDF.php";
    $content = file_get_contents($filename);
    $content = str_replace("%tablename%", "applicant_$event", $content);
    $content = str_replace("//%codepage%", $pdfcode, $content);
    $fpw = fopen("$base_dir/createPDF.php", "w");
    fwrite($fpw, $content);
    fclose($fpw);

//Generating the page for admin to download the applications
    $pagecode = "\$query = \"SELECT * FROM `applicant_$event` WHERE `status` = 'submited'\";
\$db->query(\$query);
\$applicants = \$db->last_result;\n";

    $htmlcode = "";

    $pagesno = 0;
    foreach ($pages as $page) {

        $pagesno++;

        $pagecode.="\n\n";
        $htmlcode.="echo \"<h2>Summary for $page->name </h2>\";\n";
        $pagecode.="\n";
        $pagecode.="\$data$pagesno = array();\n";
        $pagecode.="\$datarow = array('#','Username');\n";
        $pagecode.="\n";

        $query = "SELECT * FROM `fields` WHERE `page` = '$page->id' ";
        $db->query($query);
        $columns = $db->last_result;
        $selectfields = "";
        foreach ($columns as $column) {
            $pagecode.="\$datarow[] = \"$column->display_name\";\n";
            $selectfields.= "t.`$column->name` , ";
        }
        $selectfields = substr($selectfields, 0, strlen($selectfields) - 2);
        $pagecode.="\$data$pagesno" . "[] = \$datarow;\n";
        $pagecode.="\$sno = 1;\n";
        $pagecode.="foreach(\$applicants as \$applicant){\n";
        $pagecode.="\$data$pagesno" . "[] = array('',\$applicant->username);\n";
        $pagecode.="\$query = \"SELECT $selectfields FROM `tab_$event" . "_page_$page->id` as t 
          WHERE `applicant` = '\$applicant->id' \";\n";
        $pagecode.="\$db->query(\$query);\n";

        $pagecode.="\$results = \$db->last_result;\n";

        $pagecode.="foreach(\$results as \$row){\n";

        $pagecode.="\$datarow = array(\$sno,'');\n";
        $pagecode.="foreach(\$row as \$cell){\n";
        $pagecode.="\$datarow[] = \$cell;\n";
        $pagecode.="}\n";

        $pagecode.="\$sno++;\n";
        $pagecode.="\$data$pagesno" . "[] = \$datarow;\n";
        $pagecode.="}\n";

        $pagecode.="}\n";

        $pagecode.="\n";
        $htmlcode.="drawHTMLTABLE(\$data$pagesno);\n";
    }
    $pagecode.="if(isset(\$_POST['button1'])) {\n";
    $pagecode.="\theader('Content-Type: application/csv');
\theader('Content-Disposition: attachment; filename=\"Report.csv\"');
\techo ArrayToCSV(\$data);\n";

    for ($i = 1; $i < $pagesno; $i++) {
        $pagecode.="\techo \"\\n\";\n";
        $pagecode.="\techo ArrayToCSV(\$data$i);\n";
    }
    $pagecode.="die();";
    $pagecode.="}\n";


    $filename = "CopyFiles/files.php";
    $content = file_get_contents($filename);
    $content = str_replace("%tablename%", "applicant_$event", $content);
    $content = str_replace("//%codepage%", $pagecode, $content);
    $content = str_replace("//%codepage2%", $htmlcode, $content);
    $fpw = fopen("$base_dir/admin/files.php", "w");
    fwrite($fpw, $content);
    fclose($fpw);

//creating the create reporting page for the portal
    $pagecontent = "";
    foreach ($pages as $row) {
        $pagecontent.="<option value=\"tab_$event" . "_page_$row->id" . "\">$row->name</option>\n";
    }
    $filename = "CopyFiles/createreporting.php";
    $content = file_get_contents($filename);
    $content = str_replace("/*ReportingTable*/", "report_$event", $content);
    $content = str_replace("<!-- Table Names -->", "$pagecontent", $content);
    $fpw = fopen("$base_dir/admin/createreporting.php", "w");
    fwrite($fpw, $content);
    fclose($fpw);

//creating the create reporting page for the portal to view reports
    $filename = "CopyFiles/report.php";
    $content = file_get_contents($filename);
    $content = str_replace("/*ReportingTable*/", "report_$event", $content);
    $content = str_replace("/*ApplicantTable*/", "applicant_$event", $content);
    $content = str_replace("/*scruitningtable*/", "scruitning_$event", $content);
    $fpw = fopen("$base_dir/admin/report.php", "w");
    fwrite($fpw, $content);
    fclose($fpw);

//creating the install script
    $fp = fopen("$base_dir/install.php", "w");
    fwrite($fp, "<?php\n");
    fwrite($fp, "require_once 'functions.php';\n");
    fwrite($fp, "\$db=new MySQLDataBase();\n");
    fwrite($fp, "\$error = array();\n");
    foreach ($sql_queries as $query) {

        fwrite($fp, "\$query = \"$query\";\n");
        fwrite($fp, "\$db->query(\$query);\n");
        fwrite($fp, "\$error[] = \$db->last_error;\n");
        fwrite($fp, "\n");
    }

    $str = "
require_once 'header.php';
?>
<table>
  <thead>
    <tr>
      <th>HOST</th>
      <th>USERNAME</th>
      <th>PASSWORD</th>
      <th>D-BASE</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><?= DB_HOST ?></td>
      <td><?= DB_USER ?></td>
      <td><?= DB_PASSWORD ?></td>
      <td><?= DB_NAME ?></td>
    </tr>
  </tbody>
</table>
<h1>Installation Report</h1>
<ul>
<?php
\$sno=1;
foreach(\$error as \$err){
  if(\$err!=\"\"){
  echo \"<li>\$err</li>\n\";
  }else{  echo \"<li>STEP \$sno Completed successfully.</li>\n\"; }
  \$sno++;
}
?>
</ul>
<?php
require_once 'footer.php';
?>  
";
    fwrite($fp, $str);
    fclose($fp);


//Deleting the temporary tables created
    foreach ($table_name as $deltable) {

        $query = "DROP TABLE `$deltable`";
        $db->query($query);
    }
    ?>

    <?php
    print_r($table_name);
    print_r($sql_queries);
    ?>
</pre>
<input type="hidden" name="downloadportal" value="downloadportal" id ="hdndownloadportal"/>
<?php if (!isset($_POST['downloadportal'])): ?>
    <script type="text/javascript">
        $(document).ready(function(){
            $('#frmForm1').submit();
        });
    </script>
<?php endif; ?>
<?php
require_once 'footer.php';
?>
