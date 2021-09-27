<?php
require_once("includes/initialize.php");
if (!$session->is_logged_in()) {
    redirect_to("admin/login.php");
}


$user          = new User($db);
$price_schema  = new Price_schema($db); //print_r($odpocty); die();

$price_schemas = $price_schema->find_All(); // print_r($price_schemas); die();
//$current_price_schema = 

// Current season name is based on real / current date.
$current_season_name = current_season();
//echo "current price_schema: " . $current_season_name . "<br>";

if (!isset($_SESSION['current_season_name'])) {
    $_SESSION['current_season_name'] = $current_season_name;
    $current_price_schema = $price_schema->find_by_season_name($current_season_name);
} else {
    $current_price_schema = $price_schema->find_by_season_name($_SESSION['current_season_name']);
}

// print_r($_SESSION); die();


// Assigning season parameters DB values to variables.
if (isset($_POST['submit'])) {
    // print_r($_POST);
    // $selected_price_schema_name      = $_POST['season-name']; //echo $current_price_schema_name; die();
    // $_SESSION['current_season_name'] = $selected_price_schema_name; //print_r($_SESSION);// die();
    // $current_price_schema            = $price_schema->find_by_season_name($selected_price_schema_name);

    //print_r($current_price_schema);

    if (isset($_POST['start_date'])) {
        $start_date             = trim($_POST['start_date']);
    }
    if (isset($_POST['start_value'])) {
        $start_value            = trim($_POST['start_value']);
    }
    if (isset($_POST['tarif'])) {
        $tarif                  = trim($_POST['tarif']);
    }
    if (isset($_POST['volume_coeficient'])) {
        $volume_coeficient      = trim($_POST['volume_coeficient']);
    }
    if (isset($_POST['avg_combust_heat'])) {
        $avg_combust_heat       = trim($_POST['avg_combust_heat']);
    }
    if (isset($_POST['monthly_fee'])) {
        $monthly_fee            = trim($_POST['monthly_fee']);
    }
    if (isset($_POST['unit_price_kWh'])) {
        $unit_price_kWh         = trim($_POST['unit_price_kWh']);
    }
    if (isset($_POST['monthly_fee_supply'])) {
        $monthly_fee_supply     = trim($_POST['monthly_fee_supply']);
    }
    if (isset($_POST['supply_fee_kWh'])) {
        $supply_fee_kWh         = trim($_POST['supply_fee_kWh']);
    }
    if (isset($_POST['monthly_fee_distr'])) {
        $monthly_fee_distr      = trim($_POST['monthly_fee_distr']);
    }
    if (isset($_POST['distr_fee_kWh'])) {
        $distr_fee_kWh          = trim($_POST['distr_fee_kWh']);
    }
    if (isset($_POST['transport_fee_kWh'])) {
        $transport_fee_kWh      = trim($_POST['transport_fee_kWh']);
    }
    if (isset($_POST['month_fee_emergency'])) {
        $month_fee_emergency    = trim($_POST['month_fee_emergency']);
    }
    if (isset($_POST['dph'])) {
        $dph                    = trim($_POST['dph']);
    }
    if (isset($_POST['established'])) {
        $established            = trim($_POST['established']);
    }
    if (isset($_POST['advance_payment'])) {
        $advance_payment        = trim($_POST['advance_payment']);
    }

    $new_sprice_schema = new stdClass();

    $new_sprice_schema->season_name         = $current_season_name; // Not from POST but based on real date.
    $new_sprice_schema->start_date          = $start_date;
    $new_sprice_schema->start_value         = $start_value;
    $new_sprice_schema->tarif               = $tarif;
    $new_sprice_schema->volume_coeficient   = $volume_coeficient;
    $new_sprice_schema->avg_combust_heat    = $avg_combust_heat;
    $new_sprice_schema->monthly_fee         = $monthly_fee;
    $new_sprice_schema->unit_price_kWh      = $unit_price_kWh;
    $new_sprice_schema->monthly_fee_supply  = $monthly_fee_supply;
    $new_sprice_schema->supply_fee_kWh      = $supply_fee_kWh;
    $new_sprice_schema->monthly_fee_distr   = $monthly_fee_distr;
    $new_sprice_schema->distr_fee_kWh       = $distr_fee_kWh;
    $new_sprice_schema->transport_fee_kWh   = $transport_fee_kWh;
    $new_sprice_schema->month_fee_emergency = $month_fee_emergency;
    $new_sprice_schema->dph                 = $dph;
    $new_sprice_schema->established         = $established;
    $new_sprice_schema->advance_payment     = $advance_payment;


    /* Input Data Validation */

    // Presence validation (required fields).
    $required_fields = array("established", "start_date", "start_value", "volume_coeficient", "avg_combust_heat", "monthly_fee", "unit_price_kWh", "monthly_fee_supply", "supply_fee_kWh", "monthly_fee_distr", "distr_fee_kWh", "transport_fee_kWh", "month_fee_emergency", "dph", "advance_payment");
    validate_presences($required_fields);

    // Numeric imput type validation.
    $numeric_field_names = array("start_value", "volume_coeficient", "avg_combust_heat", "monthly_fee", "unit_price_kWh", "monthly_fee_supply", "supply_fee_kWh", "monthly_fee_distr", "distr_fee_kWh", "transport_fee_kWh", "month_fee_emergency", "dph", "advance_payment");
    $regex = '/^([,|.]?[0-9])+$/';
    $error_message = "Hodnotou musí byť číslo väčšie ako nula.";
    validate_positive_numeric_input_value($numeric_field_names, $regex, $error_message);

    // Max. input value length validation. UNFINISHED! Some customization for each item needed.
    $fields_with_max_lengths = array(
        "start_value"         => 6,
        "volume_coeficient"   => 6,
        "avg_combust_heat"    => 6,
        "monthly_fee"         => 6,
        "unit_price_kWh"      => 6,
        "unit_price_kWh"      => 6,
        "monthly_fee_supply"  => 6,
        "supply_fee_kWh"      => 6,
        "monthly_fee_distr"   => 6,
        "distr_fee_kWh"       => 6,
        "transport_fee_kWh"   => 6,
        "month_fee_emergency" => 6,
        "advance_payment"     => 6
    );
    validate_max_float_lengths($fields_with_max_lengths);

    if (empty($errors)) {
        if ($price_schema->save($new_sprice_schema)) {
            log_action($current_otk_initials . " : Nový cenník: " . $change_date . " bol vytvorený."); // You could make a function for new season number!
            log_trimmer();

            // Unsetting the "temp_season" SESSION values.
            unset($_SESSION['temp_season']);

            $session->message("Nový záznam o protokole bol uložený do databázy.");
            redirect_to("season-current");
        } else {
            // Failed
            $message = "Nový záznam o protokole nebol uložený do databázy.";
        }
    } else {
        // print_r($errors);
    }
} else {
    /* Variables with NULL values MUST be supplied with genuine values */
    $start_date             = null;
    $start_value            = null;
    $tarif                  = $current_price_schema->tarif;
    $volume_coeficient      = $current_price_schema->volume_coeficient;
    $avg_combust_heat       = $current_price_schema->avg_combust_heat;
    $monthly_fee            = $current_price_schema->monthly_fee;
    $unit_price_kWh         = $current_price_schema->unit_price_kWh;
    $monthly_fee_supply     = $current_price_schema->monthly_fee_supply;
    $supply_fee_kWh         = $current_price_schema->supply_fee_kWh;
    $monthly_fee_distr      = $current_price_schema->monthly_fee_distr;
    $distr_fee_kWh          = $current_price_schema->distr_fee_kWh;
    $transport_fee_kWh      = $current_price_schema->transport_fee_kWh;
    $month_fee_emergency    = $current_price_schema->month_fee_emergency;
    $dph                    = $current_price_schema->dph;
    $established            = null;
    $advance_payment        = $current_price_schema->advance_payment;
}

