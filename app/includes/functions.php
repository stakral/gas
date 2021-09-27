<?php

/**
 * Show 404
 *
 * Sends 404 not found header
 * And shows 404 HTML page
 *
 * @return void
 */
function show_404()
{
    header("HTTP/1.0 404 Not Found");
    include_once "404.php";
    die();
}


/**
 * Get Segments
 *
 * From a url like http://example.com/edit/5
 * it creates an array of URI segments [ edit, 5 ]
 *
 * @return array
 */
function get_segments()
{
    $current_url = "http"
        . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "s://" : "://")
        . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    //return $current_url; die();
    $path = str_replace(BASE_URL, '', $current_url); // removing BASE_URl from URL.
    // Getting rid of unecessary slashes to avoid empty array segments.
    $path = trim(parse_url($path, PHP_URL_PATH), '/'); // just to make shure it's formated properly.

    $segments = explode('/', $path);
    return $segments;
}


/**
 * GET POST LINK
 *
 * Create link to post [ /post/:id/:slug ]
 *
 * @param $post
 * @return mixed|string
 * $type may be post, edit, delete
 */
function get_post_link($protocol, $type)
{
    if (is_object($protocol)) {
        $id   = $protocol->id;
        // $slug = $protocol->slug;
    } else {
        $id   = $protocol['id'];
        // $slug = $protocol['slug'];
    }
    $link = BASE_URL . "/$type/$id";

    // // slug sa prida iba ak type je post
    // if ($type === 'post')
    // {
    // 	$link .= "/$slug";
    // }
    $link = filter_var($link, FILTER_SANITIZE_URL);

    return $link;
}


/**
 * Segment
 *
 * Returns the $index-th URI segment
 *
 * @param $index
 * @return string or false
 */
function segment($index)
{
    $segments = get_segments();
    // The "-1" just compensates the fact that arrays are zero indexed and we want to count from 1.
    return isset($segments[$index - 1]) ? $segments[$index - 1] : false;
}


/**
 * Go_to_URL_by_route
 *
 * Returns URL using route
 *
 * @param $route
 * @return string or false
 */
function go_to_URL_by_route($route)
{
    if (isset($route)) {
        $target_url = BASE_URL . "/" . $route;
        return $target_url;
    } else {
        return false;
    }
}


function log_action($action, $message = "")
{
    //  $logfile = "../../logs/log.txt";
    $logfile = SITE_ROOT . "/logs/log.txt";
    //  $new = file_exists($logfile) ? false : true;
    //  if $logfile does exist then $new=false - this step was not necessary
    if ($handle = fopen($logfile, 'a+bt')) { // append
        $timestamp = strftime("%Y-%m-%d %H:%M:%S", time());
        $content = "{$timestamp} | {$action} {$message}\n";
        fwrite($handle, $content);
        fclose($handle);
        // if($new) { chmod($logfile, 0755); }
    } else {
        echo "Could not open log file for writing.";
    }
}

/**
 * LOG TRIMMER
 * Keeps max number of logged lines.
 */
function log_trimmer()
{
    // Reading saved log file.
    $logfile = SITE_ROOT . "/logs/log.txt";
    if ($handle = fopen($logfile, 'r')) {
        $content = fread($handle, filesize($logfile));
        fclose($handle);
    }
    // Converting log content into array.
    $array = file($logfile);
    // Counting lines of the array
    $lines_before = count($array);
    // Defining max log lines number
    $arr = "";
    $max = 100;

    if ($lines_before > $max) {
        $arr = array_shift($array);
        // Counting lines after array_shift
        $lines_after = count($array);
        // Writing trimmed array to log.txt
        if ($handle = fopen($logfile, 'w')) {
            foreach ($array as $value) {
                fwrite($handle, $value);
                // fclose($handle); // Doesn't work!!! Deletes all records.
            }
        }
    }
}

function strip_zeros_from_date($marked_string = "")
{
    // first remove the marked zeros
    $no_zeros = str_replace('*0', '', $marked_string);
    // then remove any remaining marks
    $cleaned_string = str_replace('*', '', $no_zeros);
    return $cleaned_string;
}

function redirect_to($location = NULL)
{
    if ($location != NULL) {
        header("Location: {$location}");
        exit;
    }
}

function message()
{
    if (isset($_SESSION["message"])) {
        //$output = "<div class=\"message\">";
        $output = htmlentities($_SESSION["message"]);
        //$output .= "</div";

        $_SESSION["message"] = null; // Clear message after use
        return $output;
    }
}

function message_local()
{
    if (isset($_SESSION["message_local"])) {
        //$output = "<div class=\"message\">";
        $output = htmlentities($_SESSION["message_local"]);
        //$output .= "</div";

        $_SESSION["message_local"] = null; // Clear message after use
        return $output;
    }
}

function output_message($message = "")
{
    if (!empty($message)) {
        // return "<p class=\"seek\">{$message}</p>"; // "p class" is changed from message to seek
        return $message;
    } else {
        return "";
    }
}

/**
 * FORM ERRORS
 * Displays errors formated as "ul".
 */
function form_errors($errors = array())
{
    $output = "";
    if (!empty($errors)) {
        $output .= "<div class=\"error\">";
        $output .= "Odstráňte tieto nedostatky:";
        $output .= "<ul>"; // <ul> unordered list
        foreach ($errors as $key => $error) {
            $output .= "<li>";
            $output .= htmlentities($error);
            $output .= "</li>"; // <li> list
        }
        $output .= "<ul>";
        $output .= "</div>";
    }
    return $output;
}

