<?php

/**
 * Current season
 * Provides current season name base on current date.
 * If current month is after July (7), season changes.
 * Used in nav_bar.php for setting default season.
 */
function current_season()
{
	$current_year = (int)date("Y");
	$current_month = (int)date("m");

	if ($current_month > 7) {
		$current_season = $current_year . "-" . ($current_year + 1);
		return $current_season;
	} else {
		$current_season = ($current_year - 1) . "-" . $current_year;
		return $current_season;
	}
}


/**
 * Find specified season readings
 * Searches for specified season readings based on season name.
 * Used in nav_bar.php for setting default season.
 */
function find_specified_season_readings($season_name)
{
	$a = explode("-", $season_name); // Converts yyyy-yyyy into [yyyy, yyyy];
	$date_min = $a[0] . "-08-01";
	$date_max = $a[1] . "-07-31";

	$date_range = [
		"date_min" => $a[0] . '-08-01',
		"date_max" => $a[1] . '-07-31',
	];

	//return $date_min. "-" .$date_max;
	return (object)$date_range;
}


/**
 * Price Formater
 * Converts number floats to proper currency format.
 * returns string.
 * Note: The appended X in function name means that the function is not used.
 */
function price_formaterX($price)
{
	$formated_number = number_format($price, 2, ',', '&nbsp');
	echo $formated_number . '&nbsp&euro;';
}


/**
 * Price Formater
 * Converts number floats to proper currency format.
 * returns string.
 */
function price_formater($price)
{
	$fmt = numfmt_create('sk_SK', NumberFormatter::CURRENCY);
	return numfmt_format_currency($fmt, $price, "EUR");
}


/**
 * Number Formater
 * Converts number floats to required format i.e "xx xxx,xx".
 * returns string.
 */
function number_formater($number)
{
	$formated_num = number_format($number, 2, ',', ' ');
	return $formated_num;
}


/**
 * Float Decimal Formater
 * Converts number floats to required format i.e "xxxxx.xx".
 * returns string.
 */
function float_decimal_formater($number)
{
	$formated_num = number_format($number, 2, '.', '');
	return $formated_num;
}


/**
 * Decimal Separator Watcher
 * Checkes the decimal separator. If it's comma then it will be changed to dot.
 * Provided value MUST contain only numbers.
 * If not, unchanged value is returned.
 * Uses the float_decimal_formater().
 */
function decimal_separator_watcher($checked_value)
{
	$corrected_number_separator = trim(str_replace(',', '.', $checked_value));

	if (substr_count($corrected_number_separator, ".") <= 1) {
		if (is_numeric($corrected_number_separator)) {
			$float_number = (float)$corrected_number_separator;
			$corrected_number = float_decimal_formater($float_number);
			return $corrected_number;
		}
	};
	return $checked_value;
}

/**
 * Gas Meter Value Checker
 * Checks number decimal separator using the decimal_separator_watcher(). 
 * Returns number if it's correct.
 * If value is not a valid number, it returns unchanged value which will cause an error.
 */
function gas_meter_value_checker($value)
{
	if (isset($value) && decimal_separator_watcher($value) > 0) {
		return decimal_separator_watcher($value);
	} else {
		return $value;
	}
}


/**
 * Season Official Start End Dates
 * Generates official start and end dates for current season.
 * Returns string.
 */
function season_official_start_end_dates($current_season_name)
{
	$season_years = explode("-", $current_season_name);
	$season_start_date 	= $season_years[0] . "-08-01";
	$season_end_date = $season_years[1] . "-07-31";

	$official_dates = [
		"season_start_date" => $season_start_date,
		"season_end_date"   => $season_end_date
	];

	return (object)$official_dates;
}


/**
 * Season months counter
 * Provides number of months for specified season.
 * returns integer.
 */
function season_months_counter($current_season_name)
{
	$season_years 		= explode("-", $current_season_name);
	$season_start_date 	= $season_years[0] . "-07-31";
	$season_ending_date = $season_years[1] . "-08-01";
	$current_date 		= date("Y-m-d");

	$months_check = dateDifference($season_start_date, $current_date, $differenceFormat = '%y'); // echo $months_check; die();

	if ($months_check >= 1) {
		$months = 12;
	} else {
		$months = dateDifference($season_start_date, $current_date, $differenceFormat = '%m');
	}

	return $months;
}


/**
 * Date Format Converter
 * Converts "Y-m-d" to "d-m-Y".
 */
function date_format_converter($db_date)
{
	$date = date_create($db_date);
	$converted_date = date_format($date, "d-m-Y");

	return $converted_date;
}


/**
 * Date difference
 * Provides number of days between two readings.
 */
function dateDifference($date_1, $date_2, $differenceFormat = '%a')
{
	$datetime1 = date_create($date_1);
	$datetime2 = date_create($date_2);

	$interval = date_diff($datetime1, $datetime2);

	return $interval->format($differenceFormat);
}


