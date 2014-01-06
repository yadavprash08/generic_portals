<?php
if (isset($_POST['button1'])) {
    $file = fopen('Classes/DEFINE_PARAM.php', 'w');
    if ($file) {
	fwrite($file, "<?php \n");
        fwrite($file, "define(\"DB_HOST\", \"".$_POST['textbox1']."\");\n");
        fwrite($file, "define(\"DB_USER\", \"".$_POST['textbox2']."\");\n");
        fwrite($file, "define(\"DB_PASSWORD\", \"".$_POST['textbox3']."\");\n");
        fwrite($file, "define(\"DB_NAME\", \"".$_POST['textbox4']."\");\n");
        fwrite($file, "define(\"ADMIN_PASSWD\", \"".$_POST['textbox5']."\");\n");
        fwrite($file, "\n?>");
        fclose($file);
        ?>
        <script type="text/javascript">
            alert('Process completed sucessfully. We will be redirecting you very soon to the home page');
        </script>
        <?php
    } else {
        ?>
        <script type="text/javascript">
            alert('Unable to create file. Please check the write permission for the application in your webserver.');
        </script>
        <?php
    }

    die('');
}
?>

<html>
    <head>
        <title>Welcome to the Generic Portal</title>
        <link href="site.css" media="all" type="text/css" rel="stylesheet"/>
        <link type="text/css" href="css/redmond/jquery-ui-1.8.18.custom.css" rel="stylesheet" />	
        <script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="js/jquery-ui-1.8.18.custom.min.js"></script>

        <script type="text/javascript">
            $(document).ready(function(){
                $('.page').hide(); 
                $('.page').slideDown(2000, '');
            });
            
            function validateForm(){
            
                var status = true;
                var reqd_elmnts = ['textbox1','textbox2','textbox3','textbox4'];
                var i = 0;
                for(i=0;i<reqd_elmnts.length;i++){
                    var elmnt = document.getElementById(reqd_elmnts[i]);
                    if(elmnt.value==""){
                        status = false;
                    }
                }
            
                return status;
            }
        </script>
    </head>

    <body>
        <form action="" method="post">
            <div class="page" style="height: 570px;padding: 20px;">
                <h1 style="margin-bottom: 100px;">Creating the Information File</h1>
                <div class="inputform" style="margin: 20px;">
                    <fieldset>
                        <legend>Portal Information</legend>

                        <label for="textbox1">Database Host<span style="color: red;">*</span></label>
                        <input type="text" name="textbox1" id="textbox1" value=""/>

                        <label for="textbox2">Database User<span style="color: red;">*</span></label>
                        <input type="text" name="textbox2" id="textbox2" value=""/>

                        <label for="textbox3">Database Password<span style="color: red;">*</span></label>
                        <input type="password" name="textbox3" id="textbox3" value=""/>

                        <label for="textbox4">Database Name<span style="color: red;">*</span></label>
                        <input type="text" name="textbox4" id="textbox4" value=""/>

                        <label for="textbox5">Administrator Password<span style="color: red;">*</span></label>
                        <input type="password" name="textbox5" id="textbox5" value=""/>
                    </fieldset>

                    <input class="submitButton" type="submit" value="Add" name="button1" id="button1" style="text-align:center;margin-top: 50px;" onclick="if(!validateForm()){alert('Please fill all the mandatory fields first.');return false;}else{alert('status complete');}" />

                    <span class="navigation_plain">
                        <input class="submitButton" type="submit" value="Cancel" id="button1" style="margin-top: 50px; text-align: center;float: right;" onclick="window.location = '';return false;" />
                    </span>
                </div>
            </div>
        </form>
    </body>
</html>
