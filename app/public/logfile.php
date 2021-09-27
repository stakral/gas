<?php require_once("../includes/initialize.php"); ?>
<?php if (!$session->is_logged_in()) { redirect_to("./admin/login.php"); } ?>

<?php include_layout_template('header.php'); ?>
<?php include_layout_template('nav_bar.php'); ?>

<body style="margin-top:50px;">
<?php

$log_file = "/Users/kingus/Sites/otk/logs/log.txt";

// Reading the file
// Using the block of code below would display old records first - "normal way"
/*
if ($handle =fopen($log_file, 'r')){
	$content = fread($handle, filesize($log_file));
	fclose($handle);
}
echo $content; // This wouldn't display line endings
echo "<br />";
echo nl2br($content);
*/


// This block of code puts records in reversed order - new ones are the first
$content_array = file("$log_file"); // "file()" Reads entire file into an array.

$content_array_reversed = array_reverse($content_array);
//print_r($content_array_reversed);
foreach($content_array_reversed as $content_reversed){
		echo $content_reversed."<br />";
}

/* This block of code was moved to functions.php
echo "<br />";

// Here we remove the last row if exceedes allowed number of lines.
// Reading saved log file.
$logfile = "/Users/kingus/Sites/otk/logs/log.txt";
	if($handle = fopen($logfile, 'r')) {
	  	$content = fread($handle, filesize($logfile));
	  	fclose($handle);
	}
//echo $content;
echo "Array format:<br />";
//echo nl2br($content);
//$a=nl2br($content);// echo $a;

//print_r($content);// string
	$array=file($logfile); print_r($array);
	$lines_before=count($array);
	echo "<br /><br />"."Pocet riadkov pred: ".$lines_before."<br /><br />";

	$arr="";
	$max=10;
	if($lines_before>$max){$arr=array_shift($array);
		echo "Odstraneny prvok: ".$arr."<br /><br />";
		$lines_after=count($array);
		print_r($array);
		echo "<br /><br />"."Pocet riadkov po: ".$lines_after."<br /><br />";
//	 	$implods = implode('', $array); echo($implods);

//		$i=$lines_before-$lines_after;
	 	if($handle = fopen($logfile, 'w')) { 
	 	foreach($array as $value){
	    fwrite($handle, $value);
//	    fclose($handle);
}}
	}else{
		echo "Pocet prvkov nepresiahol dovoleny pocet t.j. ".$max.".<br />";
	}

//    $arr = implode('\n', $array); print_r($arr);
 //   $b=count($arr);// echo $b."<br />";

*/
?>
<?php // Below are just some experiments
/*
$score = 10;
$age = 20;
echo 'Taking into account your age and score, you are: ',($age > 10 ? ($score < 80 ? 'behind' : 'above average') : ($score < 50 ? 'behind' : 'above average')); // returns 'You are behind'
echo "<br />";
echo 'Taking into account your age and score, you are: ';
if($age > 10){if($score < 80){echo'behind';}else{echo 'above average';}}else{if($score < 50 ){echo 'behind';}else{echo 'above average';}};

echo"<br />";
//$a=16;
//if $a=

$w=(int)date('W', strtotime('2016-01-01'));
$m=(int)date('n', strtotime('2016-01-01'));
$week = $w==1?($m==12?53:1):($w>=51?($m==1?0:$w):$w);
echo $week."<br />";
echo strtotime('2016-01-01');

echo"<br />";

// Defining function
function whatIsToday(){
	$year=date('y', time());
    echo "Today is " . $year;
}
// Calling function
whatIsToday();

/* Toto je blud!
$year=date('y', time());
$etalon=0;
    if($etalon !== $year){$reset count()}
*/



?>
</body>
