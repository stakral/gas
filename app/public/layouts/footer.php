<script src="<?php echo BASE_URL . "/"; // I added this PHP snipped as links like "http://localhost/gas/app/user-edit/10" didn't work 
                ?>bundled.js"></script>

<script type="text/javascript" src=<?php echo BASE_URL . "/public/javascripts/ajax_dd.js" ?>></script>
<script src=<?php echo BASE_URL . "/public/javascripts/word_count.js" ?>></script>

<script src=<?php echo BASE_URL . "/public/jquery/external/jquery/jquery.js" ?>></script>
<script src=<?php echo BASE_URL . "/public/jquery/jquery-ui.js" ?>></script>
<script src=<?php echo BASE_URL . "/public/jquery/datepicker.js" ?>></script>
<link rel="stylesheet" href=<?php echo BASE_URL . "/public/jquery/jquery-ui.css" ?>>

</body>
<footer>
    <div class="footer"><?php echo 'kingus &copy; ' . date("Y", time()); ?></div>
</footer>

</html>
<?php if (isset($db)) {
    $db->close_connection();
} ?>