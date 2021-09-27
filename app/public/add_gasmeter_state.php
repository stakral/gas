<?php

require_once("includes/initialize.php");

if (!$session->is_logged_in()) {
	redirect_to("login");
}

$odpocty  = new Odpocty($db);
$user 	  = new User($db);


if (isset($_POST['submit'])) {

	$raw_gas_meter_value = trim($_POST['hodnota_odpočtu']);
	if (isset($raw_gas_meter_value)) {
		$gas_meter_value = $raw_gas_meter_value;
		//$last_gas_meter_state = LAST_OFFICIAL_READING_VALUE;
		//$season = SEASON; // Set custom season using config.php.
		$season	= current_season(); // Current season based on current date.
		//$consumption	= $last_gas_meter_state + $gas_meter_value;
	}

	if (isset($_POST['datum_odpočtu']) && !empty($_POST['datum_odpočtu'])) {
		//$reading_date = trim($_POST['datum_odpočtu']);
		//$originalDate = "2010-03-21";
		$originalDate = trim($_POST['datum_odpočtu']);
		$reading_date = date("Y-m-d", strtotime($originalDate));
	} else {
		$reading_date = "";
	}

	$new_record = new stdClass();

	$new_record->reading = $gas_meter_value;
	$new_record->date    = $reading_date;
	$new_record->season  = $season;
	//print_r($new_record); die();


	/* Inputs Data Validation */

	// Presence validation (required fields).
	$required_fields = array("hodnota_odpočtu", "datum_odpočtu");
	validate_presences($required_fields);

	// Numeric imput type validation.
	$numeric_field_names = array("hodnota_odpočtu");
	$regex = '/^([,|.]?[0-9])+$/';
	$error_message = "Hodnota odpočtu musí byť číslo väčšie ako nula.";
	validate_positive_numeric_input_value($numeric_field_names, $regex, $error_message);

	// Max. input value length validation.
	$fields_with_max_lengths = array("hodnota_odpočtu" => 10);
	validate_max_float_lengths($fields_with_max_lengths);


	if (empty($errors)) {
		if ($odpocty->save($new_record)) {
			log_action($current_otk_initials . " : Nový odpočet: " . $next_id . "/" . date('y'));
			log_trimmer();

			$session->message("Nový záznam o odpočte plynomera bol uložený do databázy.");
			redirect_to("home");
		} else {
			// Failed
			$message = "Nový záznam o odpočte plynomera nebol uložený do databázy.";
		}
	}
	//print_r($new_record); die();
	//print_r($errors); die();
}

if ($message == "") {
	$message = "Zadajte údaje o odpočte.";
}
?>




<p class="top-message-bar"><?php echo $message ?></p>

<div class="forms">
	<div class="all-forms">

		<h3 style="text-align: center">Registrácia odpočtu plynomera</h3>

		<form autocomplete="off" name="myForm" action="add-gasmeter-state" method="post">

			<div class="form-container">

				<div class="input-rows-container">
					<div class="input-row">
						<div class="label-container">
							<label>Hodnota odpočtu:</label>
						</div>
						<div class="input-container">
							<input type="text" name="hodnota_odpočtu" value="<?php if (isset($gas_meter_value)) {
																					echo gas_meter_value_checker($gas_meter_value);
																				} ?>"><br>
							<?php echo formated_div_error($errors, "hodnota_odpočtu"); ?>
						</div>
					</div>
					<div class="input-row">
						<div class="label-container">
							<label>Dátum odpočtu:</label>
						</div>
						<div class="input-container">
							<input type="text" id="datepicker" name="datum_odpočtu" value="<?php if (isset($reading_date)) {
																								echo $reading_date;
																							} ?>"><br>
							<?php echo formated_div_error($errors, "datum_odpočtu"); ?>
						</div>
					</div>
				</div>
				<div class="input-row">
					<div class="label-container">&nbsp;</div>
					<div class="input-container butons-section">
						<label> </label>
						<input type="submit" class="button-ok" name="submit" value="Uložiť">
						<a href="home" class="btn-borderless">Zrušiť</a><br><br>
					</div>
				</div>

			</div>

		</form>

	</div>
</div>


<?php include('layouts/footer.php'); ?>