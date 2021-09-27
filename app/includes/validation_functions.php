<?php

$errors = array();

function fieldname_as_text($fieldname)
{
	$underscore_position = strpos($fieldname, "_"); // Checking underscore position in the fieldname.
	$fieldname = str_replace("_", "", $fieldname); // Repmoving underscore (replacing with nothing).
	$fieldname = mb_convert_case($fieldname, MB_CASE_TITLE, "UTF-8"); // Converting first letter to uppercase.
	$fieldname = substr_replace($fieldname, " ", $underscore_position, 0); // Inserting gap in former underscore position.

	return $fieldname;
}


// * PRESENCE
// use trim() so empty spaces don't count
// use === to avoid false positives
// empty() would consider "0" to be empty

function has_presence($value)
{
	return isset($value) && $value !== "";
} // If value is set and is not equal to empty string - return true


function validate_presences($required_fields)
{
	global $errors;
	foreach ($required_fields as $field) {
		$value = trim($_POST[$field]);
		if (!has_presence($value)) {
			$errors[$field] = fieldname_as_text($field) . " je povinný údaj!";
		}
	}
}


function validate_password_db_vs_inputX($field_name, $found_user)
{
	global $errors;
	// $username=trim($_POST[$field_name]);
	$password = trim($_POST[$field_name]);
	if (!$found_user) {
		$errors[$field_name] = "Nesprávna kombinácia heslo/login.";
	}
}


function validate_password_db_vs_input($field_name, $user, $username, $password)
{
	global $errors;
	// $username=trim($_POST[$field_name]);
	$password = trim($_POST[$field_name]);
	if (!attempt_login($user, $username, $password)) {
		$errors[$field_name] = "Nesprávna kombinácia heslo/login.";
	}
}


function validate_password_confirmation($pass1, $pass2)
{
	global $errors;
	$value_pass1 = trim($_POST[$pass1]);
	$value_pass2 = trim($_POST[$pass2]);

	if ($value_pass1 === '') :
		$errors[$pass2] = "Overenie hesla je povinné RR";
	elseif ($value_pass2 === '') :
		$errors[$pass2] = "Overenie hesla je povinné RR";
	elseif ($value_pass1 !== $value_pass2) :
		$errors[$pass2] = "Hesla sa nezhoduju. Skúste to znovu.";
	endif;
}


// function validate_numeric_input_type($field_name){ // input must be a number from 1 to 99
// 	global $errors;
// 	$input_value=trim($_POST[$field_name]);
// 	if ($input_value === ''):
// 		$errors[$field_name]="Čislo pečiatky je povinný údaj!";
// 	elseif(!(preg_match('/^([1-9]?\d|99)$/', $input_value))): // To check that input pattern matches we use regular expressions
// 		$errors[$field_name] = "Zadajte číslo od 1 do 99";
// 	endif;
// }

// function validate_numeric_input_type($numeric_field_names){ // input must be a number from 1 to 99
// 	global $errors;
// 	foreach($numeric_field_names as $numeric_field_name):
// 		$numeric_input_value=trim($_POST[$numeric_field_name]);
// 		if ($numeric_input_value === ''):
// 			$errors[$numeric_field_name]=fieldname_as_text($numeric_field_name)." je povinný údaj!";
// 		elseif(!(preg_match('/^([1-9]?\d|99)$/', $numeric_input_value))): // To check that input pattern matches we use regular expressions
// 			$errors[$numeric_field_name] = "Zadajte číslo od 1 do 99";
// 		endif;
// 	endforeach;
// }


function validate_numeric_input_type($numeric_field_names, $regex, $error_message)
{ 	// input must be a number from 1 to 99.
	global $errors;
	foreach ($numeric_field_names as $numeric_field_name) :
		$numeric_input_value = trim($_POST[$numeric_field_name]);
		if ($numeric_input_value === '') :
			$errors[$numeric_field_name] = fieldname_as_text($numeric_field_name) . " je povinný údaj!";
		elseif (!(preg_match($regex, $numeric_input_value))) : // To check that input pattern matches we use regular expressions
			$errors[$numeric_field_name] = $error_message;
		endif;
	endforeach;
}

/*
 * VALIDATE POSITIVE NUMERIC INPUT VALUE
 * Valid input must be a number > 0 and in format xxxxx.xxx or xxxxx,xxx.
 * As current regex is not perfect to catch errors like multiple decimal marks,
 * we treat this case in "elseif" using additional PHP functions.
 * With PHP 8 (and highier) the strpos() can be replaced by str_contains().
 */
