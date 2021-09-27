<?php

require_once(LIB_PATH . DS . 'database.php');

class Price_schema extends DatabaseObject
{
    protected static $table_name = "price_schemas";
    protected static $db_fields = [
        'id',
        "tarif",
        'season_name',
        'start_date',
        'start_value',
        'volume_coeficient',
        'avg_combust_heat',
        'monthly_fee',
        'unit_price_kWh',
        'distr_fee_kWh',
        'transport_fee_kWh',
        'monthly_fee_distr',
        'monthly_fee_supply',
        'month_fee_emergency',
        'supply_fee_kWh',
        'dph',
        'established',
        'advance_payment'

    ];

    public $id;
    public $tarif;
    public $season_name;
    public $start_date;
    public $start_value;
    public $volume_coeficient;
    public $avg_combust_heat;
    public $monthly_fee;
    public $unit_price_kWh;
    public $distr_fee_kWh;
    public $transport_fee_kWh;
    public $monthly_fee_distr;
    public $monthly_fee_supply;
    public $month_fee_emergency;
    public $supply_fee_kWh;
    public $dph;
    public $established;
    public $advance_payment;

    /*
     * FIND BY SEASON NAME
     * Method selects a schema by specified season name.
     *
     * The "if" statement was tipically used in case when a new season starts (1st August)
     * but a new schema with correct season_name didn't exist yet
     * so the schema established for previous seasom is still valid.
     * (Used in season_add_change.php)
     */
    public function find_by_season_name($season_name)
    {
        $result = $this->db->getOne("SELECT * FROM " . static::$table_name . " WHERE season_name = :season_name ORDER BY established DESC", ['season_name' => $season_name]);
        // if (!$result) {
        //     $result = $this->db->getOne("SELECT * FROM " . static::$table_name . " ORDER BY established DESC LIMIT 1");
        // }
        return $result;
    }

    /*
     * FIND PRICE SCHEMA FOR SPECIFIED SEASON
     * Method selects schemas for specified season based on "established" column values
     * and fixed official season start and end dates,
     * it makes this method always providing correct set of schemas.
     */
    public function find_price_schemas_for_specified_season($season_name)
    {
        $a = explode("-", $season_name); // Converts yyyy-yyyy into array [yyyy, yyyy];
        $date_min = $a[0] - 1 . "-07-31"; // Although the date_min is usually $a[0] . "-01-01", this is more secure.
        $date_max = $a[1] . "-07-31";

        $data  = [
            'date_min' => $date_min,
            'date_max' => $date_max
        ]; //print_r($data); die();

        $query = "SELECT * FROM " . static::$table_name . " WHERE established > :date_min  AND established < :date_max ORDER BY established"; //echo $query; die();
        $query_result = $this->db->getAll($query, $data);
        return $query_result;
    }
}
