<?php 

	/* Inicialização
  ========================================*/
	header('Content-Type: application/json');
	require_once "../db.php";
	require_once "../global.php";
	require_once "../classes/User.php";
	require_once "../classes/Group.php";
	require_once "../classes/Mentor.php";
	session_start();
	
	set_time_limit(300);

	/* Recebimento dos dados
  ========================================*/
	get_data($data);
	
	/* Controller
  ========================================*/
	switch ($data["action"]) {
			
    /* Criar missão
  	====================*/
		case "criar-missao":
			
			try {

        $mentor = new Mentor();
        $mentor->getUser($_SESSION["gm_utoken"]);
        
        $missao = new Mission();
        $missao->setNome($data["nome_missao"]);
        $missao->setDescricao($data["descricao_missao"]);
        $missao->setPrazo($data["prazo_missao"]);
        $missao->setRecompensa($data["recompensa_missao"]);
        $mentor->create_mission($data["gtoken"], $missao);
        
				finish("success", "mission_created", "Missão criada com sucesso!");
				
			} catch (Exception $e) {
				$error = unserialize($e->getMessage());
				finish("error", $error["type"], $error["info"]);
			}
			
		break;

		default:
			finish("error", "invalid_action", "Ação desconhecida!");

	}
	

?> 