/**
 * FORMATED DIV ERROR
 * Displays errors formated as styled "div".
 */
function formated_div_error($errors = array(), $field_name)
{
    $output = "";
    if (!empty($errors)) {
        $output .= "<div class=\"errors\">";
        // $output.= "Odstráňte tieto nedostatky:";
        // $output.= "<ul>"; // <ul> unordered list
        // foreach($errors as $key=>$error){
        // $output.= "<li>";
        // $output.= htmlentities($error);
        // $output.= "</li>"; // <li> list
        // }
        // $output.= "<ul>";
        if (isset($errors[$field_name])) {
            $output .= $errors[$field_name];
        }
        $output .= "</div>";
    }
    return $output;
}

/**
 * INPUT ERROR BORDER
 * Returns class attribute value to style HTML element.
 * @param $errors
 * @param $field_name - the input field name.
 * @return string
 */
function input_error_border($errors = array(), $field_name)
{
    if (!empty($errors)) {
        $error_class = null;
        if (isset($errors[$field_name])) {
            $error_class = "error-border";
        } else {
            $error_class = "no";
        }
    }
    return $error_class;
}

/*
  Function 'MyAutoloader' is simply renamed deprecated '__autoload' function.
  To load Classes instead of depricated '__autoload' is used 'spl_autoload_register' function.
  See: https://stackoverflow.com/questions/7651509/what-is-autoloading-how-do-you-use-spl-autoload-autoload-and-spl-autoload-re
*/
function myAutoloader($class_name)
{
    $class_name = strtolower($class_name);
    $path = LIB_PATH . DS . "{$class_name}.php";
    if (file_exists($path)) {
        require_once($path);
    } else {
        die("The file {$class_name}.php could not be found.");
    }
}

spl_autoload_register('myAutoloader');

function include_layout_template($template = "")
{
    include(SITE_ROOT . DS . 'public' . DS . 'layouts' . DS . $template);
}
/*
function log_action($action, $message="") {
	$logfile = SITE_ROOT.DS.'logs'.DS.'log.txt';
	$new = file_exists($logfile) ? false : true;
    if($handle = fopen($logfile, 'a')) { // append
        $timestamp = strftime("%Y-%m-%d %H:%M:%S", time());
        $content = "{$timestamp} | {$action}: {$message}\n";
        fwrite($handle, $content);
        fclose($handle);
    if($new) {
        chmod($logfile, 0755); }
    } else {
        echo "Could not open log file for writing.";
    }
}
*/

function datetime_to_text($datetime = "")
{
    $unixdatetime = strtotime($datetime);
    return strftime("%B %d, %Y at %I:%M %p", $unixdatetime);
}

function budget_date($date_interface)
{
    $date = date_create($date_interface);
    $formated_date = date_format($date, "d.m.Y");
    return $formated_date;
}

// PLACE OF WORK

function workplace_first_letter($location)
{
    $p = substr($location, 0, 1);
    return $p;
}

// This function was replaced by "wrkplace" method in User class.
/* 
function workplace($user_id){
//    $current_user = User::find_by_id($_SESSION['otk_id']);// print_r($current_user);
    $current_user = find_by_id($user_id);// print_r($current_user);
    $place = $current_user->pracovisko; //echo $place;
    $p = substr($place,0,1);
    return $p;
}


function local_items(){
    if (workplace()=="V"){
        $items = Sortiment::find_local_items_v();
    } else {
        $items = Sortiment::find_local_items_o();
    }
    return $items;
}
*/


// function local_suppliers($work_place){
//     if ($work_place == "V"){
//         $suppliers = Supplier::find_local_suppliers_v();
//     }else{
//         $suppliers = Supplier::find_local_suppliers_o();
//     }
//     return $suppliers;
// }


// HASHING PASSWORD

function password_encrypt($password)
{
    $hash_format = "$2y$10$"; // Tells PHP to use Blowfish with the cost of 10
    $salt_length = 22; // Blowfish salts should be 22 characters or nore
    //    $salt = "YmEzNzkzY2NjOGQwYzI3O."; // Just for the testing purposes.
    $salt = generate_salt($salt_length);
    $format_and_salt = $hash_format . $salt;
    $hash = crypt($password, $format_and_salt);
    return $hash;
}

function generate_salt($length)
{
    // Not 100% unique, not 100% random but good enough for the salt
    // MD5 returns 32 characters
    $unique_random_string = md5(uniqid(mt_rand(), true));

    // Valid characters for salt are [a-zA-Z0-9./]
    $base64_string = base64_encode($unique_random_string);

    // But not "+" which is valid in 64base encoding
    $modified_base64_string = str_replace('+', '.', $base64_string);

    // Truncate string to the current length
    $salt = substr($modified_base64_string, 0, $length);
    return $salt;
}

function password_check($password, $existing_hash)
{
    // existing hash contains format and salt at start
    $hash = crypt($password, $existing_hash);
    if ($hash === $existing_hash) {
        return true;
    } else {
        return false;
    }
}

function attempt_login($user, $username, $password)
{
    // $found_user = find_user_by_username($username);
    $found_user = $user->find_by_username($username);
    if ($found_user) {
        // found admin, now check password
        // if (password_check($password, $found_user["hashed_password"])){
        if (password_check($password, $found_user->hashed_password)) {   // pasword matches
            return $found_user;
        } else {
            // password doesn't match
            return false;
        }
    } else {
        // admin not found
        return false;
    }
}
