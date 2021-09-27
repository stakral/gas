<?php
require_once(LIB_PATH.DS.'database.php');

class User extends DatabaseObject
{
	protected static $table_name="admins";
	protected static $db_fields = array('id', 'name', 'surname', 'user_name', 'pracovisko', 'razitko', 'hashed_password');


    public $id;
    public $name;
    public $surname;
    public $user_name;
    public $pracovisko;
    public $razitko;
    public $hashed_password;


    public function find_by_username($username="")
    {    
		return $this->db->getOne("SELECT * FROM ".static::$table_name." WHERE user_name = :username LIMIT 1", [ 'username' => $username ]);
  	}


    public function full_name($obj)
    {
        if (isset($obj->name) && isset($obj->surname))
        {
            return $obj->name . " " . $obj->surname;
        } else {
            return "";
        }
    }


	/**
	 * Generating user initials
	 *
	 * @param $id
	 * @return mixed
	 */    
    public function current_user_initials($id)
    {
        if (is_int($id))
        {
            $current_user = $this->find_by_id($id);// print_r($current_user); die();      
                $n = mb_substr($current_user->name,0,1, 'UTF8');
                $s = mb_substr($current_user->surname,0,1, 'UTF8');
                $r = $current_user->razitko;
                $current_otk_initials=$n.$s." ".$r;
                return $current_otk_initials; // the output will be i.e.: "SK 23"
        } else {
            return "";
        }
    }


    public function current_otk_initials()
    {
        if (isset($_SESSION['otk_id']))
        {
            $current_user = $this->find_by_id($_SESSION['otk_id']);// print_r($current_user); die();
            /*  
            If we would finished here by: "return $current_user; // the output is an object."
            Then if we'd call: "echo ($current_user->name)" the output would be i.e. "Stanislav"
            */      
            $n = mb_substr($current_user->name,0,1, 'UTF8');
            $s = mb_substr($current_user->surname,0,1, 'UTF8');
            $r = $current_user->razitko;
            $current_otk_initials=$n.$s." ".$r;
            return $current_otk_initials; // the output will be i.e.: "SK 23"
        } else {
            return "";
        }
    }


    public function current_otk()
    {
        if (isset($_SESSION['otk_id']))
        {
          $current_otk = $this->find_by_id($_SESSION['otk_id']);
          return $current_otk; // the output is an object.
        }
    }


    public static function authenticate($username="", $password="")
    {
        global $database;
        $user_name = $username;
        $hashed_password = $password;
    
        $sql  = "SELECT * FROM admins ";
        $sql .= "WHERE user_name = :username' "; /// Mind the column name!!!
        $sql .= "AND hashed_password = :password' ";
        $sql .= "LIMIT 1";
        echo $sql;
    
    //  $query = $db->prepare("SELECT * FROM ".static::$table_name." ORDER BY $column $order");
        $query = $db->prepare($sql);
    
        $query->execute([
            'username' => $username,
            'password' => $password
        ]);
        // return static::find_by_sql($query);
    
        $result_array = self::find_by_sql($query);
        return !empty($result_array) ? array_shift($result_array) : false;
    }
    
    
}




?>