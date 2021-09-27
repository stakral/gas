<?php require_once("includes/initialize.php"); ?>
<?php if (!$session->is_logged_in()) {
	redirect_to("login");
} ?>


<?php

$season    = new Season($db);
$user 	   = new User($db);


$current_otk_initials	= $user->current_otk_initials(); // Output in format "SK 23".
$current_otk 			= $user->current_otk(); // print_r($current_otk ); die(); // Provdphes access to all users attributes
$location 				= $current_otk->pracovisko;
$location_first_letter	= workplace_first_letter($location); // print_r($location_first_letter ); die();


if (isset($_POST['submit'])) {
	if (isset($_POST['season_name'])) {
		$season_name 		= trim($_POST['season_name']);
	}
	if (isset($_POST['start_date'])) {
		$start_date 		= trim($_POST['start_date']);
	}
	if (isset($_POST['start_value'])) {
		$start_value 		= trim($_POST['start_value']);
	}
	if (isset($_POST['volume_coeficient'])) {
		$volume_coeficient 	= trim($_POST['volume_coeficient']);
	}
	if (isset($_POST['avg_combust_heat'])) {
		$avg_combust_heat 	= trim($_POST['avg_combust_heat']);
	}
	if (isset($_POST['unit_price_kWh'])) {
		$unit_price_kWh 	= trim($_POST['unit_price_kWh']);
	}
	if (isset($_POST['distr_fee_kWh'])) {
		$distr_fee_kWh 		= trim($_POST['distr_fee_kWh']);
	}
	if (isset($_POST['transport_fee_kWh'])) {
		$transport_fee_kWh 	= trim($_POST['transport_fee_kWh']);
	}
	if (isset($_POST['monthly_fee_supply'])) {
		$monthly_fee_supply = trim($_POST['monthly_fee_supply']);
	}
	if (isset($_POST['monthly_fee_distr'])) {
		$monthly_fee_distr 	= trim($_POST['monthly_fee_distr']);
	}
	if (isset($_POST['dph'])) {
		$dph 				= trim($_POST['dph']);
	} // This is the "dph" of the "sortiment item" not the "dph" of the season


	$new_season = new stdClass();

	$new_season->season_name		= $season_name;
	$new_season->start_date		  	= $start_date;
	$new_season->start_value		= $start_value;
	$new_season->volume_coeficient  = $volume_coeficient;
	$new_season->avg_combust_heat   = $avg_combust_heat;
	$new_season->unit_price_kWh 	= $unit_price_kWh;
	$new_season->distr_fee_kWh 	  	= $distr_fee_kWh;
	$new_season->transport_fee_kWh  = $transport_fee_kWh;
	$new_season->monthly_fee_distr  = $monthly_fee_distr;
	$new_season->monthly_fee_supply = $monthly_fee_supply;
	$new_season->dph 				= $dph;

	//print_r($new_season); die();

	// Validations
	// $numeric_field_names = array("transport_fee_kWh_množstvo", "monthly_fee_supply_množstvo");
	// valdphate_numeric_input_type($numeric_field_names);

	// $numeric_field_names = array("transport_fee_kWh", "monthly_fee_supply");
	// $regex='/^([1-9]\d{0,2})$/';
	// $error_message="Zadajte číslo od 1 do 999";
	// validate_numeric_input_type($numeric_field_names, $regex, $error_message);


	if (($hodnotenie === "") || ($hodnotenie === "KT1")) // The "poznámka" field is required only if "KT2" or "KT3" are selected
	{
		$required_fields = array("start_date", "start_value",/* "volume_coeficient", "avg_combust_heat", "unit_price_kWh", "kt", "dph",*/ "distr_fee_kWh", "transport_fee_kWh", "monthly_fee_distr", "monthly_fee_supply");
	} else {
		$required_fields = array("start_date", "start_value",/* "volume_coeficient", "avg_combust_heat", "unit_price_kWh", "kt", "dph",*/ "distr_fee_kWh", "transport_fee_kWh", "monthly_fee_distr", "monthly_fee_supply", "poznámka"); // input field names, NOT the db columns names!
	}
	validate_presences($required_fields);


	if (!empty($errors)) {
		if ($season->save($new_season)) {
			// echo "Pjer";
			// print_r($new_season); die();
			//			log_action('New season amonthly_fee_distred', "Created by ".$current_otk->full_name().".");
			//	 		The statement above can be used to display All the attributes of the $current_otk (name, surname, razitko etc.)
			log_action($current_otk_initials . " : Nový protokol: " . $next_dph . "/" . $location . "/" . date('y')); // You could make a function for new season number!
			log_trimmer();

			// Unsetting the "temp_season" SESSION values.
			unset($_SESSION['temp_season']);

			$session->message("Nový záznam o protokole bol uložený do databázy.");
			redirect_to("current-season");
		} else {
			// Failed
			$message = "Nový záznam o protokole nebol uložený do databázy.";
		}
	}
}

