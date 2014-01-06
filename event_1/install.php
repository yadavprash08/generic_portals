<?php
require_once 'functions.php';
$db=new MySQLDataBase();
$error = array();
$query = "CREATE TABLE `report_1` ( `id` int(11) NOT NULL AUTO_INCREMENT, `query` text NOT NULL, `no_of_cols` int(11) NOT NULL, `order` int(11) NOT NULL DEFAULT '1', PRIMARY KEY(`id`) ) ENGINE=INNODB";
$db->query($query);
$error[] = $db->last_error;

$query = "CREATE  TABLE `applicant_1` (
	`id` INT NOT NULL AUTO_INCREMENT ,
	`username` VARCHAR(100) NOT NULL ,
	`password` VARCHAR(100) NOT NULL ,
	`status` VARCHAR(45) NOT NULL DEFAULT 'registered' ,
	`cr_time` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
	PRIMARY KEY (`id`) ,
	UNIQUE INDEX `username_UNIQUE` (`username` ASC) 
	) ENGINE = InnoDB;
";
$db->query($query);
$error[] = $db->last_error;

$query = "CREATE TABLE tab_1_page_1
	( `id` INT NOT NULL AUTO_INCREMENT ,
	`applicant` INT(11) NOT NULL ,
	`first_name` varchar(20) NOT NULL ,
	`middle_name` varchar(20) ,
	`last_name` varchar(20) NOT NULL ,
	`date_of_birth` DATETIME NOT NULL ,
	PRIMARY KEY(`id`)) ENGINE = InnoDB ;
";
$db->query($query);
$error[] = $db->last_error;


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
$sno=1;
foreach($error as $err){
  if($err!=""){
  echo "<li>$err</li>
";
  }else{  echo "<li>STEP $sno Completed successfully.</li>
"; }
  $sno++;
}
?>
</ul>
<?php
require_once 'footer.php';
?>  