function validate_positive_numeric_input_value($numeric_field_names, $regex, $error_message)
{
	global $errors;
	foreach ($numeric_field_names as $numeric_field_name) :
		$numeric_input_value = trim($_POST[$numeric_field_name]);
		if ($numeric_input_value === '') :
			$errors[$numeric_field_name] = fieldname_as_text($numeric_field_name) . " je povinný údaj!";
		elseif (
			!(preg_match($regex, $numeric_input_value)) // Checking if input pattern matches the regular expression.
			|| $numeric_input_value == 0
			|| substr_count($numeric_input_value, ".") > 1 // Max one ".".
			|| substr_count($numeric_input_value, ",") > 1 // Max one ",".
			|| (stripos($numeric_input_value, ".") && stripos($numeric_input_value, ",")) // Can not contain both "." and ",".
		) :
			$errors[$numeric_field_name] = $error_message;
		endif;
	endforeach;
}


function validate_alphabetic_input_type($alphabetic_field_names)
{	// input must NOT be a number.
	global $errors;
	foreach ($alphabetic_field_names as $alphabetic_field_name) :
		$alphabetic_input_value = trim($_POST[$alphabetic_field_name]);
		if ($alphabetic_input_value === '') :
			$errors[$alphabetic_field_name] = fieldname_as_text($alphabetic_field_name) . " je povinný údaj!";
		elseif (!(preg_match('/^[a-žA-Ž]+$/', $alphabetic_input_value))) : // To check that input pattern matches we use regular expressions
			$errors[$alphabetic_field_name] = "Iba abecedné znaky!";
		endif;
	endforeach;
}


// * STRING LENGHT
// max lenght
function has_max_length($value, $max)
{
	return strlen($value) <= $max;
}


function validate_max_lengths($fields_with_max_lengths)
{
	global $errors; // we need global scope! IMPORTANT!
	foreach ($fields_with_max_lengths as $field => $max) {
		$value = trim($_POST[$field]);
		if (!has_max_length($value, $max)) {
			$errors[$field] = fieldname_as_text($field) . " obsahuje nedovolený počet znakov (" . strlen($value) . ")! Max.=" . $max;
		}
	}
}


function validate_max_float_lengths($fields_with_max_lengths)
{
	global $errors; // we need global scope! IMPORTANT!
	foreach ($fields_with_max_lengths as $field => $max) {
		if (is_numeric($_POST[$field])) { // Checking if input value is a number.
			$value = float_decimal_formater(trim($_POST[$field]));
			if (!has_max_length($value, $max)) {
				$errors[$field] = fieldname_as_text($field) . " obsahuje nedovolený počet znakov (" . strlen($value) . ")! Max.=" . $max;
			}
		}
	}
}


// min lenght
function has_min_length($value, $min)
{
	return strlen($value) >= $min;
}


// * TYPE

// * INCLUSION IN A SET
function has_inclusion_in($value, $set)
{
	return in_array($value, $set);
}


function validate_min_lengths($fields_with_min_lengths)
{
	global $errors; // we need global scope! IMPORTANT!
	foreach ($fields_with_min_lengths as $field => $min) {
		$value = trim($_POST[$field]);
		if (!has_min_length($value, $min) && (strlen($value) > 0)) {
			$errors[$field] = fieldname_as_text($field) . " obsahuje málo znakov (" . strlen($value) . ")! Min.=" . $min;
		} elseif (strlen($value) < 1) {
			$errors[$field] = fieldname_as_text($field) . " je povinný údaj!";
		}
	}
}


function validate_fields_required($fields_required)
{
	global $errors;
	foreach ($fields_required as $field) {
		$value = trim($_POST[$field]);
		if (!has_presence($value)) {
			//			$errors[$field]=ucfirst($field)." can't be blank!"; // replaced by the line below
			$errors[$field] = fieldname_as_text($field) . " can't be blank!";
		}
	}
}


/**
 * URL ID Segments Validation
 *
 * Validates URL id segment from current URL like i.e. http://example.com/edit/5
 *
 * @return $id or false.
 */
function url_id_segment_validation($object, $segment_id)
{
	// Retrieving all object IDs from DB.
	$real_object_IDs = $object->get_object_IDs();
	// Empty array to store object IDs.
	$valid_object_IDs = [];

	// Creating array of IDs from real_object_IDs (array of arrays).
	foreach ($real_object_IDs as $real_object_ID) {
		$real_object_ID = $real_object_ID[0];
		$valid_object_IDs[] .= $real_object_ID;
	}

	// Checking if the provided URL segment_id is in array of valid_object_IDs.
	if (in_array($segment_id, $valid_object_IDs)) {
		return $segment_id;
	} else {
		return false;
	}
}


	// * UNIQUENESS - uses database to check uniqueness
	// * FORMAT
	// use a regex on a string - Syntax: preg_match($regex, $subject)
