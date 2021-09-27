<?php
// The code of this file is included in tbody element of home.php.

$price_schemas_for_specified_season = $price_schema->find_price_schemas_for_specified_season($current_season_name); //print_r($price_schemas_for_specified_season); die();
$number_of_schemas = count($price_schemas_for_specified_season); // echo $number_of_schemas;
$official_dates = season_official_start_end_dates($current_season_name); // print_r($official_dates);
$current_season_official_start_date = $official_dates->season_start_date; // echo $current_season_official_start_date . "<br>";
$current_season_official_end_date = $official_dates->season_end_date; // echo $current_season_official_end_date . "<br>";

if ($number_of_schemas < 1) :

    echo "Sorry, but there's no price schema.";

elseif ($number_of_schemas >= 1) :

    for ($k = 0; $k < $number_of_schemas; $k++) :

        $current_season_schema_established_date[$k] = $price_schemas_for_specified_season[$k]->established; //echo $current_season_schema_established_date[$k] . "<br>";
        $current_season_schema = $price_schemas_for_specified_season[$k]; //print_r($current_season_schema); //echo "<br>"; die();
        if ($number_of_schemas > 1) {
            $prev_season_schema = $price_schemas_for_specified_season[$k - 1]; //echo $prev_season_schema->monthly_fee_distr;
        } else {
            $prev_season_schema = 0;
        }



        /* Monthly fees calculation. */

        $current_date                           = date("Y-m-d");
        $prev_season_schema_established_date    = $price_schemas_for_specified_season[$k - 1]->established;
        $current_season_schema_established_date = $price_schemas_for_specified_season[$k]->established;
        $next_season_schema_established_date    = $price_schemas_for_specified_season[$k + 1]->established;
        $last_season_schema_established_date    = $price_schemas_for_specified_season[$number_of_schemas - 1]->established;

        $current_season_monthly_fee_distr       = $current_season_schema->monthly_fee_distr;
        $current_season_monthly_fee_supply      = $current_season_schema->monthly_fee_supply;
        $current_season_monthly_fee             = $current_season_monthly_fee_supply + $current_season_monthly_fee_distr;

        $prev_season_monthly_fee_distr          = $prev_season_schema->monthly_fee_distr;
        $prev_season_monthly_fee_supply         = $prev_season_schema->monthly_fee_supply;
        $prev_season_monthly_fee                = $prev_season_monthly_fee_supply + $prev_season_monthly_fee_distr;
        /*
        // Outputs of variables for "Monthly fees calculation created above.
        echo "============================<br>";
        echo "pssed ". $prev_season_schema_established_date . "<br>";
        echo "cssed: ". $current_season_schema_established_date . "<br>";
        echo "nssed: ". $next_season_schema_established_date . "<br>";
        echo "lssed: ". $last_season_schema_established_date . "<br>";

        echo "cs_distr_fee: ". $current_season_monthly_fee_distr . "<br>";
        echo "cs_monthly_fee: ". $current_season_monthly_fee_supply . "<br>";
        echo "<br>" . $current_season_schema_established_date . " current schema monthly fee: ". $current_season_monthly_fee . "<br>"; // + $current_season_schema->month_fee_emergency;
        echo $prev_season_schema_established_date . " prev schema monthly fee: ". $prev_season_monthly_fee . "<br>";
        */

        /* 
         * Case when a new season starts (usually 01 August each year) till another schema is established.
         */
        if ($k == 0) {
            $months = dateDifference($current_season_official_start_date, $next_season_schema_established_date, $differenceFormat = '%m');
            $monthly_fees = $months * $current_season_monthly_fee;
            $current_schema_monthly_fees_sum = $current_schema_monthly_fees_sum + $monthly_fees;
            /*
            // Outputs of variables created above.
            echo "first IF: k = " . $k . "<br>";// die();
            echo "monthly_fees for k = " . $k . " is ". $monthly_fees . " € <br>";
            echo "current_schema_monthly_fees_sum: ". $current_schema_monthly_fees_sum . "<br>";
            */
        }

        /* 
         * Case when there's multiple schemas for the same year (very rare).
         */ elseif ($k > 0 && ($number_of_schemas - $k > 1)) {
            $months = dateDifference($current_season_schema_established_date, $next_season_schema_established_date, $differenceFormat = '%m');
            $prev_schema_monthly_fees_sum = $prev_season_monthly_fee * $months + $prev_schema_monthly_fees_sum;
            $monthly_fees = $current_season_monthly_fee * $months;
            $current_schema_monthly_fees_sum = $prev_schema_monthly_fees_sum + $monthly_fees;
            /*
            // Outputs of variables created above.
            echo "second IF: k = " . $k . "<br>";// die();
            echo "prev_schema_monthly_fees_sum: ". $prev_schema_monthly_fees_sum . "<br>";
            echo "elseif_2 months: ". $months . "<br>";
            echo "monthly_fees for k = " . $k . " is ". $monthly_fees . " € <br>";
            echo "current_schema_monthly_fees_sum: ". $current_schema_monthly_fees_sum . "<br>";
            */
        }

        /* 
         *  Case when the current season schema is the last added one (ussually after a new year).
         */ elseif (($number_of_schemas - 1) - $k == 0) {
            $months = dateDifference($last_season_schema_established_date, $current_date, $differenceFormat = '%m months and %y years');
            $prev_schema_monthly_fees_sum = $prev_season_monthly_fee * $months + $prev_schema_monthly_fees_sum;
            $monthly_fees = $current_season_monthly_fee * $months;
            $current_schema_monthly_fees_sum = $current_schema_monthly_fees_sum + $monthly_fees;
            /*
            // Outputs of variables created above.
            echo "third IF: k =  " . $k .  "<br>";// die();
            echo "prev_schema_monthly_fees_sum: ". $prev_schema_monthly_fees_sum . "<br>";
            echo "elseif_3 months: ". $months . "<br>";
            echo "monthly_fees for k = " . $k . " is ". $monthly_fees . " € <br>";
            echo "current_schema_monthly_fees_sum: ". $current_schema_monthly_fees_sum . " (Note: Completed months only!)<br>";
            */
        }



        /* Generating table rows with calculated values */

        // "Schema change date" (established) of each "price schema". Used in "table_row.php" to identify a date when a new schema was established.
        $schema_change_date = $current_season_schema_established_date; // echo $schema_change_date;

        for ($i = 0; $i <= $number_of_readings - 1; $i++) {
            // Setting new variable.
            $reading_date = $gas_meter_readings[$i]->date; //print_r($reading_date);echo "<br>";//die();

            if (($reading_date > $current_season_schema_established_date) && (($reading_date < $next_season_schema_established_date) || !$next_season_schema_established_date)) {
                // The ternary operator defines different start date for the first season schema $k=0 and the others.
                $k == 0 ? $start_date = $current_season_official_start_date : $start_date = $current_season_schema_established_date;
                $current_months_count = dateDifference($start_date, $gas_meter_readings[$i]->date, $differenceFormat = '%m') + 1;
                $monthly_fees = $current_months_count * $current_season_monthly_fee  + $prev_schema_monthly_fees_sum; // echo "11: ". $monthly_fees;
                include "public/table_row.php";
                /*
                echo $current_months_count .". mesiac_X = ";
                echo $calculations->monthly_fees . "<br>";
                */
            } elseif ($reading_date >= $last_season_schema_established_date && ($k == $number_of_schemas - 1)) {
                $current_months_count = dateDifference($current_season_schema_established_date, $gas_meter_readings[$i]->date, $differenceFormat = '%m') + 1;
                $monthly_fees = $current_months_count * $current_season_monthly_fee + $prev_schema_monthly_fees_sum; // echo "22: ". $monthly_fees;
                include "public/table_row.php";
                /*
                echo $current_months_count .". mesiac_Y = ";
                echo $calculations->monthly_fees . "<br>";
                */
            }
        }

    endfor;

endif;

// The "sum" values are defined in included file table_row.php.
$total_consumption_incl_monthly_supply_fees = ($sum_consumption_kWh_supply_fee_costs + $monthly_fees);
$total_kWH_distr_and_transport_fees = ($monthly_distr_fees + $sum_consumption_kWh_distr_fee_costs + $sum_consumption_kWh_transport_fee_costs);
$total_gas_and_fees = $total_consumption_incl_monthly_supply_fees + $total_kWH_distr_and_transport_fees;
$total_gas_and_fees_incl_vat = $total_gas_and_fees * 1.2;

// Storing total fee value for calculating "credit" on home.php.
$_SESSION['current_total_fees_incl_dph'] = $total_gas_and_fees_incl_vat;
