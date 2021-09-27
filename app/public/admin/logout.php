<?php require_once("includes/initialize.php"); ?>
<?php// require_once("../../includes/initialize.php"); ?>
<?php	
/*    
	$session->logout();
    redirect_to("login.php");
*/
?>

<?php
    $user = new User($db);
    $current_otk_initials=$user->current_otk_initials(); // Output in format "SK 23".
// My logout with log action
    $session->logout();
    log_action( $current_otk_initials." : Odhlásenie.");
    log_trimmer();
//    echo $current_user->full_name();
    redirect_to("login");

?>
