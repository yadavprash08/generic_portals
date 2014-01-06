<?php
    if(ini_get('short_open_tag')!= 1){
        echo "Please configure the short_open_tag option and set it's value to On before running this application.";
        die();
    }
?>
<?php
if(!file_exists('Classes/DEFINE_PARAM.php')){
    header('Location: createDefinitions.php');
    die();
}
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
      $file_data.=str_replace(",", "", $cell) . ",";
    }
    $file_data.="\n";
  }

  return $file_data;
}

function getGenericClassCode($table_name) {
  global $db;
  $_db = $db;
  $fields = array();
  $primary_keys = array();
  $datetimecols = array();
  $pkey_count = 0;
  $primary_key_cond = "";

  $tab = "";
  $code = "class " . strtoupper($table_name) . "\t{\n";
  $tab = "\t";

  $code.= $tab . "private \$_db;\n";
  $code.= $tab . "private \$_query_type = \"insert\";\n";
  $code.= $tab . "private \$_update_columns;\n";
  $code.= $tab . "private \$_filter = array();\n";
  $code.= $tab . "private";
  $query = "SHOW FULL COLUMNS IN $table_name";
  $_db->query($query);


  $cols = $db->last_result;
  foreach ($_db->last_result as $row) {
    if ($row->Type == "timestamp"||$row->Type == "datetime") {
      $datetimecols[$row->Field] = TRUE;
    } else {
      $datetimecols[$row->Field] = FALSE;
    }
    $code.=" \$_" . $row->Field . " ,";
    $fields[] = $row->Field;
    if ($row->Key == "PRI") {
      $primary_keys[] = $row->Field;
      $pkey_count++;
      $primary_key_cond .=" `$row->Field` = '\$this->_$row->Field',";
    }
  }
  $primary_key_cond = substr($primary_key_cond, 0, strlen($primary_key_cond) - 1);
  if ($primary_key_cond == "") {
    $primary_key_cond = "1";
  }
  $code = substr($code, 0, strlen($code) - 1) . ";\n\n";
//$code.=$tab."private \$_primary_condition = \"$primary_key_cond\";\n";
  $inpvar = "";
  for ($i = 0; $i < $pkey_count; $i++) {
    $inpvar.= "\$" . $primary_keys[$i] . " ,";
  }
  $inpvar = substr($inpvar, 0, strlen($inpvar) - 1);

  $code.=$tab . "public function __construct($inpvar,\$db=NULL) {\n";
  $tab = "\t\t";

  $code.=$tab . "if(\$db==NULL)\t{\n";
  $code.=$tab . "\t\$this->_db = new MySQLDataBase();\n";
  $code.=$tab . "}else\t{\n";
  $code.=$tab . "\t\$this->_db = \$db;\n";
  $code.=$tab . "}\n";
  $code.=$tab . "\$this->loadData($inpvar);\n";
  $tab = "\t";
  $code.=$tab . "}\n\n";

  $code.=$tab . "private function loadData($inpvar) {\n";
  $tab = "\t\t";
  $select_fields = "";
  foreach ($cols as $col) {
    if ($datetimecols[$col->Field]) {
      $select_fields.="DATE_FORMAT(`$col->Field`,'%d/%m/%Y') as '$col->Field' , ";
    } else {
      $select_fields.= "`" . $col->Field . "` , ";
    }
  }
  $select_fields = substr($select_fields, 0, strlen($select_fields) - 2);
  $code.=$tab . "\$query = \"SELECT $select_fields FROM `$table_name` ";
  if ($pkey_count) {
    $code.=" WHERE `" . $primary_keys[0] . "` = '\$$primary_keys[0]'";
    for ($i = 1; $i < $pkey_count; $i++) {
      $code.=" AND `" . $primary_keys[$i] . "` = '\$$primary_keys[$i]'";
    }
  }
  $code.="\";\n";
  $code.=$tab . "\$this->_db->query(\$query);\n";
  $code.=$tab . "if(\$this->_db->num_rows==1)\t{\n";
  $tab.="\t";
  $code.=$tab . "\$row = \$this->_db->last_result[0];\n\n";
  foreach ($fields as $column) {
    $code.=$tab . "\$this->_$column = \$row->$column;\n";
  }
  $code.="\t\t\t\$this->_query_type = \"update\";\n";
  $code.="\t\t}\n";

  $tab = "\t";
  $code.=$tab . "}\n";

  $code.="\n" . $tab . "public function filter()\t{\n";
  $tab.="\t";
  $code.=$tab . "\$query = \"SELECT ";
  if (count($primary_keys)) {
    $code.=" `" . $primary_keys[0] . "` ";
    for ($i = 1; $i < $pkey_count; $i++) {
      $code.=", `" . $primary_keys[$i] . "` ";
    }
  } else {
    $code.= " * ";
  }
  $code.=" FROM `$table_name` WHERE \";\n";
  $code.=$tab . "\$keys = array_keys(\$this->_filter);\n";
  $code.=$tab . "if(count(\$keys)){\n";
  $code.=$tab . "\t\$query.=\"`\$keys[0]` = '\". str_replace(\"'\", \"''\", \$this->_filter[\$keys[0]]) .\"'\";\n";
  $code.=$tab . "\tfor(\$i=1;\$i<count(\$keys);\$i++){\n";
  $code.=$tab . "\t\t\$query.=\" AND `\$keys[\$i]` = '\". str_replace(\"'\", \"''\", \$this->_filter[\$keys[\$i]]) .\"'\";";
  $code.=$tab . "\t\t\n";
  $code.=$tab . "\t}\n";
  $code.=$tab . "}else{\n";
  $code.=$tab . "\t\$query.= \"1\";\n";
  $code.=$tab . "}\n";
  $code.=$tab . "\$this->_db->query(\$query);\n";
  $code.=$tab . "\$return = array();\n";
  $code.=$tab . "if(\$this->_db->num_rows){\n";
  $code.=$tab . "\tforeach(\$this->_db->last_result as \$row){\n";
  $code.=$tab . "\t\t\$row_arr = (array)\$row;\n";
  $code.=$tab . "\t\t\$return[] = \$row_arr;\n";
  $code.=$tab . "\t}\n";
  $code.=$tab . "}\n";
  $code.=$tab . "return \$return;\n";
  $tab = "\t";
  $code.=$tab . "}\n";

  $code.="\n" . $tab . "public function clearFilter()\t{\n";
  $code.=$tab . "\t\$this->_filter = array();\n";
  $code.=$tab . "}\n";

  foreach ($fields as $column) {
    $code.="\n" . $tab . "public function get" . ucwords($column) . "()\t{\n";
    $code.=$tab . "\treturn \$this->_$column;\n";
    $code.=$tab . "}\n";

    $code.="\n" . $tab . "public function set" . ucwords($column) . "(\$value)\t{\n";
    $code.=$tab . "\t\$this->_$column = \$value;\n";
    if ($datetimecols[$column]) {
      $code.=$tab . "\t\$this->_update_columns['$column'] = \"STR_TO_DATE('\$value','%d/%m/%Y')\";\n";
    } else {
      $code.=$tab . "\t\$this->_update_columns['$column'] = \"\$value\";\n";
    }
    $code.=$tab . "}\n";

    $code.="\n" . $tab . "public function filter" . ucwords($column) . "(\$value)\t{\n";
    $code.=$tab . "\t\$this->_filter['$column'] = \"\$value\";\n";
    $code.=$tab . "}\n";
  }

  $code.="\n" . $tab . "public function saveChanges()\t{\n";
  $tab.="\t";
  $code.=$tab . "\$keys = array_keys(\$this->_update_columns);" . "\n";
  $code.=$tab . "if (\$this->_query_type == \"insert\")\t{" . "\n";
  $tab.="\t";
  $code.=$tab . "\$cols = \"\";" . "\n";
  $code.=$tab . "foreach (\$keys as \$col)\t{" . "\n";
  $code.=$tab . "\t\$cols.=\"`\$col` ,\";" . "\n";
  $code.=$tab . "}" . "\n";
  $code.=$tab . "\$cols = substr(\$cols, 0, strlen(\$cols) - 1);" . "\n";
  $code.=$tab . "\$values = \"\";" . "\n";
  $code.=$tab . "foreach (\$this->_update_columns as \$indx => \$value) {" . "\n";
  $code.=$tab . "\tif(";
  $contains_datetime = FALSE;
  foreach ($datetimecols as $key => $value) {
    if ($value) {
      $code.="\$indx=='$key' || ";
      $contains_datetime = TRUE;
    }
  }
  if (!$contains_datetime) {
    $code.="FALSE";
  } else {
    $code = substr($code, 0, strlen($code) - 3);
  }
  $code.=")\t{\n";
  $code.=$tab . "\t\t\$values.=\" \".\$value .\",\";" . "\n";
  $code.=$tab . "\t}else\t{\n";
  $code.=$tab . "\t\t\$values.=\" '\" . str_replace(\"'\", \"''\", \$value) . \"',\";" . "\n";
  $code.=$tab . "\t}\n";
  $code.=$tab . "}" . "\n";
  $code.=$tab . "\$values = substr(\$values, 0, strlen(\$values) - 1);" . "\n";
  $code.=$tab . "\$query = \"INSERT INTO `$table_name` (\$cols) VALUES (\$values)\";" . "\n";

  $tab = "\t\t";
  $code.=$tab . "}else\t{" . "\n";
  $tab.="\t";
  $code.=$tab . "\$query = \"UPDATE `$table_name` SET \";" . "\n";
  $code.=$tab . "\$cols = \"\";" . "\n";
  $code.=$tab . "foreach(\$keys as \$col){" . "\n";
  $code.=$tab . "\tif(\$this->_update_columns[\$col]==''){" . "\n";
  $code.=$tab . "\t\t\$cols.= \" `\$col` = NULL,\";" . "\n";
  $code.=$tab . "\t}else\t{" . "\n";
  $code.=$tab . "\t\tif(";
  $contains_datetime = FALSE;
  foreach ($datetimecols as $key => $value) {
    if ($value) {
      $code.="\$col=='$key' || ";
      $contains_datetime = TRUE;
    }
  }
  if (!$contains_datetime) {
    $code.="FALSE";
  } else {
    $code = substr($code, 0, strlen($code) - 3);
  }
  $code.=")\t{\n";
  $code.=$tab . "\t\t\t\$cols.= \" `\$col` = \".\$this->_update_columns[\$col].\",\";" . "\n";
  $code.=$tab . "\t\t}else\t{\n";
  $code.=$tab . "\t\t\t\$cols.= \" `\$col` = '\".str_replace(\"'\", \"''\", \$this->_update_columns[\$col]) .\"',\";" . "\n";
  $code.=$tab . "\t\t}\n";
  $code.=$tab . "\t}" . "\n";
  $code.=$tab . "}" . "\n";
  $code.=$tab . "\$cols = substr(\$cols, 0,  strlen(\$cols)-1);" . "\n";
  $code.=$tab . "\$query .= \$cols;" . "\n";
  $code.=$tab . "\$query .= \" WHERE \";" . "\n";
  $code.=$tab . "\$query.=\"$primary_key_cond\";" . "\n";

  $tab = "\t\t";
  $code.=$tab . "}" . "\n";

  $tab = "\t\t";
  $code.=$tab . "\$this->_db->query(\$query);" . "\n";
  $code.=$tab . "if(\$this->_db->last_error!=\"\"){" . "\n";
  $code.=$tab . "\treturn false;" . "\n";
  $code.=$tab . "}" . "\n";
  $code.=$tab . "\$this->_query_type = \"update\";" . "\n";
  $code.=$tab . "return true;" . "\n";
  $code.=$tab . "}\n";

  $code.="}\n";
  return $code;
}

