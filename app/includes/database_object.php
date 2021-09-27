<?php
require_once("initialize.php");


class DatabaseObject
{

	/*
	 * Common Database Methods.
	 * These methods are taken from User class as they can be inherited by all
	 * other classes which need to do DB CRUD.
	 * It works on PHP 5.3 and highier as it requires "Late static bindings".
	 * These methods are just copied from User class and "return self::" was changed to "return static::"
	 * the "static::" here is as the scope resolution operator asking for the LATE STATIC BINDING.
	 */

	protected $db;
	/**
	 * Our class needs a DB object to work
	 *
	 * @param DB $db
	 */

	public function __construct(Database $db)
	{ // DB je nazov classu (nepovinne).
		$this->db = $db;
	}


	/**
	 * Find All
	 *
	 * @return mixed
	 */
	public function find_all()
	{
		return $this->db->getAll("SELECT * FROM " . static::$table_name, []);
	}


	/**
	 * Get user by $id
	 *
	 * @param $id
	 * @return mixed
	 */
	public function find_by_id($id)
	{
		return $this->db->getOne("SELECT * FROM " . static::$table_name . " WHERE id = :id", ['id' => $id]);
	}


	/**
	 * Get object's $ids
	 *
	 * @param $id
	 * @return mixed
	 */
	public function get_object_IDs()
	{
		$data = [];
		$query = "SELECT id FROM " . static::$table_name;

		return $this->db->query($query, $data)->fetchAll(PDO::FETCH_NUM);

		$stmt = $this->db->prepare($query);
		$stmt->execute();

		// set the resulting array to associative
		$result = $stmt->setFetchMode(PDO::FETCH_NUM);

		return $results;
	}


	/*
	 * Function ATTRIBUTES.
	 * Returns array of attribute names and their values for the current object.
	 */
	protected function attributes()
	{
		$attributes = array();
		foreach (static::$db_fields as $field) {
			if (property_exists($this, $field)) {
				$attributes[$field] = $this->$field;
			}
		}
		return $attributes;
	}


	public function save($obj) //$a=$this->id;  return $a; die();
	{
		// A new record doen't have an id yet.
		return isset($obj->id) ? $this->update($obj) : $this->create($obj);
	}


	public function create($obj) //echo "Rakatak"; die();
	{
		$attributes = $this->attributes(); // print_r($attributes); die();// print_r(array_keys($attributes));
		array_shift($attributes); // We must remove the first element, which is "id".
		$db_columns = array_keys($attributes); // print_r($attributes); die();

		$sql_value_placeholders = array();
		foreach ($attributes as $key => $value) {
			$sql_value_placeholders[] = ":{$key}";
		}

		$data = (array)$obj; // print_r($data); die(); // As we need $data to be an array, we're casting object to an array.

		$sql = "INSERT INTO " . static::$table_name . " (";
		$sql .= implode(", ", $db_columns);
		$sql .= ") VALUES (";
		$sql .= implode(", ", $sql_value_placeholders);
		$sql .= ")"; // echo $sql; die();

		$query_results = $this->db->insert($sql, $data);

		return ($query_results) ? true : false;
	}


	public function update($obj)
	{
		$attributes = $this->attributes(); // print_r($attributes); die();

		// Creating attribute pairs for PDO prepared statement query.
		$attribute_pairs = array();
		foreach ($attributes as $key => $value) {
			$attribute_pairs[] = "{$key} = :{$key}";
		}

		// We need to cast $obj to array to pass values as parameter into updateOne() method.
		$data = (array)$obj; // print_r($data); die();

		$sql = "UPDATE " . static::$table_name . " SET ";
		$sql .= join(", ", $attribute_pairs); // Creating something like this: p_o = :p_o, p_v = :p_v ";
		$sql .= " WHERE id = :id"; // echo $sql; die();
		$query_result = $this->db->updateOne($sql, $data); // print_r($query_result);// die();

		return ($query_result->rowCount() === 1) ? true : false;
	}


	public function delete($obj)
	{
		// I am not using the attribute pairs here as we need only the "id".
		$data = (array)$obj; // Casting object to an array.
		$data = array_slice($data, 0, 1, true);  // We need only the "id" which is the first array element.

		$sql = "DELETE FROM " . static::$table_name;
		$sql .= " WHERE id = :id";
		$sql .= " LIMIT 1";

		$query = $this->db->deleteOne($sql, $data); // print_r($query); die();

		return ($query->rowCount() === 1) ? true : false;

		// NB: After deleting, the instance of User still 
		// exists, even though the database entry does not.
		// This can be useful, as in:
		//   echo $user->first_name . " was deleted";
		// but, for example, we can't call $user->update() 
		// after calling $user->delete().
	}


	public function delete_by_list($item_list)  // Deletes one or multilpe items
	{
		// $item_array = (explode(',', $item_list));// print_r($item_array); die();

		$sql = "DELETE FROM " . static::$table_name . " WHERE id IN ($item_list)"; // print_r($sql); die();

		$query_result = $this->db->deleteOne($sql, []);

		return ($query_result->rowCount()) ? true : false;
	}


	/********************** DB WHITELIST METHODS ***********************/

	/*
	 * This section contains a whitelisting methods to avoid SQL injection.
	 * We need to check dynamic DB tables names, columns and order types.
	 * Those arguments are whitelisted and then we just keep checking if the dynamic values are valid.
	 */

