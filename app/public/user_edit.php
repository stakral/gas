<?php require_once("includes/initialize.php"); ?>
<?php if (!$session->is_logged_in()) {
	redirect_to("login");
} ?>

<?php

$user = new User($db);

// The segment(2) function used below returns current URL id segment value (current item id).
$edited_user_id = segment(2);

$current_otk 	= $user->current_otk(); // print_r($current_otk ); die(); // Provides access to all users attributes
$edited_user 	= $user->find_by_id($edited_user_id);
$logged_in_user = $user->find_by_id($_SESSION['otk_id']);

if (!$edited_user) {
	redirect_to("users-manage");
}

if (isset($_POST['submit'])) {
	/*
	if (isset($_POST['meno'])){ 				$name 			= $_POST['meno'];}
	if (isset($_POST['priezvisko'])){ 			$surname 		= $_POST['priezvisko'];}
	if (isset($_POST['login'])){				$username 		= $_POST['login'];}
	if (isset($_POST['heslo'])){				$password 		= $_POST['heslo'];}
	if (isset($_POST['nové_heslo'])){			$passwordnew 	= $_POST['nové_heslo'];}
	if (isset($_POST['overenie_hesla'])){		$passwordconf 	= $_POST['overenie_hesla'];}
	if (isset($_POST['číslo_pečiatky'])){		$razitko 		= $_POST['číslo_pečiatky'];}
	if (isset($_POST['pracovisko'])){			$pracovisko 	= $_POST['pracovisko'];}


	$edited_user->id 			= (INT)$_GET['id'];
	$edited_user->name 			= $db->escape_value(trim($_POST["meno"]));
	$edited_user->surname 		= $db->escape_value(trim($_POST["priezvisko"]));
//	$edited_user->user_name 	= $db->escape_value(trim($_POST["login"])); // We can't update username as password is checked against it!
	$edited_user->razitko 		= $db->escape_value(trim($_POST["číslo_pečiatky"]));
	$edited_user->pracovisko 	= $db->escape_value(trim($_POST["pracovisko"]));

	if(isset($_POST["nové_heslo"]) && ($_POST["nové_heslo"]!=="")){
		$edited_user->hashed_password = password_encrypt($db->escape_value(trim($_POST["nové_heslo"])));	
	}else{
		$edited_user->hashed_password = password_encrypt($db->escape_value(trim($_POST["heslo"])));
	}
*/

	if (isset($_POST['meno'])) {
		$name 			= trim($_POST['meno']);
	}
	if (isset($_POST['priezvisko'])) {
		$surname 		= trim($_POST['priezvisko']);
	}
	if (isset($_POST['login'])) {
		$username 		= trim($_POST['login']);
	}
	if (isset($_POST['heslo'])) {
		$password 		= trim($_POST['heslo']);
	}
	if (isset($_POST['nové_heslo'])) {
		$passwordnew 	= trim($_POST['nové_heslo']);
	}
	if (isset($_POST['overenie_hesla'])) {
		$passwordconf 	= trim($_POST['overenie_hesla']);
	}
	if (isset($_POST['číslo_pečiatky'])) {
		$razitko 		= trim($_POST['číslo_pečiatky']);
	}
	if (isset($_POST['pracovisko'])) {
		$pracovisko 	= trim($_POST['pracovisko']);
	}


	$edited_user->id 			= $edited_user_id;
	$edited_user->name 			= filter_var($name, FILTER_SANITIZE_STRING);
	$edited_user->surname 		= filter_var($surname, FILTER_SANITIZE_STRING);
	//	$edited_user->user_name 	= $db->escape_value(trim($_POST["login"])); // We can't update username as password is checked against it!
	$edited_user->razitko 		= filter_var($razitko, FILTER_VALIDATE_INT);
	$edited_user->pracovisko 	= filter_var($pracovisko, FILTER_SANITIZE_STRING);

	//print_r($edited_user); die();
	if (isset($_POST["nové_heslo"]) && ($_POST["nové_heslo"] !== "")) {
		$edited_user->hashed_password = password_encrypt($passwordnew);
	} else {
		$edited_user->hashed_password = password_encrypt($password);
	}


	// Validations - ORDER MATTERS!
	if (isset($_POST["nové_heslo"]) && ($_POST["nové_heslo"] !== "")) {
		validate_password_confirmation($pass1 = "nové_heslo", $pass2 = "overenie_hesla");
		validate_min_lengths($field = ["nové_heslo" => 7]);
		validate_max_lengths($field = ["nové_heslo" => 20]);
	}

	$username = trim($edited_user->user_name);
	$password = trim($_POST['heslo']);
	validate_password_db_vs_input($field_name = 'heslo', $user, $username, $password);

	$required_fields = array("meno", "priezvisko", /*"login",*/ "číslo_pečiatky", "heslo",/* "overenie_hesla",*/  "pracovisko"); // input field names, NOT the db columns names!
	validate_presences($required_fields);

	$fields_with_max_lengths = array("meno" => 15, "priezvisko" => 20, /*"login" => 15,*/ "číslo_pečiatky" => 2/*, "heslo"=>25*/);
	validate_max_lengths($fields_with_max_lengths);

	$fields_with_min_lengths = array("meno" => 3, "priezvisko" => 3, /*"login" => 5,*/ /*"heslo"=>7*/);
	validate_min_lengths($fields_with_min_lengths);

	$alphabetic_field_names = array("meno", "priezvisko");
	validate_alphabetic_input_type($alphabetic_field_names);

	$numeric_field_names = array("číslo_pečiatky");
	$regex = '/^([1-9]\d{0,1})$/';
	$error_message = "Zadajte číslo od 1 do 99";
	validate_numeric_input_type($numeric_field_names, $regex, $error_message);

	//	validate_numeric_input_type($peciatka="číslo_pečiatky");


	if (empty($errors)) {	// if its empty, then perform update
		$current_otk_initials = $user->current_otk_initials(); // Output in format "SK 23".
		log_action($current_otk_initials . " : Užívateľ \"{$edited_user->user_name}\" editovaný.");

		if ($user->save($edited_user)) {
			$session->message("Údaje užívateľa \"" . $user->full_name($edited_user) . "\" boli aktualizované.");
			redirect_to(go_to_URL_by_route('users-manage'));
		} else {
			// Failure
			$session->message("Nesprávne heslo! Údaje užívateľa \"" . $user->full_name($edited_user) . "\" neboli aktualizované.");
			redirect_to(go_to_URL_by_route('users-manage'));
		}
	}
}