function addTab(&$tab) {
  $tab.="\t";
}

function removeTab(&$tab) {
  $tab = substr($tab, 1);
}

function addComment($comment, &$code) {
  $code.="\n//$comment\n";
}

function addHTMLComment($comment, &$code) {
  $code.="\n<!--$comment -->\n";
}

function addLine($line, &$code, $tab) {
  $code.=$tab . $line . "\n";
}

function getGenericPageCode($tableName, $page_id) {
  $tableName = strtolower($tableName);
  global $db;
  global $dbname;

  $tab = "";
  $code = "";

  $query = "select * from `pages` WHERE `id` = '$page_id' ";
  $db->query($query);
  $page_rec = $db->last_result[0];

  $query = "SELECT * FROM `pages` WHERE `event` = '$page_rec->event' AND `order` > '$page_rec->order' ORDER BY `order`";
  $db->query($query);
  $nextpage = "submitapplication.php";
  if ($db->num_rows > 0) {
    $nextpage = "PAGE_" . strtoupper(str_replace("/","_",str_replace(" ", "_", $db->last_result[0]->name))) . ".php";
  }

  $previouspage = "index.php";
  $query = "SELECT * FROM `pages` WHERE `event` = '$page_rec->event' AND `order` < '$page_rec->order' ORDER BY `order` DESC";
  $db->query($query);
  if ($db->num_rows > 0) {
    $previouspage = "PAGE_" . strtoupper(str_replace("/","_",str_replace(" ", "_", $db->last_result[0]->name))) . ".php";
  }

  echo "<pre>";
  echo "PAGE INFO \n\n";
  print_r($page_rec);
  echo "</pre>";

  $query = "SELECT * FROM `fields` WHERE `page` = '$page_id' ORDER BY `order`;";
  $db->query($query);
  $cols = $db->last_result;

  $primary_key = array();
  $dateTime = array();

//house Keeping task

  addLine("<?php", $code, $tab);
  addTab($tab);
  addLine("/**", $code, $tab);
  addTab($tab);
  addLine("Table Name : $tableName", $code, $tab);
  addLine("Author : Prashant Yadav & Sakshi Agarwal", $code, $tab);
  removeTab($tab);
  addLine("**/", $code, $tab);
  addComment("Comment Over", $code);
  removeTab($tab);

//Adding the require lines
  addComment("Adding the require lines", $code);
  addLine("require_once 'functions.php';", $code, $tab);
  $class_name = strtoupper($tableName);
  addLine("require_once 'Classes/$class_name.php';", $code, $tab);
  addLine("", $code, $tab);
  addLine("if(!\$db)\t{", $code, $tab);
  addTab($tab);
  addLine("\$db = new MySQLDataBase();", $code, $tab);
  removeTab($tab);
  addLine("}", $code, $tab);

  addLine("if(!isset(\$_SESSION)){session_start();}", $code, $tab);
  addLine("", $code, $tab);
  addLine("if(\$_SESSION['applicant']==\"\"){header('Location: signinerr.php');}", $code, $tab);
  addLine("\$applicant = \$_SESSION['applicant'];", $code, $tab);
  addLine("", $code, $tab);

//generating the postback scrip
  addComment("Generating the postback script", $code);
  addLine("if(isset(\$_POST['button1'])){", $code, $tab);
  addTab($tab);

  addLine("\$MAX_RECORD = $page_rec->max_records;", $code, $tab);
  addLine("\$count_records = 0;", $code, $tab);
  addLine("\$query = \"SELECT count(*) as 'count' FROM $tableName WHERE `applicant` = '\$applicant'\";", $code, $tab);
  addLine("\$db->query(\$query);", $code, $tab);
  addLine("\$count_records = \$db->last_result[0]->count;", $code, $tab);

  addLine("\$id = \"\";", $code, $tab);
  addLine("\$id = \$_POST['lblPrimary1'];", $code, $tab);
  $line = "\$objtable = new $class_name(\$id, \$db);";
  addLine($line, $code, $tab);
  addLine("", $code, $tab);
  addLine("\$objtable->setApplicant(\$applicant);", $code, $tab);

  $sno = 1;
  foreach ($cols as $column) {
    if ($column->name != "id" && $column->name != "applicant") {
      addLine("\$objtable->set" . ucwords($column->name) . "(\$_POST['textbox$sno']);", $code, $tab);
      $sno++;
    }
  }

  addLine("", $code, $tab);
  addLine("if(\$id==\"\") {", $code, $tab);
  addLine("\tif(\$count_records < \$MAX_RECORD) {", $code, $tab);
  addLine("\t\t\$objtable->saveChanges();", $code, $tab);
  addLine("\t}", $code, $tab);
  addLine("}else {", $code, $tab);
  addLine("\t\$objtable->saveChanges();", $code, $tab);
  addLine("}", $code, $tab);

  addLine("", $code, $tab);
  removeTab($tab);
  addLine("}", $code, $tab);

  addLine("if(isset(\$_POST['button2'])){", $code, $tab);
  addTab($tab);

  addLine("\$query = \"DELETE FROM `$tableName` WHERE `id` = '\".\$_POST['lblPrimary1'].\"' \";", $code, $tab);
  addLine("\$db->query(\$query);", $code, $tab);

  removeTab($tab);
  addLine("}", $code, $tab);

  addComment("Importing the page header", $code);
  addLine("require_once 'header.php';", $code, $tab);
  addLine("?>", $code, $tab);
  addLine("<div>", $code, $tab);
  if($page_rec->remarks!=""){
    $code.=$page_rec->remarks;
  }
  addLine("</div>", $code, $tab);
  addLine("<script type=\"text/javascript\">", $code, $tab);

  $line = "function editRecord( var1 )\t{";
  addLine($line, $code, $tab);
  addTab($tab);

  addLine("$('#button2').show();",$code,$tab);
  addLine("$('#cancel').show();",$code,$tab);
  addLine("document.getElementById('lblPrimary1').value = var1 ;", $code, $tab);
  $sno = 1;
  foreach ($cols as $row) {
    if ($column->name != "id" && $column->name != "applicant") {
      addLine("", $code, $tab);
      addLine("dest = document.getElementById('textbox$sno');", $code, $tab);
      addLine("source = document.getElementById('label_'+ var1 + '_$sno');", $code, $tab);
      addLine("dest.value = source.innerHTML;", $code, $tab);
      $sno++;
    }
  }
  addLine("", $code, $tab);
  addLine("dest = document.getElementById('button1');", $code, $tab);
  addLine("dest.value = 'Update';", $code, $tab);
  addLine("return false;", $code, $tab);

  removetab($tab);
  addLine("}", $code, $tab);
  addLine("", $code, $tab);
  addLine("function validateForm(){", $code, $tab);
  addTab($tab);
  
  $sno = 1;
  $elementsname = "";
  foreach($cols as $column){
    if ($column->name != "id" && $column->name != "applicant") {
      if($column->mandatory == "Yes"){
        $elementsname.="'textbox$sno' , ";
      }
      $sno++;
    }
  }
  $elementsname = substr($elementsname,0,  strlen($elementsname)-2);
  
  addLine("var reqdelements = [$elementsname];",$code,$tab);
  addLine("var FormStatus = true;",$code,$tab);
  addLine("var errmsg = '';",$code,$tab);
  addLine("for(i=0;i<reqdelements.length;i++) {",$code,$tab);
  addTab($tab);
  
  addLine("var inp = document.getElementById(reqdelements[i]);",$code,$tab);
  addLine("if(inp && inp.value!=''){", $code, $tab);
  addLine("}else{", $code, $tab);
  addLine("\terrmsg = 'Required field is left blank';", $code, $tab);
  addLine("\tFormStatus = false;", $code, $tab);
  addLine("}", $code, $tab);
  addLine("", $code, $tab);
  removeTab($tab);
  addLine("}",$code,$tab);
  
  addLine("if(errmsg!='')",$code,$tab);
  addLine("\talert(errmsg);",$code,$tab);
  addLine("return FormStatus;",$code,$tab);
  
  removeTab($tab);
  addLine("}", $code, $tab);

  addLine("</script>", $code, $tab);

//creating the table
  addLine("<div class=\"\">", $code, $tab);
  addTab($tab);
  addLine("<table class=\"Datatable\">", $code, $tab);
  addTab($tab);
  addLine("<thead>", $code, $tab);
  addTab($tab);
  addLine("<tr>", $code, $tab);
  addTab($tab);
  addLine("<th>#</th>", $code, $tab);
  foreach ($cols as $column) {
    if ($column->name != "id" && $column->name != "applicant") {
      addLine("<th>$column->display_name</th>", $code, $tab);
    }
  }

  addLine("<th>EDIT</th>", $code, $tab);

  removeTab($tab);
  addLine("</tr>", $code, $tab);

  removeTab($tab);
  addLine("</thead>", $code, $tab);

  addLine("<tbody>", $code, $tab);
  addLine("<?php", $code, $tab);
  $select_field = "";
  foreach ($cols as $col) {
    if ($col->type == "Date") {
      $select_field.= "DATE_FORMAT(`$col->name`,'%d/%m/%Y') as '$col->name' , ";
    } else {
      $select_field.= "`$col->name` , ";
    }
  }
  addLine("\$query = \"SELECT $select_field `id` FROM `$tableName` WHERE `applicant` = '\$applicant'\"; ", $code, $tab);
  addLine("\$db->query(\$query); ", $code, $tab);
  addLine("\$results = \$db->last_result; ", $code, $tab);
  addLine("\$sno = 1; ", $code, $tab);
  addLine("foreach(\$results as \$row){", $code, $tab);

  addTab($tab);
  addLine("?>", $code, $tab);
  addLine("<tr>", $code, $tab);

  addTab($tab);
  addLine("<td><?= \$sno++ ?></td>", $code, $tab);

  $sno = 1;
  foreach ($cols as $column) {
    if ($column->name != "id" && $column->name != "applicant") {
      addLine("<td title=\"<?= \$row->$column->name ?>\">", $code, $tab);
      addLine("\t<label id=\"label_<?= \$row->id ?>_$sno\" ><?= \$row->$column->name ?></label>", $code, $tab);
      addLine("<?= showLimitedText(\$row->$column->name) ?>", $code, $tab);
      addLine("</td>", $code, $tab);
      $sno++;
    }
  }
  addLine("<td><a href=\"#\" onclick=\"return editRecord('<?= \$row->id ?>');\">Edit</a></td>", $code, $tab);

  removeTab($tab);
  addLine("</tr>", $code, $tab);

  removeTab($tab);
  addLine("<?php", $code, $tab);
  addLine("}", $code, $tab);
  addLine("?>", $code, $tab);
  addLine("</tbody>", $code, $tab);

  removeTab($tab);
  addLine("</table>", $code, $tab);

  removeTab($tab);
  addLine("</div>", $code, $tab);

//drawing the input type
  addLine("<div class=\"inputform\">", $code, $tab);
  addTab($tab);
  addLine("<input type=\"hidden\" name=\"lblPrimary1\" id=\"lblPrimary1\" />", $code, $tab);
  addLine("<fieldset>", $code, $tab);
  addTab($tab);
  addLine("<legend>Add/Edit</legend>", $code, $tab);

  $sno = 1;
  foreach ($cols as $column) {
    $query = "SELECT * FROM fieldoptions WHERE `field` = '$column->id' ";
    $db->query($query);

    if ($column->name != "id" && $column->name != "applicant") {

      //geting the column remarks
      $remarks = "";
      if($column->remarks!=""){
        $remarks = "<span class=\"remarks\">( $column->remarks )</span>";
      }
      
      $reqd = "";
      if ($column->mandatory == "Yes") {
        $reqd = "<span style=\"color:#f00;\">*</span>";
      }
      addLine("<label for=\"textbox$sno\">$column->display_name $reqd $remarks </label>", $code, $tab);

      if ($db->num_rows > 0) {
        $line = "<select name=\"textbox$sno\" id=\"textbox$sno\">";
        foreach ($db->last_result as $row) {
          $line.="\n<option value=\"$row->option_value\">$row->option_name</option>";
        }
        $line.="</select>";
      } elseif ($column->type == "String" && $column->max_length > 500) {
        $line = "<textarea name=\"textbox$sno\" id=\"textbox$sno\" value=\"\" maxlength=\"$column->max_length\"></textarea>";
      } else {

        $line = "<input type=\"text\" name=\"textbox$sno\" id=\"textbox$sno\" value=\"\" ";
        if ($column->max_length > 0) {
          $line.= " maxlength=\"$column->max_length\" ";
        }
        if ($column->type == "Integer") {
          $line.=" onkeyup=\"validateNumeric(this);\" ";
        }
        if ($column->type == "Date") {
          $line.=" onblur=\"validateDate(this);\" ";
        }
        if ($column->type == "Decimal") {
          $line.=" onblur=\"validateDecimal(this);\" ";
        }
        $line.= " />";
      }

      addLine($line, $code, $tab);
      addLine("<script type=\"text/javascript\">",$code,$tab);
      if($column->type == "Date"){
        addLine("\$(function(){\$('#textbox$sno').datepicker({changeMonth:true, changeYear:true, yearRange: \"c-20:c+20\"});});", $code, $tab);
      }
      addLine("</script>", $code, $tab);
      $sno++;
    }
  }

  removeTab($tab);
  addLine("</fieldset>", $code, $tab);
  addLine("<br/>", $code, $tab);
  addLine("<input type=\"submit\" name=\"button1\" id=\"button1\" value=\"Add\" onclick=\"return validateForm();\" />", $code, $tab);
  addLine("<a href=\"\" id=\"cancel\" style=\"display:none;\">Cancel</a>", $code, $tab);
  addLine("<input type=\"submit\" name=\"button2\" id=\"button2\" value=\"Remove Selected\" style=\"display:none;\" onclick=\"if(!confirm('Are you sure you want to remove the selected item.')){return false;}\">", $code, $tab);
  removeTab($tab);
  addLine("</div>", $code, $tab);
  addLine("<div class=\"navigation_plain\">", $code, $tab);
  $line="\t<a href=\"$nextpage\" class=\"forward\" ";
  if($nextpage=="submitapplication.php"){
      $line.="onclick=\"if(!confirm('You are about to submit the application.\\nOnce submitted, no changes to the application would be applowed.\\nIt is sujested to check your application before submission. Continue with submission of Application.')){return false;}\"";
  }
  $line.="> Next </a>";
  addLine($line, $code, $tab);
  addLine("\t<a href=\"$previouspage\" class=\"previous\" > Previous </a>", $code, $tab);
  addLine("\t<div style=\"clear:both;\"></div>", $code, $tab);
  addLine("</div>", $code, $tab);

  addLine("<?php", $code, $tab);
  addLine("require_once 'footer.php';", $code, $tab);
  addLine("?>", $code, $tab);
  return $code;
}

