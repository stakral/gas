<?php
$full_name  = ($_SESSION['name'] . " " . $_SESSION['surname']);
$pracovisko = $_SESSION['pracovisko'];
$logged_in_user_id = $_SESSION['otk_id'];

if (!isset($_SESSION['current_season_name'])) {
	$current_season_name = current_season(); // Based on current date.
	$_SESSION['current_season_name'] = $current_season_name;
} else {
	$current_season_name = $_SESSION['current_season_name'];
}
?>




<div class="nav-bar">

	<div class="nav-seg-left">
		<span class="season-name"><a href=<?php echo go_to_URL_by_route('home'); ?>>Fakturačné obdobie <?php echo $current_season_name; ?></a></span>
	</div>

	<nav class="nav-seg-mid">
		<div class="menu-links">
			<a href=<?php echo go_to_URL_by_route('add-gasmeter-state'); ?>>Registrácia</a>
			<a href=<?php echo go_to_URL_by_route('settings'); ?>>Nastavenia</a>
		</div>
	</nav>

	<div class="nav-seg-right">
		<div class="username">
			<?php echo "Prihlásený užívateľ: " ?>
			<a href=<?php echo go_to_URL_by_route('user-edit/' . $logged_in_user_id); ?>><?php echo $full_name; ?></a>
		</div>
		<div class="logout">
			<a href=<?php echo go_to_URL_by_route('logout'); ?> onclick="return confirm('Odhlásenie potvrďťe kliknutím na OK');">Odhlásiť sa</a>
		</div>
	</div>

</div>

<?php //print_r($_SESSION);
?>