if (isset($errors)) {
    if (count($errors) == 1) {
        $error = array_values($errors);
        $message = $error[0];
    } elseif (count($errors) > 1) {
        $message = "Skontrolujte zadané hodnoty. Všetky údaje sú povinné!";
    }
}

if ($message == "") {
    $message = "Upravte údaje podľa potreby. Zmeny v nastaveniach potvrďte kliknutím na \"Uložiť zmeny\"!";
}

?>




<p class="top-message-bar"><?php echo $message ?></p>

<div class="forms">
    <div class="settings">

        <h3 class="center">Registrácia zmeny parametrov fakturačného obdobia <?php echo $_SESSION['current_price_schema_name']; ?></h3>

        <form name="myForm" action="season-add-change" method="post" autocomplete="off">
            <div class="form-container">

                <div class="one-line-form__container">
                    <label>Dátum zmeny:</label>
                    <input class="<?php echo input_error_border($errors, "established"); ?>" type="text" id="datepicker" name="established" value="<?php echo $established; ?>" />
                </div>

                <table class="tarif-table-add">
                    <thead>
                        <tr class="tarif-table__tbody-head">
                            <th colspan="1" style="text-align: center; width: 50%;">Parameter</th>
                            <th colspan="1" style="text-align: center; width: 30%;">Pôvodné hodnoty</th>
                            <th colspan="1" style="text-align: center; width: 20%;">Nové hodnoty</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Tarifa</td>
                            <td><?php echo htmlentities($current_price_schema->tarif); ?></td>
                            <td><input class="<?php echo input_error_border($errors, "tarif"); ?>" type="text" name="tarif" value="<?php echo htmlentities($tarif); ?>"></td>
                        </tr>
                        <tr>
                            <td>Mesačný poplatok za OM</td>
                            <td><?php echo htmlentities($current_price_schema->monthly_fee); ?></td>
                            <td><input class="<?php echo input_error_border($errors, "monthly_fee"); ?>" type="text" name="monthly_fee" value="<?php echo htmlentities($monthly_fee); ?>"></td>
                        </tr>
                        <tr>
                            <td>Dátum odpočtu pred zmenou</td>
                            <td><?php echo htmlentities($current_price_schema->start_date); ?></td>
                            <td><input class="<?php echo input_error_border($errors, "start_date"); ?>" type="text" id="datepicker2" name="start_date" value="<?php echo $start_date; ?>" /></td>
                        </tr>
                        <tr>
                            <td>Počiatočný stav plynomera</td>
                            <td><?php echo htmlentities($current_price_schema->start_value); ?></td>
                            <td><input class="<?php echo input_error_border($errors, "start_value"); ?>" type="text" name="start_value" value="<?php echo htmlentities($start_value); ?>"></td>
                        </tr>
                        <tr>
                            <td>Objemové prepočítavacie číslo</td>
                            <td><?php echo htmlentities($current_price_schema->volume_coeficient); ?></td>
                            <td><input class="<?php echo input_error_border($errors, "volume_coeficient"); ?>" type="text" name="volume_coeficient" value="<?php echo htmlentities($volume_coeficient); ?>"></td>
                        </tr>
                        <tr>
                            <td>Spaľovacie teplo objemové</td>
                            <td><?php echo htmlentities($current_price_schema->avg_combust_heat); ?></td>
                            <td><input class="<?php echo input_error_border($errors, "avg_combust_heat"); ?>" type="text" name="avg_combust_heat" value="<?php echo htmlentities($avg_combust_heat); ?>"></td>
                        </tr>
                        <tr>
                            <td>Sadzba za odobratý plyn (za kWh)</td>
                            <td><?php echo htmlentities($current_price_schema->unit_price_kWh); ?></td>
                            <td><input class="<?php echo input_error_border($errors, "unit_price_kWh"); ?>" type="text" name="unit_price_kWh" value="<?php echo htmlentities($unit_price_kWh); ?>"></td>
                        </tr>
                        <tr>
                            <td>Dodávka - Stála mesačná platba za OM</td>
                            <td><?php echo htmlentities($current_price_schema->monthly_fee_supply); ?></td>
                            <td><input class="<?php echo input_error_border($errors, "monthly_fee_supply"); ?>" type="text" name="monthly_fee_supply" value="<?php echo htmlentities($monthly_fee_supply); ?>"></td>
                        </tr>
                        <tr>
                            <td>Dodávka - variabilná zložka za kWh</td>
                            <td><?php echo htmlentities($current_price_schema->supply_fee_kWh); ?></td>
                            <td><input class="<?php echo input_error_border($errors, "supply_fee_kWh"); ?>" type="text" name="supply_fee_kWh" value="<?php echo htmlentities($supply_fee_kWh); ?>"></td>
                        </tr>
                        <tr>
                            <td>Distribucia - Stála mesačná platba za OM</td>
                            <td><?php echo htmlentities($current_price_schema->monthly_fee_distr); ?></td>
                            <td><input class="<?php echo input_error_border($errors, "monthly_fee_distr"); ?>" type="text" name="monthly_fee_distr" value="<?php echo htmlentities($monthly_fee_distr); ?>"></td>
                        </tr>
                        <tr>
                            <td>Distribucia - variabilná sadzba za kWh</td>
                            <td><?php echo htmlentities($current_price_schema->distr_fee_kWh); ?></td>
                            <td><input class="<?php echo input_error_border($errors, "distr_fee_kWh"); ?>" type="text" name="distr_fee_kWh" value="<?php echo htmlentities($distr_fee_kWh); ?>"></td>
                        </tr>
                        <tr>
                            <td>Preprava - variabilná zložka za kWh</td>
                            <td><?php echo htmlentities($current_price_schema->transport_fee_kWh); ?></td>
                            <td><input class="<?php echo input_error_border($errors, "transport_fee_kWh"); ?>" type="text" name="transport_fee_kWh" value="<?php echo htmlentities($transport_fee_kWh); ?>"></td>
                        </tr>
                        <tr>
                            <td>Mesačná platba - poistenie</td>
                            <td><?php echo htmlentities($current_price_schema->month_fee_emergency); ?></td>
                            <td><input class="<?php echo input_error_border($errors, "month_fee_emergency"); ?>" type="text" name="month_fee_emergency" value="<?php echo htmlentities($month_fee_emergency); ?>"></td>
                        </tr>
                        <tr>
                            <td>Mesačná platba - preddavok</td>
                            <td><?php echo htmlentities($current_price_schema->advance_payment); ?></td>
                            <td><input class="<?php echo input_error_border($errors, "advance_payment"); ?>" type="text" name="advance_payment" value="<?php echo htmlentities($advance_payment); ?>"></td>
                        </tr>
                        <tr>
                            <td>DPH</td>
                            <td><?php echo htmlentities($current_price_schema->dph); ?></td>
                            <td><input class="<?php echo input_error_border($errors, "dph"); ?>" type="text" name="dph" value="<?php echo htmlentities($dph); ?>"></td>
                        </tr>
                    </tbody>
                </table>

                <div class="butons-section">
                    <input type="submit" class="button-ok button-left" name="submit" value="Uložiť zmeny">
                    <a href=<?php echo go_to_URL_by_route('home'); ?> class="btn-borderless button-left">Späť na odpočty</a>
                </div>

            </div>
        </form>
    </div>
</div>


<?php include('layouts/footer.php'); ?>