<?php
require_once("includes/initialize.php");
if (!$session->is_logged_in()) {
    redirect_to("admin/login.php");
}


$user     = new User($db);
$price_schema = new Price_schema($db); //print_r($odpocty); die();

$price_schemas = $price_schema->find_All(); // print_r($price_schemas); die();
//$current_season = 

//echo "current season: " . current_season() . "<br>";
$current_season_name = current_season();

if (!isset($_SESSION['current_season_name'])) {
    $_SESSION['current_season_name'] = $current_season_name;
    $current_season = $price_schema->find_by_season_name($current_season_name);
} else {
    $current_season = $price_schema->find_by_season_name($_SESSION['current_season_name']);
}

// print_r($_SESSION); die();


// Assigning "suciastka" DB values to variables.
if (isset($_POST['submit'])) {
    $selected_season_name            = $_POST['season-name']; //echo $current_season_name; die();
    $_SESSION['current_season_name'] = $selected_season_name; //print_r($_SESSION);// die();
    $current_season                  = $price_schema->find_by_season_name($selected_season_name);

    $message = "Fakturačné obdobie bolo zmenené na {$current_season->season_name}.";
    $_SESSION['message'] = $message;

    redirect_to("home");

    //print_r($current_season);

    // $price_schema_name        = $current_season->season_name;
    // $start_date         = $current_season->start_date;
    // $start_value        = $current_season->start_value; 
    // $volume_coeficient  = $current_season->volume_coeficient; 
    // $avg_combust_heat   = $current_season->avg_combust_heat; 
    // $unit_price_kWh     = $current_season->unit_price_kWh; 
    // $distr_fee_kWh      = $current_season->distr_fee_kWh; 
    // $transport_fee_kWh  = $current_season->transport_fee_kWh;
    // $monthly_fee_distr  = $current_season->monthly_fee_distr;
    // $monthly_fee_supply = $current_season->monthly_fee_supply;
    // $dph                = $current_season->dph;


    // if ($objWriter){
    // 	log_action( $current_otk_initials." : Protokol ".$current_protocol_number." exportovaný.");
    // 	log_trimmer();
    // 	$_SESSION["message"]="Export zaznamu OK!";
    // }

    //redirect_to("export_done.php");

}

if ($message == "") {
    $message = "Vyberte fakturačné obdobie. Zmenu potvrďte kliknutím na \"Zmeniť\".";
}
?>




<p class="top-message-bar"><?php echo $message ?> </p>

