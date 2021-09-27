<?php require_once("includes/initialize.php"); ?>
<?php
if (!$session->is_logged_in()) {
	redirect_to("admin/login.php");
}
?>
<p class="top-message-bar">Vyberte kategóriu nastavení.</p>




<div class="forms">
	<div class="all-forms">

		<h3 class="center">Nastavenia</h3>
		<div class="container">
			<ul>
				<li><a href="season-current">Fakturačné obdobie</a></li>
				<li><a href="season-add-change">Registrácia zmien fakturačného obdobia</a></li>
				<li><a href="users-manage">Nastavenia používateľov aplikácie</a></li>
			</ul>
		</div>

	</div>
</div>


<?php include('layouts/footer.php'); ?>