function getSinglePageGenericPageCode($tableName, $page_id) {
  $tableName = strtolower($tableName);
  global $db;
  global $dbname;

  $tab = "";
  $code = "";

  $query = "select * from `pages` WHERE `id` = '$page_id' ";
  $db->query($query);
  $page_rec = $db->last_result[0];

  $query = "SELECT * FROM `pages` WHERE `event` = '$page_rec->event' AND `order` > '$page_rec->order' ORDER BY `order`";
  $db->query($query);
  $nextpage = "submitapplication.php";
  if ($db->num_rows > 0) {
    $nextpage = "PAGE_" . strtoupper(str_replace("/","_",str_replace(" ", "_", $db->last_result[0]->name))) . ".php";
  }

  $previouspage = "index.php";
  $query = "SELECT * FROM `pages` WHERE `event` = '$page_rec->event' AND `order` < '$page_rec->order' ORDER BY `order` DESC";
  $db->query($query);
  if ($db->num_rows > 0) {
    $previouspage = "PAGE_" . strtoupper(str_replace("/","_",str_replace(" ", "_", $db->last_result[0]->name))) . ".php";
  }

  $query = "SELECT * FROM `fields` WHERE `page` = '$page_id' ORDER BY `order`;";
  $db->query($query);
  $cols = $db->last_result;

  $primary_key = array();
  $dateTime = array();

//house Keeping task
//Creating Javascript
  $javascriptref = "";
  $label_id = "";

  addLine("<?php", $code, $tab);
  addTab($tab);
  addLine("/**", $code, $tab);
  addTab($tab);
  addLine("Table Name : $tableName", $code, $tab);
  addLine("Author : Prashant Yadav & Sakshi Agarwal", $code, $tab);
  removeTab($tab);
  addLine("**/", $code, $tab);
  addComment("Comment Over", $code);
  removeTab($tab);

//Adding the require lines
  addComment("Adding the require lines", $code);
  addLine("require_once 'functions.php';", $code, $tab);
  $class_name = strtoupper($tableName);
  addLine("require_once 'Classes/$class_name.php';", $code, $tab);
  addLine("", $code, $tab);
  addLine("if(!\$db)\t{", $code, $tab);
  addTab($tab);
  addLine("\$db = new MySQLDataBase();", $code, $tab);
  removeTab($tab);
  addLine("}", $code, $tab);

  addLine("if(!isset(\$_SESSION)){session_start();}", $code, $tab);
  addLine("", $code, $tab);
  addLine("if(\$_SESSION['applicant']==\"\"){header('Location: signinerr.php');}", $code, $tab);
  addLine("\$applicant = \$_SESSION['applicant'];", $code, $tab);
  addLine("", $code, $tab);
//generating the postback scrip
  addComment("Generating the postback script", $code);
  addLine("if(isset(\$_POST['button1'])){", $code, $tab);
  addTab($tab);

  addLine("\$id = \"\";", $code, $tab);
  addLine("\$id = \$_POST['lblPrimary1'];", $code, $tab);
  $line = "\$objtable = new $class_name(\$id, \$db);";
  addLine($line, $code, $tab);
  addLine("", $code, $tab);
  addLine("\$objtable->setApplicant(\$applicant);", $code, $tab);

  $sno = 1;
  foreach ($cols as $column) {
    if ($column->name != "id" && $column->name != "applicant") {
      addLine("\$objtable->set" . ucwords($column->name) . "(\$_POST['textbox$sno']);", $code, $tab);
      $sno++;
    }
  }

  addLine("", $code, $tab);
  addLine("\$objtable->saveChanges();", $code, $tab);

  addLine("", $code, $tab);
  removeTab($tab);
  addLine("}", $code, $tab);

  addLine("if(isset(\$_POST['button2'])){", $code, $tab);
  addTab($tab);

  addLine("\$query = \"DELETE FROM `$tableName` WHERE `id` = '\".\$_POST['lblPrimary1'].\"' \";", $code, $tab);
  addLine("\$db->query(\$query);", $code, $tab);

  removeTab($tab);
  addLine("}", $code, $tab);

  addComment("Importing the page header", $code);
  addLine("require_once 'header.php';", $code, $tab);
  addLine("?>", $code, $tab);
  addLine("<div>", $code, $tab);
  if($page_rec->remarks!=""){
    $code.=$page_rec->remarks;
  }
  addLine("</div>", $code, $tab);
  addLine("<script type=\"text/javascript\">", $code, $tab);

  $line = "function editRecord( var1 )\t{";
  addLine($line, $code, $tab);
  addTab($tab);

  addLine("document.getElementById('lblPrimary1').value = var1 ;", $code, $tab);
  $sno = 1;
  foreach ($cols as $row) {
    if ($column->name != "id" && $column->name != "applicant") {
      addLine("", $code, $tab);
      addLine("dest = document.getElementById('textbox$sno');", $code, $tab);
      addLine("source = document.getElementById('label_'+ var1 + '_$sno');", $code, $tab);
      addLine("dest.value = source.innerHTML;", $code, $tab);
      $sno++;
    }
  }
  addLine("", $code, $tab);
  addLine("dest = document.getElementById('button1');", $code, $tab);
  addLine("dest.value = 'Update';", $code, $tab);
  addLine("return false;", $code, $tab);

  removetab($tab);
  addLine("}", $code, $tab);
  addLine("", $code, $tab);
  addLine("function validateForm(){", $code, $tab);
  addTab($tab);
  
  $sno = 1;
  $elementsname = "";
  foreach($cols as $column){
    if ($column->name != "id" && $column->name != "applicant") {
      if($column->mandatory == "Yes"){
        $elementsname.="'textbox$sno' , ";
      }
      $sno++;
    }
  }
  $elementsname = substr($elementsname,0,  strlen($elementsname)-2);
  
  addLine("var reqdelements = [$elementsname];",$code,$tab);
  addLine("var FormStatus = true;",$code,$tab);
  addLine("var errmsg = '';",$code,$tab);
  addLine("for(i=0;i<reqdelements.length;i++) {",$code,$tab);
  addTab($tab);
  
  addLine("var inp = document.getElementById(reqdelements[i]);",$code,$tab);
  addLine("if(inp && inp.value!=''){", $code, $tab);
  addLine("}else{", $code, $tab);
  addLine("\terrmsg = 'Required field is left blank';", $code, $tab);
  addLine("\tFormStatus = false;", $code, $tab);
  addLine("}", $code, $tab);
  addLine("", $code, $tab);
  removeTab($tab);
  addLine("}",$code,$tab);
  
  addLine("if(errmsg!='')",$code,$tab);
  addLine("\talert(errmsg);",$code,$tab);
  addLine("return FormStatus;",$code,$tab);
  
  removeTab($tab);
  addLine("}", $code, $tab);

  addLine("</script>", $code, $tab);

  addLine("<?php", $code, $tab);
  addTab($tab);
  addLine("\$query = \"SELECT `id` FROM `$tableName` WHERE `applicant` = '\$applicant' \";", $code, $tab);
  addLine("\$db->query(\$query);", $code, $tab);
  addLine("\$id = \"\";", $code, $tab);
  addLine("if(\$db->num_rows>0){", $code, $tab);
  addLine("\t\$id=\$db->last_result[0]->id;", $code, $tab);
  addLine("}", $code, $tab);
  addLine("\$objtable = new $class_name(\$id , \$db);", $code, $tab);
  addLine("", $code, $tab);
  removeTab($tab);
  addLine("?>", $code, $tab);

//drawing the input type
  addLine("<div class=\"inputform\">", $code, $tab);
  addTab($tab);
  addLine("<input type=\"hidden\" name=\"lblPrimary1\" id=\"lblPrimary1\" value=\"<?= \$objtable->getId() ?>\" />", $code, $tab);
  addLine("<fieldset>", $code, $tab);
  addTab($tab);
  addLine("<legend>Add/Edit</legend>", $code, $tab);

  $sno = 1;
  foreach ($cols as $column) {
    
    $query = "SELECT * FROM fieldoptions WHERE `field` = '$column->id' ";
    $db->query($query);

    if ($column->name != "id" && $column->name != "applicant") {

      //geting the column remarks
      $remarks = "";
      if($column->remarks!=""){
        $remarks = "<span class=\"remarks\">( $column->remarks )</span>";
      }
      
      $reqd = "";
      if ($column->mandatory == "Yes") {
        $reqd = "<span style=\"color:#f00;\">*</span>";
      }
      addLine("<label for=\"textbox$sno\">$column->display_name $reqd $remarks </label>", $code, $tab);

      if ($db->num_rows > 0) {
        
        $line = "<select name=\"textbox$sno\" id=\"textbox$sno\" value=\"<?= \$objtable->get" . ucwords($column->name) . "() ?>\">";
        foreach ($db->last_result as $row) {
          $line.="\n<option value=\"$row->option_value\">$row->option_name</option>";
        }
        $line.="</select>";
      } elseif ($column->type == "String" && $column->max_length > 500) {
        $line = "<textarea name=\"textbox$sno\" id=\"textbox$sno\" value=\"\" maxlength=\"$column->max_length\"><?= \$objtable->get" . ucwords($column->name) . "() ?></textarea>";
      } else {

        $line = "<input type=\"text\" name=\"textbox$sno\" id=\"textbox$sno\" value=\"<?= \$objtable->get" . ucwords($column->name) . "() ?>\" ";
        if ($column->max_length > 0) {
          $line.= " maxlength=\"$column->max_length\" ";
        }
        if ($column->type == "Integer") {
          $line.=" onkeyup=\"validateNumeric(this);\" ";
        }
        if ($column->type == "Date") {
          $line.=" onblur=\"validateDate(this);\" ";
        }
        if ($column->type == "Decimal") {
          $line.=" onblur=\"validateDecimal(this);\" ";
        }
        $line.= " />";
        
        
      }

      addLine($line, $code, $tab);
      addLine("<script type=\"text/javascript\">",$code,$tab);
      addLine("document.getElementById('textbox$sno').value = '<?= \$objtable->get" . ucwords($column->name) . "() ?>';", $code, $tab);
      if($column->type == "Date"){
        addLine("\$(function(){\$('#textbox$sno').datepicker({changeMonth:true, changeYear:true, yearRange: \"c-20:c+20\"});});", $code, $tab);
      }
      addLine("</script>", $code, $tab);
      $sno++;
    }
  }

  removeTab($tab);
  addLine("</fieldset>", $code, $tab);
  addLine("<br/>", $code, $tab);
  addLine("<input type=\"submit\" name=\"button1\" id=\"button1\" value=\"Save\" onclick=\"return validateForm();\" >", $code, $tab);
  //addLine("<a href=\"\">Cancel</a>", $code, $tab);
  addLine("<input type=\"submit\" name=\"button2\" id=\"button2\" value=\"Reset\" onclick=\"if(!confirm('Are you sure you want to reset all fields.')){return false;}\">", $code, $tab);
  removeTab($tab);
  addLine("</div>", $code, $tab);
  addLine("<div class=\"navigation_plain\">", $code, $tab);
  $line="\t<a href=\"$nextpage\" class=\"forward\" ";
  if($nextpage=="submitapplication.php"){
      $line.="onclick=\"if(!confirm('You are about to submit the application.\\nOnce submitted, no changes to the application would be applowed.\\nIt is sujested to check your application before submission. Continue with submission of Application.')){return false;}\"";
  }
  $line.="> Next </a>";
  addLine($line, $code, $tab);
  addLine("\t<a href=\"$previouspage\" class=\"previous\" > Previous </a>", $code, $tab);
  addLine("\t<div style=\"clear:both;\"></div>", $code, $tab);
  addLine("</div>", $code, $tab);

  addLine("<?php", $code, $tab);
  addLine("require_once 'footer.php';", $code, $tab);
  addLine("?>", $code, $tab);
  return $code;
}

?>
