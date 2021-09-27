<?php
require_once("includes/initialize.php");
// require_once("../../includes/initialize.php");

if ($session->is_logged_in()) {
	redirect_to("home");
}

// Remember to give your form's submit tag a name="submit" attribute!
if (isset($_POST['submit'])) {	//echo "PITCHA"; die();
	// Form has been submitted.

	if (isset($_POST['login'])) {
		$username = $_POST['login'];
	}
	if (isset($_POST['heslo'])) {
		$password = $_POST['heslo'];
	}

	$user = new User($db);

	validate_password_db_vs_input($field_name = 'heslo', $user, $username, $password);

	$required_fields = array("login", "heslo"); // input field names, NOT the db columns names!
	validate_presences($required_fields);

	// Check database to see if username/password exist.
	// $found_user = User::authenticate($username, $existing_hash);

	$found_user = attempt_login($user, $username, $password); // HERE ALL THE HASHING TAKES PLACE
	//print_r($found_user); die();
	if (empty($errors)) {

		if ($found_user) {
			// Success
			// Stores user's data to session and marks user as logged in.
			$session->login($found_user);

			$current_otk_initials = $user->current_otk_initials(); // Output in format "SK 23".
			log_action($current_otk_initials . " : Prihlásenie.");
			log_trimmer();
			redirect_to("home");
		}
	} else {
		// username/password combo was not found in the database
		$message = "Username/password combination incorrect.";
	}
} else { // Form has not been submitted.
	$username = "";
	$password = "";
}

?>
<html>

<head>
	<title>Protokoly OOP - Prihlásenie</title>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
</head>


<body class="forms">

	<div class="all-forms">
		<h3 style="text-align: center">Evidencia spotreby zemného plynu</h3>
		<form autocomplete="off" action="" method="post">
			<div class="form-container">
				<fieldset>
					<legend>Prihlásenie do aplikácie</legend>
					<div class="input-rows-container">
						<div class="input-row">
							<div class="label-container">
								<label>Login:</label>
							</div>
							<div class="input-container">
								<input type="text" name="login" value="<?php echo htmlentities($username); ?>">
								<?php echo formated_div_error($errors, "login"); ?>
							</div>
						</div>
						<div class="input-row">
							<div class="label-container">
								<label>Heslo:</label>
							</div>
							<div class="input-container">
								<input type="password" name="heslo" value="<?php if (isset($_POST['heslo'])) {
																				echo htmlentities($password);
																			} ?>" />
								<?php echo formated_div_error($errors, "heslo"); ?>
							</div>
						</div>
					</div>
				</fieldset>
				<div class="butons-section">
					<input type="submit" class="button-ok" name="submit" value="Vstúpiť">
				</div>
			</div>
		</form>

	</div>

	<script src="<?php echo BASE_URL . "/"; // I added this PHP snipped as links like "http://localhost/gas/app/user-edit/10" didn't work 
					?>bundled.js"></script>
</body>