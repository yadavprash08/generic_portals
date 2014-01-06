<?php require_once 'functions.php'; ?>
<?php require_once 'header.php'; ?>

<?php if (!isset($db)) {
    $db = new MySQLDataBase();
} ?>
<script type="text/javascript">
    var count = 0;
    var scr_increment = 1;
    var scrolling = true;
    var max_count = 15;
    
    $(document).ready(function(){
        showResponses();
        runScrollBar(10);
    });
    
    function hideAnimation(){
        $('#img_scaning_system').hide();
    }
    
    function showResponses(){
        count++;
        $('#p_msg'+count).show();
        //scrlwidth = count*40;
        //$('#myscrlbar').css('width',scrlwidth+'px');
        if(count<max_count){
            setTimeout('showResponses()', 1000);
        }else{
            hideAnimation();
            scrolling = false;
        }
    }
    
    function runScrollBar(width){
        var total_time = ((max_count-2) * 1000)-1;
        var time_out = total_time/ 400;
        $('#myscrlbar').css('width',width+'px');
        width = width + scr_increment;
        if(width>=400 || width<= 0){ width = 400; }//scr_increment = scr_increment * -1;}
        if(scrolling){ 
            setTimeout('runScrollBar('+ width +')', time_out); 
        }else{
            //$('#myscrlbar').animate({margin:0, width: 400},1000,function(){}});
        }
    }
</script>
<style type="text/css">
    #div_scrollbar{
        text-align: center;
        margin-bottom: 10px;
    }

    #myscrlbar_container{
        margin: auto;
        width: 400px;
        border: solid #000 1px;
        padding: 0px;
    }

    #myscrlbar{
        background-color: #00a;
        width: 40px;
    }

    #div_installation_main{
        height: 400px;
        width: 98%;
        margin-right: 10px;
        padding: 10px;
        border:#220 solid 1px;
        overflow: auto;
    }

    #div_installation_main div{
        display: none;
    }

    #div_installation_main p.heading{

    }

    #div_installation_main span.error{
        color:#800;
    }

    #div_installation_main span.correct{
        color: #00bb00;
    }

    span.result{
        padding-left: 20px;
        float: right;
        vertical-align: bottom;
    }
</style>
<p><img id="img_scaning_system" src="images/working.gif" style="width: 15px; height: 15px;"/>Please be patient while we scan with the installation of the system...</p>
<div id="div_scrollbar">
    <div id="myscrlbar_container">
        <div id="myscrlbar">&nbsp;</div>
    </div>
</div>
<?php $cur_dir = getcwd(); ?>
<?php $falg = array(); ?>
<?php $define_file = TRUE; ?>
<div id="div_installation_main">
    <div id="p_msg1">
        <p class="heading">Checking the read permission for the Directory...
<?php if (is_readable($cur_dir)) { ?> <span class="result correct">Pass</span> <?php } else { ?> <span class="result error">Failed</span> 
    <?php $flag[] = "Please make the directory readable for user " . get_current_user();
} ?>
        </p>
    </div>

    <div id="p_msg2">
        <p class="heading">Checking the Write permission for the Directory...
<?php if (is_writable($cur_dir)) { ?> <span class="result correct">Pass</span> <?php } else { ?> <span class="result error">Failed</span>
    <?php $flag[] = "Please make the directory readable for user " . get_current_user();
} ?>
        </p>
    </div>

    <div id="p_msg3">
        <p class="heading">

        </p>
    </div>

    <?php if (count($flag) > 0) { ?> 
        <script type="text/javascript">
            max_count = 3;
        </script>
            <?php } ?>

    <div id="p_msg4">
        <p class="heading">Looking the Setting File...
<?php if (file_exists('Classes/DEFINE_PARAM.php')) { ?> <span class="result correct">Pass</span> <?php } else { ?> <span class="result error">Failed</span> <?php $define_file = FALSE;
} ?>
        </p>
    </div>
<?php if ($define_file == TRUE): ?>
        <div id="p_msg5">
            <p class="heading">Is Database Name defined...
                <?php if (DB_NAME) { ?> <span class="result correct">Pass</span> <?php } else { ?> <span class="result error">Failed</span> <?php } ?>
            </p>
        </div>

        <div id="p_msg6">
            <p class="heading">Checking Connection with the database...
                <?php $db_status = false;
                $db_check = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
                if ($db_check) {
                    $db_status = TRUE;
                    mysql_close($db_check); ?> <span class="result correct">Pass</span> <?php } else { ?> <span class="result error">Failed</span> <?php } ?>
            </p>
        </div>

        <div id="p_msg7">
            <p class="heading">Checking to select the database...
        <?php $db->query("SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = '" . DB_NAME . "'");
        if ($db_status && $db->num_rows == 1) { ?> <span class="result correct">Pass</span> <?php } else { ?> <span class="result error">Failed</span> <?php $db_status = FALSE;
    } ?>
            </p>
        </div>

        <div id="p_msg8">
            <p class="heading">Initializing for database creation...
            </p>
        </div>