/**
 * Real consumption volume
 * To get "real gas volume consumption" it's necessary to implement the "local sea level coefficient".
 */
function real_gas_consumption_volume($consumption_volume, $volume_coeficient)
{
	$real_gas_consumption_volume = $consumption_volume * $volume_coeficient;
	return $real_gas_consumption_volume;
}


/**
 * Gas consumption in kWh
 * To get "gas consumption in kWh" we need to apply the "average combustion heat volume" coefficient. 
 */
function gas_consumption_in_kWh($real_gas_consumption_volume)
{
	$gas_consumption_in_kW = $real_gas_consumption_volume * AVERAGE_COMBUSTION_HEAT_VOLUME;
	return $gas_consumption_in_kW;
}


function row_values_calculations($gas_meter_readings, $current_season_schema, $monthly_fees, $i)
{
	// Preparing values for HTML table row output.
	$start_date 		 = $current_season_schema->start_date;
	$start_value		 = $current_season_schema->start_value;
	$volume_coeficient 	 = $current_season_schema->volume_coeficient;
	$unit_price_kWh		 = $current_season_schema->unit_price_kWh; // Not used as it's equal to sum of the rest of *_fee_kWh fees.
	$distr_fee_kWh		 = $current_season_schema->distr_fee_kWh;
	$transport_fee_kWh	 = $current_season_schema->transport_fee_kWh;
	$supply_fee_kWh	 	 = $current_season_schema->supply_fee_kWh;
	$monthly_fee_supply	 = $current_season_schema->monthly_fee_supply;
	$monthly_fee_distr	 = $current_season_schema->monthly_fee_distr;
	$dph	 			 = $current_season_schema->dph;

	$monthly_fee		 = $current_season_schema->monthly_fee; // echo $monthly_fee;

	// The $order_number below will be used instead of $id because we want each season to start from 1.
	$order_number = $i + 1;

	$date = date_format_converter($gas_meter_readings[$i]->date);

	// Number of days between the gas meter readings.
	// The "start_day" below is the date of last official reading and the "date" is the current "reading date". 
	if ($i == 0) {
		$days = dateDifference($start_date, $date, $differenceFormat = '%a');
	} else {
		$days = dateDifference($gas_meter_readings[$i - 1]->date, $date, $differenceFormat = '%a');
	}

	$current_reading = $gas_meter_readings[$i]->reading;
	$previous_reading = $gas_meter_readings[$i - 1]->reading;

	if ($i == 0) {
		// The first gas volume value. 
		$consumption_volume = $current_reading - $start_value;
	} else {
		$consumption_volume = $current_reading - $previous_reading;
	}

	$real_gas_consumption_volume		 = real_gas_consumption_volume($consumption_volume, $volume_coeficient);
	$consumption_kWh					 = gas_consumption_in_kWh($real_gas_consumption_volume);
	$consumption_kWh_supply_fee_costs	 = $consumption_kWh * $supply_fee_kWh; // $consumption_kWh * $unit_price_kWh; // The three fees below could be replaced by "unit_price_kWh".
	$consumption_kWh_distr_fee_costs	 = $consumption_kWh * $distr_fee_kWh;
	$consumption_kWh_transport_fee_costs = $consumption_kWh * $transport_fee_kWh;
	$daily_consump_volume 				 = $consumption_volume / $days;
	$daily_consump_kWh 	  				 = $consumption_kWh / $days;
	$daily_consump_money  				 = $consumption_kWh_supply_fee_costs / $days;

	$calculations = [
		"order_number" 				  		  => $order_number,
		"date" 						  		  => $date,
		"days" 						  		  => $days,
		"current_reading" 			  		  => $current_reading,
		"consumption_volume" 		  		  => $consumption_volume,
		"real_gas_consumption_volume" 		  => $real_gas_consumption_volume,
		"consumption_kWh"			  		  => $consumption_kWh,
		"consumption_kWh_supply_fee_costs"	  => $consumption_kWh_supply_fee_costs,
		"consumption_kWh_distr_fee_costs"	  => $consumption_kWh_distr_fee_costs,
		"consumption_kWh_transport_fee_costs" => $consumption_kWh_transport_fee_costs,
		"daily_consump_volume"		  		  => $daily_consump_volume,
		"daily_consump_kWh"			  		  => $daily_consump_kWh,
		"daily_consump_money"		  		  => $daily_consump_money,
		"dph"						  		  => $dph,
		"monthly_fees"						  => $monthly_fees
	];

	return (object)$calculations;
}


//$date_range = (object)find_specified_season_readings("2017-2018");
//$date_range = find_specified_season_readings("2017-2018");
//print_r($date_range->date_min);
