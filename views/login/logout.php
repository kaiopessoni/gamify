<?php 

	session_start();

	unset($_SESSION["gm_user_active"]);
	unset($_SESSION["gm_utoken"]);

	header("Location: /login");

?>