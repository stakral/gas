<?php

class Odpocty extends DatabaseObject
{
	protected static $table_name = "odpocty";
	protected static $db_fields = array('id', 'reading', 'date', 'season');

	public $id;
	public $reading;
	public $date;
	public $season;


	/**
	 * Find All gas state readings by season.
	 *
	 * @return mixed
	 */
	public function find_all_by_season($season)
	{
		$data = ['season' => $season];

		$query_results = $this->db->getAll("SELECT * FROM " . static::$table_name . " WHERE season = :season ORDER BY id ASC", $data);

		return $query_results;
	}


	/**
	 * Separate readings for specified price schemas.
	 *
	 * @return mixed
	 */
	public function separate_readings_for_specified_price_schemas($gas_state_readings)
	{
		$a = explode("-", $this->season); // Converts yyyy-yyyy into [yyyy, yyyy];
		$date_min = $a[0] - 1 . "-07-31";
		$date_max = $a[1] . "-07-31";

		$data  = [
			'date_min' => $date_min,
			'date_max' => $date_max
		]; //print_r($data); die();

		$query = "SELECT * FROM " . static::$table_name . " WHERE established > :date_min  AND established < :date_max"; //echo $query; die();
		$query_result = $this->db->getAll($query, $data);
		return $query_result;
	}
}