<div class="forms">

    <div class="settings">

        <h3 class="center">Parametre fakturačného obdobia <?php echo $_SESSION['current_season_name']; ?></h3>
        <div class="form-container">
            <form name="myForm" action="season-current" method="post">
                <div class="one-line-form__container">

                    <label>Obdobie:</label>

                    <select name="season-name">
                        <!-- <option value=""> - Vybrať - </option> -->
                        <?php foreach ($price_schemas as $price_schema) : ?>
                            <option value="<?php echo htmlentities($price_schema->season_name); ?>" <?php
                                                                                                    if ((isset($current_season->season_name)) && ($price_schema->season_name == $current_season->season_name)) {
                                                                                                        echo "selected";
                                                                                                    } ?>>
                                <?php echo htmlentities($price_schema->season_name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input class="button-ok" type="submit" name="submit" value="Zmeniť">

                    <a href=<?php echo go_to_URL_by_route('home'); ?> class="btn-borderless">Späť na odpočty</a>
                </div>
            </form>

            <table class="tarif-table">
                <thead>
                    <tr class="tarif-table__tbody-head">
                        <th colspan="1" style="text-align: center; width: 40%;"> Parameter</th>
                        <th colspan="1" style="text-align: center; width: 30%;">Hodnota parametra</th>
                        <!-- <th colspan="1" style="text-align: center; width: 30%;">Počet zmien</th> -->
                    </tr>
                </thead>
                <tbody>

                    <tr class="tarif-table__tbody-row">
                        <td style="text-align: left; padding-left:4px">Tarifa</td>
                        <td style="text-align: left; padding-left:4px"><?php echo htmlentities($current_season->tarif); ?></td>
                    </tr>
                    <tr class="tarif-table__tbody-row">
                        <td style="text-align: left; padding-left:4px">Parametre platné od</td>
                        <td style="text-align: left; padding-left:4px"><?php echo htmlentities($current_season->established); ?></td>
                    </tr>
                    <tr class="tarif-table__tbody-row">
                        <td style="text-align: left; padding-left:4px">Dátum oficialneho odpočtu</td>
                        <td style="text-align: left; padding-left:4px"><?php echo htmlentities($current_season->start_date); ?></td>
                    </tr>
                    <tr class="tarif-table__tbody-row">
                        <td style="text-align: left; padding-left:4px">Počiatočný stav plynomera</td>
                        <td style="text-align: left; padding-left:4px"><?php echo htmlentities($current_season->start_value); ?></td>
                    </tr>
                    <tr class="tarif-table__tbody-row">
                        <td style="text-align: left; padding-left:4px">Objemové prepočítavacie číslo</td>
                        <td style="text-align: left; padding-left:4px"><?php echo htmlentities($current_season->volume_coeficient); ?></td>
                    </tr>
                    <tr class="tarif-table__tbody-row">
                        <td style="text-align: left; padding-left:4px">Spaľovacie teplo objemové</td>
                        <td style="text-align: left; padding-left:4px"><?php echo htmlentities($current_season->avg_combust_heat); ?></td>
                    </tr>
                    <tr class="tarif-table__tbody-row">
                        <td style="text-align: left; padding-left:4px">Stála mesačná platba za OM</td>
                        <td style="text-align: left; padding-left:4px"><?php echo htmlentities($current_season->monthly_fee); ?></td>
                    </tr>
                    <tr class="tarif-table__tbody-row">
                        <td style="text-align: left; padding-left:4px">Sadzba za odobratý plyn (za kWh)</td>
                        <td style="text-align: left; padding-left:4px"><?php echo htmlentities($current_season->unit_price_kWh); ?></td>
                    </tr>
                    <tr class="tarif-table__tbody-row">
                        <td style="text-align: left; padding-left:4px">Dodávka - Stála mesačná platba za OM</td>
                        <td style="text-align: left; padding-left:4px"><?php echo htmlentities($current_season->monthly_fee_supply); ?></td>
                    </tr>
                    <tr class="tarif-table__tbody-row">
                        <td style="text-align: left; padding-left:4px">Dodávka - variabilná zložka za kWh</td>
                        <td style="text-align: left; padding-left:4px"><?php echo htmlentities($current_season->supply_fee_kWh); ?></td>
                    </tr>
                    <tr class="tarif-table__tbody-row">
                        <td style="text-align: left; padding-left:4px">Distribucia - Stála mesačná platba za OM</td>
                        <td style="text-align: left; padding-left:4px"><?php echo htmlentities($current_season->monthly_fee_distr); ?></td>
                    </tr>
                    <tr class="tarif-table__tbody-row">
                        <td style="text-align: left; padding-left:4px">Distribucia - variabilná sadzba za kWh</td>
                        <td style="text-align: left; padding-left:4px"><?php echo htmlentities($current_season->distr_fee_kWh); ?></td>
                    </tr>
                    <tr class="tarif-table__tbody-row">
                        <td style="text-align: left; padding-left:4px">Preprava - variabilná zložka za kWh</td>
                        <td style="text-align: left; padding-left:4px"><?php echo htmlentities($current_season->transport_fee_kWh); ?></td>
                    </tr>
                    <tr class="tarif-table__tbody-row">
                        <td style="text-align: left; padding-left:4px">Mesačná platba - preddavok</td>
                        <td style="text-align: left; padding-left:4px"><?php echo htmlentities($current_season->advance_payment); ?></td>
                    </tr>
                    <tr class="tarif-table__tbody-row">
                        <td style="text-align: left; padding-left:4px">DPH</td>
                        <td style="text-align: left; padding-left:4px"><?php echo htmlentities($current_season->dph); ?></td>
                    </tr>

                </tbody>
            </table>
        </div>
        <!-- <div class="butons-section">
                <a href=<?php echo go_to_URL_by_route('home'); ?> class="button-ok btn-right">Späť na odpočty</a>
            </div> -->

    </div>
</div>

<?php include('layouts/footer.php'); ?>