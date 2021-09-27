<?php
// A class to help work with Sessions
// In our case, primarily to manage logging users in and out

// Keep in mind when working with sessions that it is generally 
// inadvisable to store DB-related objects in sessions

class Session {
	
    private $logged_in=false;
    public  $otk_id;

    public  $username;
    public  $name;
    public  $surname;
    public  $pracovisko;
    public  $razitko;

    public  $message;
    public  $temp_protocol;
    public  $current_season;


    function __construct() {
        session_save_path("/Users/kingus/tmp"); 
        /*
        I added the line above after Homebrew Apache and PHP installation
        as there was an error message regarding session writing permissions
        using PHP versions 5.5, 5.6, 7.0, 7.1 but after uncomenting: session.save_path = "/tmp"
        in php.ini it's not needed any more.
        */
        session_start();
        $this->check_message();
        $this->check_login();

        if ($this->logged_in) {
        // actions to take right away if user is logged in
        } else {
        // actions to take right away if user is not logged in
        }
    }
    
    /*
    public function temp_protocol_data_storage($protocol_object){

    }
    */

    public function is_logged_in() {
        return $this->logged_in;
    }


	public function login($user) {
        // database should find user based on username/password
        if ($user) {
            $this->otk_id     = $_SESSION['otk_id'] = $user->id;
            $this->name       = $_SESSION['name'] = $user->name;
            $this->surname    = $_SESSION['surname'] = $user->surname;
            $this->pracovisko = $_SESSION['pracovisko'] = $user->pracovisko;
            /*      
            $this->user_name  = $_SESSION['user_name'] = $user->user_name;
            $this->razitko    = $_SESSION['razitko'] = $user->razitko;
            */    
            $this->logged_in  = true;
        }
    }


    public function logout() {
        unset($_SESSION['otk_id']);
        unset($this->otk_id);
        unset($_SESSION['name']);
        unset($this->name);
        unset($_SESSION['surname']);
        unset($this->surname);
        unset($_SESSION['pracovisko']);
        unset($this->pracovisko);
        /*    
        unset($_SESSION['user_name']);
        unset($this->user_name);
        unset($_SESSION['razitko']);
        unset($this->razitko);
        */
        // unset($_SESSION['first_name']);
        // unset($this->first_name);
        // unset($_SESSION['last_name']);
        // unset($this->last_name);
        // unset($_SESSION['username']);
        // unset($this->username);
        // unset($_SESSION['message_local']);
        // unset($this->message_local);
        // unset($_SESSION['admin_id']);
        // unset($this->admin_id);

        $this->logged_in = false;
    }
    
    
    // this function has a dual duty - SET message and GET message
    public function message($msg="") {
        if (!empty($msg)) {
            // then this is "set message"
            // make sure you understand why $this->message=$msg wouldn't work
            $_SESSION['message'] = $msg;
        } else {
            // then this is "get message"
                return $this->message;
        }
    }


    private function check_login() {
        if (isset($_SESSION['otk_id'])) {
            $this->user_id = $_SESSION['otk_id'];
            $this->logged_in = true;
        } else {
            unset($this->otk_id);
            $this->logged_in = false;
        }
    }


	private function check_message() {
		// Is there a message stored in the session?
		if (isset($_SESSION['message'])) {
			// Add it as an attribute and erase the stored version
            $this->message = $_SESSION['message'];
            unset($_SESSION['message']);
        } else {
            $this->message = "";
        }
	}
	
}

$session = new Session();
$message = $session->message();

?>