<?php require_once("includes/initialize.php"); ?>
<?php if (!$session->is_logged_in()) {
	redirect_to("user/login.php");
} ?>

<?php
$user = new User($db);

$current_otk = $user->find_by_id($_SESSION['otk_id']); // print_r($current_otk); die();
$boss = $current_otk->id; // print_r($boss); die();

// The segment(2) function used below returns current URL id segment value (current item id).
$user_to_delete_id = segment(2);


if ($user_to_delete_id && $boss == 10) {
	$selected_user = $user->find_by_id($user_to_delete_id); // print_r($selected_user); die();

	if ($user->delete($selected_user)) {
		$current_otk_initials = $user->current_otk_initials($selected_user); // Output in format "SK 23".
		log_action($current_otk_initials . " : Užívateľ \"{$selected_user->user_name}\" odstránený.");

		$session->message("Uživateľský účet \"" . $user->full_name($selected_user) . "\" bol úspešne odstránený.");
		redirect_to(go_to_URL_by_route('users-manage'));
	} else {
		$message = "Chyba, účet nebol odstránený.";
	}
} else {
	$session->message("");
}

if ($message == "") {
	$message = "Upravte údaje podľa potreby. Zmeny uložte!";
}
?>




<div class="forms">
	<p class="top-message-bar"><?php echo $message ?> </p>

	<div class="settings">

		<h3 class="center">Nastavenia používateľov aplikácie</h3><?php // echo $boss; 
																	?>
		<div class="form-container">
			<table class="users-table">
				<thead>
					<tr class="users-table__tr">
						<th style=" text-align: left; width: 200px;">Meno a priezvisko</th>
						<th style="text-align: left; width: 200px;">Prihlasovacie meno</th>
						<th style="text-align: left; width: 100px;">Pracovisko</th>
						<th style="text-align: center; width: 200px;">Číslo pečiatky</th>
						<th colspan="2" ; style="text-align: center;">Možnosti</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$employees = $user->find_all_ordered("surname", "ASC"); // print_r($users);
					foreach ($employees as $employee) :
					?>
						<tr>
							<td style="text-align: left;"><?php echo $user->full_name($employee); ?></td>
							<td style="text-align: left;"><?php echo $employee->user_name; ?></td>
							<td style="text-align: left;"><?php echo $employee->pracovisko; ?></td>
							<td style="text-align: center;"><?php echo $employee->razitko; ?></td>
							<td style="text-align: left; width: 70px;"><a href=<?php echo "user-edit/" . urlencode($employee->id); ?>>Upraviť</a></td>
							<?php if ($boss == 10) { ?>
								<td style="text-align: right; width: 70px;"><a href="users-manage/<?php echo urlencode($employee->id); ?>" onclick="return confirm('Kliknutím na OK bude užívateľský účet &ldquo;<?php echo $user->full_name($employee); ?>&rdquo; odstránený.');">Odstrániť</a></td>
							<?php } else { ?>
								<td style="text-align: right; width: 70px;"><a href="<?php echo go_to_URL_by_route('users-manage'); ?>" onclick="return alert('Váš účet nedisponuje oprávnením na výkon tejto operácie.');">Odstrániť</a></td>
							<?php } ?>
						</tr>
					<?php endforeach; ?>
				</tbody>

			</table>
			<div class="butons-section">
				<a href="user-add" class="button-ok">Pridať používateľa</a>
				<a href="settings" class="btn-borderless">Nastavenia</a>
			</div>
		</div>

	</div>
</div>


<?php include('layouts/footer.php'); ?>