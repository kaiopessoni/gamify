<?php 

	/* Inicialização
  ========================================*/
	header('Content-Type: application/json');
	require_once "../db.php";
	require_once "../global.php";
	require_once "../classes/User.php";
	require_once "../classes/Group.php";
  require_once "../classes/Player.php";
  
	session_save_path($_SERVER["DOCUMENT_ROOT"] . '/session');
  
	if  (session_status() == PHP_SESSION_NONE )
    session_start();

	/* Recebimento dos dados
  ========================================*/
	get_data($data);
	
	/* Controller
  ========================================*/
	switch ($data["action"]) {
			
    /* Completar missão
  	====================*/
		case "completar-missao":
			
			try {
        
        $player = new Player();
        $player->getUser($_SESSION["gm_utoken"]);

        $player->complete_mission($data["gtoken"], $data["mtoken"]);

				finish("success", "mission_completed", "Solicitação enviada para o mentor!");
				
			} catch (Exception $e) {
				$error = unserialize($e->getMessage());
				finish("error", $error["type"], $error["info"]);
			}
			
		break;


		default:
			finish("error", "invalid_action", "Ação desconhecida!");

	}
	

?> 