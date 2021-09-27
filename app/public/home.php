<?php
require_once("includes/initialize.php");

if (!$session->is_logged_in()) {
	redirect_to("login");
}

$price_schema = new Price_schema($db);
$odpocty 	  = new Odpocty($db);
$user 	 	  = new User($db);

$current_otk = $user->current_otk(); // print_r($_SESSION); die(); // Provides access to all users attributes.

$current_season_name = $_SESSION['current_season_name']; // This is solved in nav_bar.php.
$gas_meter_readings  = $odpocty->find_all_by_season($current_season_name); // print_r($gas_meter_readings); die();

// Credit calculation - Amount of money paid as advanced payments minus curent total gas expences.
$current_season      = $price_schema->find_by_season_name($current_season_name);
$number_of_months	 = season_months_counter($current_season_name) + 1; // The "+1" is becaouse of we want the current month too.
if (isset($_SESSION['current_total_fees_incl_dph']) && $current_season->advance_payment * $number_of_months != null) {
	//echo $current_season->advance_payment * $number_of_months;
	$current_credit = price_formater($current_season->advance_payment * $number_of_months - $_SESSION['current_total_fees_incl_dph']);
} else {
	$current_credit = 0;
}

// Message for the top message bar.
$number_of_readings = count($gas_meter_readings);
if ($number_of_readings > 0 && $current_credit > 0) {
	$message_local = "Stav kreditu: {$current_credit} ";
} elseif ($number_of_readings > 0 && $current_credit == 0) {
	$message_local = "Údaj o výške mesačného preddavku nieje k dispozícii.";
} elseif ($number_of_readings < 1) {
	$message_local = "Žiadný záznam o odčítaní stavu plynomera.";
}

if ($message == "") {
	$message = $message_local;
}

?>





<p class="top-message-bar"><?php echo $message ?> </p>

<div class="gas-table-container">

	<table class="gas-table">
		<thead class="gas-table__thead">
			<tr>
				<th class="dark-layer" colspan="4">Dáta o odpočte</th>
				<th class="light-layer" colspan="4">Spotreba</th>
				<th class="dark-layer" colspan="3">Denne</th>
			</tr>
			<tr>
				<th class="gas-table__col-1 dark-layer">č.</th>
				<th class="gas-table__col-2 dark-layer">Dátum</th>
				<th class="gas-table__col-3 dark-layer">Dni</th>
				<th class="gas-table__col-4 dark-layer">m<sup>3</sup></th>
				<th class="gas-table__col-5">m<sup>3</sup></th>
				<th class="gas-table__col-6">real. m<sup>3</sup></th>
				<th class="gas-table__col-7">kWh</th>
				<th class="gas-table__col-8">&euro;</th>
				<th class="gas-table__col-9 dark-layer">m<sup>3</sup></th>
				<th class="gas-table__col-10 dark-layer">kWh</th>
				<th class="gas-table__col-11 dark-layer">&euro;</th>
			</tr>
		</thead>
		<tbody class="gas-table__tbody">
			<?php include("includes/table_content_calculations.php"); ?>
		</tbody>
		<tfoot class="gas-table__tfoot">
			<tr class="gas-table__bottom-row">
				<th class="gas-table__th"><?php echo "Spolu:"; ?></th>
				<th><?php // echo $date; 
					?></th>
				<th><?php // echo $days;
					?></th>
				<th><?php // echo $reading; 
					?></th>
				<th class="dark-layer"><?php echo number_formater($sum_consumption_volume); ?></th>
				<th class="dark-layer"><?php echo number_formater($sum_real_gas_consumption_volume); ?></th>
				<th class="dark-layer"><?php echo number_formater($sum_consumption_kWh); ?></th>
				<th class="dark-layer"><?php echo number_formater($sum_consumption_kWh_supply_fee_costs); ?></th>
				<th><?php // echo number_format($daily_consump_volume, 2); 
					?></th>
				<th><?php // echo number_format($daily_consump_kWh, 2); 
					?></th>
				<th><?php // echo number_format($daily_consump_money, 2); 
					?></th>
			</tr>
		</tfoot>
	</table>

	<section class="info">
		<h3>Jednotlivé položky ako su uvádzané v rozpise položiek k faktúre:</h3>
		<p>Suma za dodávku plynu vrátane mesačných poplatkov* za dodávku bez DPH: <?php echo price_formater($total_consumption_incl_monthly_supply_fees); ?></p>
		<p>Suma za poplatky** za kWh spojené s prepravou a distribúciou plynu bez DPH: <?php echo price_formater($total_kWH_distr_and_transport_fees); ?></p>
		<p>Suma celkových nákladov za plyn plus poplatky bez DPH: <?php echo price_formater($total_gas_and_fees); ?></p>
		<p>Suma celkových nákladov za plyn plus poplatky vrátane DPH: <?php echo price_formater($total_gas_and_fees_incl_vat); ?></p>
		<h4>Vysvetlivky:</h4>
		<p>* - Iba mesačný poplatok za dodávku plynu (podľa cenníkov pre dané obdobie). </p>
		<p>** - Mesačný poplatok za distribúciu + poplatok za distribuciu za kWh + poplatok za prepravu za kWh (podľa cenníkov pre dané obdobie). </p>
		<h4>Pozn.:</h4>
		<p>ToolTip pri spotrebe v &euro; zobrazuje hodnotu s poplatkami za kWh a ostatnými mesačnými poplatkami (monthly_fee = monthly_fee_supply + monthly_fee_distr) t.j. 8,64 = 7,64 + 1 &euro; bez DPH mesačne (s DPH je to cca 10,37&euro;).</p>
	</section>

</div>


<!-- Graph of daily gas consumption development -->

<div class="graph-container">
	<h3 class="center">Graf dennej spotreby plynu v m<sup>3</sup> za sezónu <?php echo $current_season_name; ?>.</h3>
	<?php include("partials/graph.php") ?>
</div>

<?php include('layouts/footer.php'); ?>