	public function db_tables_whitelist()
	{
		$results = $this->db->query("SHOW TABLES");
		for ($i = 0; $i < $results->rowCount(); $i++) {
			$db_table = $results->fetch(PDO::FETCH_ASSOC); // print_r($db_table); die();
			$db_tables[] = $db_table['Tables_in_' . DB_NAME]; // The "Tables_in_budget" is an Assoc array key GIVEN by fetch() function.
		}
		return $db_tables;
	}

	private function db_table_columns_whitelist()
	{
		$results = $this->db->query("SELECT * FROM " . static::$table_name . " LIMIT 0");
		for ($i = 0; $i < $results->columnCount(); $i++) {
			$db_table_column = $results->getColumnMeta($i);
			$db_table_columns[] = $db_table_column['name'];
		}
		return $db_table_columns;
	}

	private function db_order_by_whitelist($order)
	{
		$order_array = ["ASC", "DESC"];
		if (in_array($order, $order_array)) {
			return true;
		} else {
			return false;
		}
	}

	private function whitelists_check($checked_db_column)
	{
		$db_tables		  = $this->db_tables_whitelist();
		$db_table_columns = $this->db_table_columns_whitelist();
		// echo "ZZZ: " . $checked_db_column . " ZZZ ";
		// print_r ($db_table_columns); //die();
		if (in_array($checked_db_column, $db_table_columns) && in_array(static::$table_name, $db_tables)) {
			return true;
		} else {
			return false;
		}
	}

	/********************** End of DB WHITELIST METHODS ***********************/



	public function find_by_list_ordered($list, $column, $order)
	{
		$whitelists_check_result = $this->whitelists_check($column);
		$order_by_check_result 	 = $this->db_order_by_whitelist($order);

		if ($whitelists_check_result && $order_by_check_result) {
			$data = ['list' => $list];

			$query = "SELECT * FROM " . static::$table_name . " WHERE suciastka IN ('list') = :list ORDER BY $column $order";
			//print_r($query); die();

			$query_result = $this->db->getAll($query, $data);

			return $query_result;
		} else {
			return false;
		}
	}


	public function find_all_ordered($column, $order) // This method is used by sortiment_manage.php and users_manage.php.
	{
		$whitelists_check_result = $this->whitelists_check($column); // print_r($whitelists_check_result); die();
		$whitelist_order_check 	 = $this->db_order_by_whitelist($order);

		if ($whitelists_check_result && $whitelist_order_check) {
			$query_result = $this->db->getAll("SELECT * FROM " . static::$table_name . " ORDER BY $column $order", []);

			return $query_result;
		}
		return false;
	}


	public function find_by_searched($seek_opt, $searched)
	{
		// Used by protocol_searched.php, Attribute names not very semantic!
		$whitelists_check_result = $this->whitelists_check($seek_opt); // print_r($whitelists_check_result); die();

		if ($whitelists_check_result) {
			$data = [
				'searched' => $searched
			];
			$query = "SELECT * FROM " . static::$table_name . " WHERE $seek_opt = :searched ORDER BY id DESC"; //echo $query; die();

			$query_result = $this->db->getAll($query, $data);

			return $query_result;
		}
		return false;
	}


	public function find_by_searched1($seek_opt, $searched, $column, $order = "DESC")
	{ // This method is used by items_filter_searched.php and params_manage.php
		$whitelists_check_result = static::whitelists_check($seek_opt);
		$order_by_check_result 	 = static::db_order_by_whitelist($order);

		if ($whitelists_check_result && $order_by_check_result) {
			$data = ['searched' => $searched];
			$query = "SELECT * FROM " . static::$table_name . " WHERE $seek_opt = :searched ORDER BY $column $order";
			$query_result = $this->db->getAll($query, $data);
		}
		return $query_result;
	}


	public function find_by_searched2($op, $suc_id, $column, $order = "ASC") // This method is used by protocol_export.php
	{
		$whitelists_check_result = $this->whitelists_check($column);
		$order_by_check_result 	 = $this->db_order_by_whitelist($order);

		if ($whitelists_check_result && $order_by_check_result) {
			$data = [
				'op' => $op,
				'suc_id' => $suc_id,
			];
			$query = "SELECT * FROM " . static::$table_name . " WHERE op = :op AND suc_id = :suc_id ORDER BY " . $column . " " . $order . "";
			$query_result = $this->db->getAll($query, $data);
		}
		return $query_result;
	}


	public function find_by_pracovisko($pracovisko = "")
	{
		$location_first_letter = $this->workplace_first_letter($pracovisko);

		$data = ['pracovisko' => $location_first_letter];

		$query_result = $this->db->getAll("SELECT * FROM " . static::$table_name . " WHERE pracovisko = :pracovisko ORDER BY id DESC", $data);

		return $query_result;
	}


	public function find_max_id() //muj napad - used for cloninng params
	{
		$query_result = $this->db->getOne("SELECT MAX(id) AS 'id' FROM " . static::$table_name . " LIMIT 1 ", []);

		return $query_result;
	}




	/* Local items */


	public function find_local_items_v()
	{
		$query_result = $this->db->getAll("SELECT * FROM " . static::$table_name . " WHERE p_v = 'v' ORDER BY suciastka ASC", []);

		return $query_result;
	}


	public function find_local_items_o()
	{
		$query_result = $this->db->getAll("SELECT * FROM " . static::$table_name . " WHERE p_o = 'o' ORDER BY suciastka ASC", []);

		return $query_result;
	}


	public function workplace_first_letter($location)
	{
		$p = substr($location, 0, 1);
		return $p;
	}


	public function local_items($location)
	{
		if ($this->workplace_first_letter($location) == "V") {
			$items = $this->find_local_items_v();
		} else {
			$items = $this->find_local_items_o();
		}
		return $items;
	}
}
