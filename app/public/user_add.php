<?php require_once("includes/initialize.php"); ?>
<?php if (!$session->is_logged_in()) {
	redirect_to("admin/login.php");
} ?>

<?php

if (isset($_POST['submit'])) {
	//if (($_SERVER['REQUEST_METHOD'] =='POST') && (!empty($_POST['action']))):

	$user 	  = new User($db);
	$add_user = new stdClass();

	if (isset($_POST['meno'])) {
		$name 					= $_POST['meno'];
	}
	if (isset($_POST['priezvisko'])) {
		$surname 			= $_POST['priezvisko'];
	}
	if (isset($_POST['login'])) {
		$username 				= $_POST['login'];
	}
	if (isset($_POST['heslo'])) {
		$password 				= $_POST['heslo'];
	}
	if (isset($_POST['overenie_hesla'])) {
		$passwordconf 	= $_POST['overenie_hesla'];
	}
	if (isset($_POST['číslo_pečiatky'])) {
		$razitko 		= $_POST['číslo_pečiatky'];
	}
	if (isset($_POST['pracovisko'])) {
		$pracovisko 		= $_POST['pracovisko'];
	}

	$add_user->name 			= trim($name);
	$add_user->surname 			= trim($surname);
	$add_user->user_name 		= trim($username);
	$add_user->hashed_password 	= trim(password_encrypt($password));
	$add_user->razitko 			= (int)($razitko);
	$add_user->pracovisko 		= trim($pracovisko);

	// validations
	$required_fields = array("meno", "priezvisko", "login", "číslo_pečiatky", "heslo", "overenie_hesla",  "pracovisko"); // input field names, NOT the db columns names!
	validate_presences($required_fields);

	$fields_with_max_lengths = array("meno" => 15, "priezvisko" => 30, "login" => 15, "číslo_pečiatky" => 2, "heslo" => 25); // password will be hashed (30)
	validate_max_lengths($fields_with_max_lengths);

	$fields_with_min_lengths = array("meno" => 3, "priezvisko" => 3, "login" => 5, "heslo" => 7); // password will be hashed (30)
	validate_min_lengths($fields_with_min_lengths);

	$alphabetic_field_names = array("meno", "priezvisko");
	validate_alphabetic_input_type($alphabetic_field_names);

	validate_password_confirmation($pass1 = "heslo", $pass2 = "overenie_hesla");

	$numeric_field_names = array("číslo_pečiatky");
	$regex = '/^([1-9]\d{0,1})$/';
	$error_message = "Zadajte číslo od 1 do 99";
	validate_numeric_input_type($numeric_field_names, $regex, $error_message);

	// Setting variables for "if" statement to trigger final procedures.
	$err_name = 0;
	$err_surname = 0;
	$err_username = 0;
	$err_passlength = 0;
	$err_razitko = 0;
	$err_passconf = 0;

	if ($name === '') :
		$error_name = '<div class="error">Meno je povinný údaj</div>';
	else :
		$err_name = 1; // All these err_* variables are here for us just to be able to make the condition for "if" statement with $sum_of_errors below.
	endif;

	if ($surname === '') :
		$error_surname = '<div class="error">Priezvisko je povinný údaj</div>';
	else :
		$err_surname = 1;
	endif;

	if ($username === '') :
		$error_username = '<div class="error">Užíateľské meno je povinný údaj</div>';
	else :
		$err_username = 1;
	endif;

	if ($razitko === '') :
		$error_razitko = '<div class="error">Čislo pečiatky je povinný údaj</div>';
	elseif (!(preg_match('/^([1-9]?\d|99)$/', $razitko))) : // To check that input pattern matches we use regular expressions
		$error_patternmatch = '<div class="error">Zadajte číslo od 1 do 99</div>';
	else :
		$err_razitko = 1;
	endif;

	// To check if the field has certain number of characters we can use PHP strlen() method.

	if (strlen($password) <= 6) :
		$error_passlength = '<div class="error">Heslo musí obsahovať aspoň 7 znakov</div>';
	else :
		$err_passlength = 1;
	endif;

	// Tocheck if the passwords are mathing we can simply compare the with each-other.

	if ($passwordconf === '') :
		$error_passconf = '<div class="error">Overenie hesla je povinné</div>';
	elseif ($passwordconf !== $password) :
		$error_passconf = '<div class="error">Hesla sa nezhoduju</div>';
	else :
		$err_passconf = 1;
	endif;

	$sum_of_errors = $err_name + $err_surname + $err_username + $err_passlength + $err_razitko + $err_passconf;

	// To check that input pattern matches we use regular expressions

	// if (!(preg_match('/[A-Za-z]+, [A-Za-z]+/', $name))):
	// $error_patternmatch = '<div class="error">Sorry, the name must be in the format: Last, First</div>';
	// endif;

	// if ($sum_of_errors==6){
	if (empty($errors)) {
		$current_otk_initials = $user->current_otk_initials(); // Output in format "SK 23".
		log_action($current_otk_initials . " : Užívateľ \"{$add_user->user_name}\" vytvorený.");


		if ($user->save($add_user)) {
			// Success - i.e. redirect_to ("somepage.php");
			$session->message("Nový užívateľský účet \"" . $user->full_name($add_user) . "\" bol úspešne vytvorený.");
			redirect_to("users-manage");
		} else {
			// Failure
			$session->message("Nový užívateľský účet nebol vytvorený!");
			redirect_to("user-add");
		}
	}
}