if ($message == "") {
	$message = "Upravte údaje podľa potreby. ZmenyX v nastaveniach potvrďte kliknutím na \"Uložiť zmeny\"!";
}

?>




<p class="top-bar-message"><?php echo $message ?> </p>

<body class="forms">
	<div dph="all_forms">
		<div class="all_forms">
			<h2 style="text-align: center">Registrácia nového faktur. obdobia</h2>
			<form name="myForm" action="season-add" method="post">

				<label class="forms">Obdobie:</label>
				<input type="text" name="season_name" value="<?php if (isset($season_name)) {
																	echo $season_name;
																} ?>" /><br />
				<?php echo formated_div_error($errors, "season_name"); ?>

				<label class="forms">Začiatok obdobia:</label>
				<input type="text" id="datepicker" name="start_date" value="<?php if (isset($start_date)) {
																				echo $start_date;
																			}  ?>" /><br />
				<?php echo formated_div_error($errors, "start_date"); ?>

				<label class="forms">Stav plynomera:</label>
				<input type="text" name="start_value" value="<?php if (isset($start_value)) {
																	echo $start_value;
																} ?>" /><br />
				<?php echo formated_div_error($errors, "start_value"); ?>

				<label class="forms">Objemový koeficient:</label>
				<input type="text" name="volume_coeficient" value="<?php if (isset($volume_coeficient)) {
																		echo $volume_coeficient;
																	} ?>" /><br />
				<?php echo formated_div_error($errors, "volume_coeficient"); ?>

				<label class="forms">Priem. spaľ. teplo:</label>
				<input type="text" name="avg_combust_heat" value="<?php if (isset($avg_combust_heat)) {
																		echo $avg_combust_heat;
																	} ?>" /><br />
				<?php echo formated_div_error($errors, "avg_combust_heat"); ?>

				<label class="forms">Jedn. cena za kWh:</label>
				<input type="text" name="unit_price_kWh" value="<?php if (isset($unit_price_kWh)) {
																	echo $unit_price_kWh;
																} ?>" /><br />
				<?php echo formated_div_error($errors, "unit_price_kWh"); ?>

				<label class="forms">Distribucia za kWh:</label>
				<input type="text" name="distr_fee_kWh" value="<?php if (isset($distr_fee_kWh)) {
																	echo $distr_fee_kWh;
																} ?>" /><br />
				<?php echo formated_div_error($errors, "distr_fee_kWh"); ?>

				<label class="forms">Preprava za kWh:</label>
				<input type="text" name="transport_fee_kWh" value="<?php if (isset($transport_fee_kWh)) {
																		echo $transport_fee_kWh;
																	} ?>" /><br />
				<?php echo formated_div_error($errors, "transport_fee_kWh"); ?>

				<label class="forms">Mes. za dodávku:</label>
				<input type="text" name="monthly_fee_supply" value="<?php if (isset($monthly_fee_supply)) {
																		echo $monthly_fee_supply;
																	} ?>" /><br />
				<?php echo formated_div_error($errors, "monthly_fee_supply"); ?>

				<label class="forms">Mes. za distribúciu:</label>
				<input type="text" name="monthly_fee_distr" value="<?php if (isset($monthly_fee_distr)) {
																		echo $monthly_fee_distr;
																	} ?>" /><br />
				<?php echo formated_div_error($errors, "monthly_fee_distr"); ?>

				<label class="forms">Sadzbe DPH:</label>
				<input type="text" name="dph" value="<?php if (isset($dph)) {
															echo $dph;
														} ?>" /><br />
				<?php echo formated_div_error($errors, "dph"); ?>

				<label class="forms"> </label>
				<input type="submit" class="button_ok" name="submit" value="Uložiť">
				<a href="home" class="button_cancel">Zrušiť</a><br /><br />

			</form>

		</div>
	</div>
	<!-- 	<div dph="txtHint2"></div> -->
</body>

</html>