<?php endif; ?>
    <!-- No Read Write Execute Permission Block -->
    <?php if (count($flag) != 0): ?>
        <script type="text/javascript">
            $(document).ready(function(){
                setTimeout(alert('During the pre installation check one of the following mendatory conditions were found to be missing. Please do ensure that the User :: <?= get_current_user() ?> must hold the read, write and execute permission to the Directory <?= $cur_dir ?>.'),2000);
            });
        </script>
        <?php else: ?>
            <?php if (!$db_status): ?>
            <script type="text/javascript">
                max_count = 7;
            </script>
            <?php else: ?>
            <div id="p_msg9">
                <p>Creating the Event Table</p>
        <?php $query = "CREATE TABLE IF NOT EXISTS `events` (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `name` varchar(250) NOT NULL,
                            `start` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                            `end` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
                            `remarks` text,
                            `inactive` int(11) NOT NULL DEFAULT '0',
                            PRIMARY KEY (`id`)
                          ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='this table stores the information of the variour events with' AUTO_INCREMENT=1 ;"; ?>
                <?php $db->query($query); ?>
            </div>

            <div id="p_msg10">
                <p>Creating the Page Table</p>
                <?php $query = "CREATE TABLE IF NOT EXISTS `pages` (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `event` int(11) NOT NULL,
                            `name` varchar(45) NOT NULL,
                            `order` int(11) NOT NULL DEFAULT '0',
                            `min_records` int(11) NOT NULL DEFAULT '0',
                            `max_records` int(11) NOT NULL DEFAULT '5',
                            `remarks` text,
                            PRIMARY KEY (`id`),
                            KEY `fk_pages_event` (`event`)
                          ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;"; ?>
                <?php $db->query($query); ?>
            </div>

            <div id="p_msg11">
                <p>Creating the Field Table</p>
                <?php $query = "CREATE TABLE IF NOT EXISTS `fields` (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `page` int(11) NOT NULL,
                            `name` varchar(145) NOT NULL,
                            `display_name` varchar(145) NOT NULL,
                            `max_length` int(11) NOT NULL,
                            `type` varchar(45) NOT NULL,
                            `order` int(11) NOT NULL DEFAULT '0',
                            `remarks` text,
                            `mandatory` varchar(45) NOT NULL DEFAULT 'No',
                            PRIMARY KEY (`id`),
                            KEY `fk_fields_page` (`page`)
                          ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;"; ?>
                <?php $db->query($query); ?>
            </div>

            <div id="p_msg12">
                <p>Creating the Field Option Table</p>
        <?php $query = "CREATE TABLE IF NOT EXISTS `fieldoptions` (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `field` int(11) NOT NULL,
                            `option_name` varchar(45) NOT NULL,
                            `option_value` varchar(45) NOT NULL,
                            PRIMARY KEY (`id`),
                            KEY `fk_fieldoptions_field` (`field`)
                          ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;"; ?>
                <?php $db->query($query); ?>
            </div>

            <div id="p_msg13">
                <p>Adding the Constraint on the Page Table</p>
        <?php $query = "ALTER TABLE `pages`
                            ADD CONSTRAINT `fk_pages_event` FOREIGN KEY (`event`) REFERENCES `events` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;"; ?>
                <?php $db->query($query); ?>
            </div>

            <div id="p_msg14">
                <p>Adding the Constraint on the Field Table</p>
            <?php $query = "ALTER TABLE `fields`
                            ADD CONSTRAINT `fk_fields_page` FOREIGN KEY (`page`) REFERENCES `pages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;"; ?>
            <?php $db->query($query); ?>
            </div>

            <div id="p_msg15">
                <p>Adding the Constraint on the Field Option Table</p>
        <?php $query = "ALTER TABLE `fieldoptions`
                            ADD CONSTRAINT `fk_fieldoptions_field` FOREIGN KEY (`field`) REFERENCES `fields` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;"; ?>
        <?php $db->query($query); ?>
            </div>

    <?php endif; ?>
<?php endif; ?>
</div>
<?php require_once 'footer.php'; ?>