?>
<?php //if (isset($connection)) {mysqli_close($connection); } 
?>


<p class="top-message-bar">Zadajte údaje nového užívateľa. Zmeny uložte!</p>

<?php // $layout_context="admin"; 
?>

<div class="forms">
	<div class="all-forms">

		<h3 style="text-align: center">Nový užívateľ</h3>

		<form autocomplete="off" action="user-add" method="post">
			<div class="form-container">
				<div class="input-rows-container margin-up">
					<div class="input-row">
						<div class="label-container">
							<label>Meno:</label>
						</div>
						<div class="input-container">
							<input type="text" name="meno" value="<?php if (isset($name)) {
																		echo $name;
																	} ?>" />
							<?php echo formated_div_error($errors, "meno"); // presence, min and max validation 
							?>
						</div>
					</div>
					<div class="input-row">
						<div class="label-container">
							<label>Priezvisko:</label>
						</div>
						<div class="input-container">
							<input type="text" name="priezvisko" value="<?php if (isset($surname)) {
																			echo $surname;
																		} ?>" />
							<?php echo formated_div_error($errors, "priezvisko"); ?>
						</div>
					</div>
					<div class="input-row">
						<div class="label-container">
							<label>Login:</label>
						</div>
						<div class="input-container">
							<input type="text" name="login" value="<?php if (isset($username)) {
																		echo $username;
																	} ?>" />
							<?php echo formated_div_error($errors, "login"); ?>
						</div>
					</div>
					<div class="input-row">
						<div class="label-container">
							<label>Číslo pečiatky:</label>
						</div>
						<div class="input-container">
							<input type="text" name="číslo_pečiatky" value="<?php if (isset($razitko)) {
																				echo $razitko;
																			} ?>" />
							<?php echo formated_div_error($errors, "číslo_pečiatky"); ?>
						</div>
					</div>
					<div class="input-row">

						<div class="label-container">
							<label>Heslo:</label>
						</div>
						<div class="input-container">
							<input type="password" name="heslo" value="<?php if (isset($password)) {
																			echo $password;
																		} ?>" />
							<?php echo formated_div_error($errors, "heslo"); ?>
						</div>
					</div>
					<div class="input-row">
						<div class="label-container">
							<label>Overiť heslo:</label>
						</div>
						<div class="input-container">
							<input type="password" name="overenie_hesla" value="<?php if (isset($passwordconf)) {
																					echo $passwordconf;
																				} ?>" /><br />
							<?php echo formated_div_error($errors, "overenie_hesla");  // Password confirmation and presence validation in one method  
							?>
						</div>
					</div>
					<div class="input-row top-gap">
						<div class="label-container">
							<label>Prevádzka:</label>
						</div>
						<div class="input-container">
							<label class="radio">
								<input name="pracovisko" type="radio" value="Volgogradská" checked="checked">
								<span>Volgogradska</span>
							</label>
							<label class="radio">
								<input name="pracovisko" type="radio" value="Okrajová">
								<span>Okrajova</span>
							</label>
						</div>
					</div>
				</div>

				<div class="input-row">
					<div class="label-container">&nbsp;</div>
					<div class="input-container butons-section">
						<input type="submit" class="button-ok" name="submit" value="Uložiť">
						<a href="<?php echo go_to_URL_by_route('users-manage'); ?>" class="btn-borderless">Zrušiť</a><br /><br />
					</div>
				</div>
			</div>
		</form>
		<?php
		// echo form_errors($errors); // Displays errors formated in unordered list
		// print_r($errors); // Displais just array of errors to check if necessary
		?>
	</div>
</div>

<?php include('layouts/footer.php'); ?>