<?php 

	/* Inicialização
  ========================================*/
	header('Content-Type: application/json');
	require_once "../db.php";
	require_once "../global.php";
	require_once "../classes/User.php";
	require_once "../classes/Group.php";
	require_once "../classes/Moderator.php";
	session_start();
	
	set_time_limit(300);

	/* Recebimento dos dados
  ========================================*/
	get_data($data);
	
	/* Controller
  ========================================*/

	switch ($data["action"]) {
			
		/* Muda o tipo do participante do grupo
  	====================*/
		case "change-user-type":
			
			try {
				
				$mod = new Moderator();
        $mod->getUser($_SESSION["gm_utoken"]);
        
        $tipo_participante = User::getType($data["utoken"], $data["gtoken"]);
        $message = "";


        switch ($data["type"]) {
          case "moderador":
            $novo_tipo = ($tipo_participante == "jogador") ? "jogador/moderador" : "mentor/moderador";
            $mod->change_type_of_user($data["utoken"], $data["gtoken"], $novo_tipo);
            $message = "Participante atualizado para Moderador com sucesso!";
          break;

          case "mentor":
            $mod->change_type_of_user($data["utoken"], $data["gtoken"], "mentor");
            $message = "Participante atualizado para Mentor com sucesso!";
            break;

          case "jogador":
            $mod->change_type_of_user($data["utoken"], $data["gtoken"], "jogador");
            $message = "Participante atualizado para Jogador com sucesso!";
            break;
            
          case "remover_moderador":
            $novo_tipo = ($tipo_participante == "jogador/moderador") ? "jogador" : "mentor";
            $mod->change_type_of_user($data["utoken"], $data["gtoken"], $novo_tipo);
            $message = "Participante atualizado com sucesso!";
          break;

          case "remover_do_grupo":
            $mod->change_status_of_user($data["utoken"], $data["gtoken"], "removido");
            $message = "Participante removido do grupo com sucesso!";
          break;
        }

				finish("success", "type_updated", $message);
				
			} catch (Exception $e) {
				$error = unserialize($e->getMessage());
				finish("error", $error["type"], $error["info"]);
			}
			
		break;
			
		default:
			finish("error", "invalid_action", "Ação desconhecida!");

	}
	


?> 