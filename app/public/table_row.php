<?php

/* The code of this file might seem weird, it's because it is a part of the "for loop" included in table_content_calculations.php file. */

$calculations = row_values_calculations($gas_meter_readings, $current_season_schema, $monthly_fees, $i);

// Partial & Final sums
$sum_consumption_volume 				 = $sum_consumption_volume + $calculations->consumption_volume;
$sum_real_gas_consumption_volume 		 = $sum_real_gas_consumption_volume + $calculations->real_gas_consumption_volume;
$sum_consumption_kWh 					 = $sum_consumption_kWh + $calculations->consumption_kWh;
$sum_consumption_kWh_supply_fee_costs 	 = $sum_consumption_kWh_supply_fee_costs + $calculations->consumption_kWh_supply_fee_costs;
$sum_consumption_kWh_distr_fee_costs 	 = $sum_consumption_kWh_distr_fee_costs + $calculations->consumption_kWh_distr_fee_costs;
$sum_consumption_kWh_transport_fee_costs = $sum_consumption_kWh_transport_fee_costs + $calculations->consumption_kWh_transport_fee_costs;
// Data for graph.
$real_consumption_volume[$calculations->date] .= number_format($calculations->daily_consump_volume, 2);
?>


<?php
if (isset($schema_change_date)) {
?>
	<tr class="table-row-pricelist">
		<td class="gas-table__td">Cenník</td>
		<td class="gas-table__td"><?php if (isset($schema_change_date)) {
										echo date_format_converter($schema_change_date);
										$schema_change_date = null;
									} ?></td>
		<td class="gas-table__td" colspan="9"></td>
	</tr>
<?php
}
?>
<tr>
	<td><?php echo $calculations->order_number; ?></td>
	<td><?php echo $calculations->date; ?></td>
	<td><?php echo $calculations->days; ?></td>
	<td><?php echo number_formater($calculations->current_reading); ?></td>
	<td class="dark-layer text-right">
		<a href="#" class="table-link tooltip">
			<!-- CSS Tooltip -->
			<?php echo number_formater($calculations->consumption_volume); ?>
			<span>
				<!-- <img class="callout" src="public/images/arrow.gif" /> -->
				<?php echo number_formater($sum_consumption_volume); ?>
			</span>
		</a>
	</td>
	<td class="dark-layer text-right">
		<a href="#" class="table-link tooltip">
			<!-- CSS Tooltip -->
			<?php echo number_formater($calculations->real_gas_consumption_volume); ?>
			<span>
				<?php echo number_formater($sum_real_gas_consumption_volume); ?>
			</span>
		</a>
	</td>
	<td class="dark-layer text-right">
		<a href="#" class="table-link tooltip">
			<!-- CSS Tooltip -->
			<?php echo number_formater($calculations->consumption_kWh); ?>
			<span>
				<?php echo number_formater($sum_consumption_kWh); ?>
			</span>
		</a>
	</td>
	<td class="dark-layer text-right">
		<a href="#" class="table-link tooltip">
			<!-- CSS Tooltip -->
			<?php echo number_formater($calculations->consumption_kWh_supply_fee_costs); ?>
			<span>
				<?php echo price_formater($calculations->dph * ($sum_consumption_kWh_supply_fee_costs +
					$sum_consumption_kWh_distr_fee_costs +
					$sum_consumption_kWh_transport_fee_costs + $calculations->monthly_fees)) . " s DPH"; ?>
			</span>
		</a>
	</td>
	<td><?php echo number_formater($calculations->daily_consump_volume); ?></td>
	<td><?php echo number_formater($calculations->daily_consump_kWh); ?></td>
	<td><?php echo number_formater($calculations->dph * $calculations->daily_consump_money); ?></td>
</tr>