?>


<?php // $layout_context="admin"; 
?>

<p class="top-message-bar"><?php if ($message == "") {
								echo "Upravte údaje podľa potreby. Zmeny uložte!";
							} else {
								echo $session->message();
							} ?></p>

<div class="forms">
	<div class="all-forms">

		<h3 style="text-align: center">Úprava údajov používateľa:<br /><?php /* echo htmlentities($admin["name"]); ?> <?php echo htmlentities($admin["surname"]); */ ?></h3>
		<form action="<?php echo go_to_URL_by_route('user-edit/' . $edited_user_id); ?>" method="post">
			<div class="form-container">
				<fieldset class="person">
					<legend>Osobné údaje:</legend>
					<div class="input-rows-container">
						<div class="input-row">
							<div class="label-container">
								<label>Meno:</label>
							</div>
							<div class="input-container">
								<input class="input-gap" type="text" name="meno" value="<?php echo htmlentities($edited_user->name); ?>">
								<?php echo formated_div_error($errors, "meno"); ?>
							</div>
						</div>
						<div class="input-row">
							<div class="label-container">
								<label>Priezvisko:</label>
							</div>
							<div class="input-container">
								<input class="input-gap" type="text" name="priezvisko" value="<?php echo htmlentities($edited_user->surname); ?>">
								<?php echo formated_div_error($errors, "priezvisko"); ?>
							</div>
						</div>
						<div class="input-row">
							<div class="label-container">
								<label>Login:</label>
							</div>
							<div class="input-container">
								<input class="input-gap" type="text" name="login" value="<?php echo htmlentities($edited_user->user_name); ?>" disabled>
								<?php // echo formated_div_error($errors, "login"); 
								?>
							</div>
						</div>
						<div class="input-row">
							<div class="label-container">
								<label>Číslo pečiatky:</label>
							</div>
							<div class="input-container">
								<input class="input-gap" type="text" name="číslo_pečiatky" value="<?php echo htmlentities($edited_user->razitko); ?>">
								<?php echo formated_div_error($errors, "číslo_pečiatky"); ?>
							</div>
						</div>
						<div class="input-row">
							<div class="label-container">
								<label>Heslo:</label>
							</div>
							<div class="input-container">
								<input class="input-gap" type="password" name="heslo" value="<?php if (isset($_POST['heslo'])) {
																									echo htmlentities($password);
																								} ?>">
								<?php echo formated_div_error($errors, "heslo"); ?>
							</div>
						</div>
					</div>
				</fieldset>
				<fieldset class="password">
					<legend>Zmena hesla:</legend>
					<div class="input-rows-container">
						<div class="input-row">
							<div class="label-container">
								<label>Nové heslo:</label>
							</div>
							<div class="input-container">
								<input class="input-gap" type="password" name="nové_heslo" value="<?php if (isset($_POST['nové_heslo'])) {
																										echo htmlentities($passwordnew);
																									} ?>">
								<?php echo formated_div_error($errors, "nové_heslo"); ?>
							</div>
						</div>
						<div class="input-row">
							<div class="label-container">
								<label>Overiť nové heslo:</label>
							</div>
							<div class="input-container">
								<input class="input-gap" type="password" name="overenie_hesla" value="<?php if (isset($_POST['overenie_hesla'])) {
																											echo htmlentities($passwordconf);
																										} ?>">
								<?php echo formated_div_error($errors, "overenie_hesla"); ?>
							</div>
						</div>
					</div>
				</fieldset>
				<fieldset class="workplace">
					<legend>Pracovisko:</legend>
					<div class="input-rows-container">
						<div class="input-row">
							<div class="label-container label-up">
								<label>Prevádzka:</label>
							</div>
							<div class="input-container">
								<label class="radio">
									<input name="pracovisko" type="radio" value="Volgogradská" <?php if ($edited_user->pracovisko == "Volgogradská") {
																									echo "checked";
																								} ?>>
									<span>Volgogradska</span>
								</label>
								<label class="radio">
									<input name="pracovisko" type="radio" value="Okrajová" <?php if ($edited_user->pracovisko == "Okrajová") {
																								echo "checked";
																							} ?>>
									<span>Okrajova</span>
								</label>
							</div>
						</div>
					</div>
				</fieldset>
				<!-- <label class="forms">&nbsp</label> -->
				<div class="butons-section right">
					<input type="submit" class="button-ok" name="submit" value="Uložiť zmeny">
					<a href="<?php echo go_to_URL_by_route('users-manage'); ?>" class="btn-borderless">Zrušiť</a>
				</div>
		</form>
	</div>
</div>


<br />
<?php
// $a=$found_user->hashed_password; $b=$_POST['heslo']; if(password_check($b, $a)){echo "yes";}else{echo"No";}
// echo $edited_user->hashed_password;
// echo form_errors($errors);
?>
</div>


<?php include('layouts/footer.php'); ?>