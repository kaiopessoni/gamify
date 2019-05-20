<?php 

  session_save_path($_SERVER["DOCUMENT_ROOT"] . '/session');
    
  if  (session_status() == PHP_SESSION_NONE )
    session_start();

	unset($_SESSION["gm_user_active"]);
	unset($_SESSION["gm_utoken"]);

	header("Location: /login");

?>