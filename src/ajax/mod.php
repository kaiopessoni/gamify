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

        // Verifica se é moderador
        $type_mod = ($tipo_participante == "jogador/moderador" || $tipo_participante == "mentor/moderador") ? "/moderador" : "";
        switch ($data["type"]) {
          case "moderador":
            $mod->change_type_of_user($data["utoken"], $data["gtoken"], $tipo_participante . "/moderador");
            $message = "Participante atualizado para Moderador com sucesso!";
          break;

          case "mentor":
            $mod->change_type_of_user($data["utoken"], $data["gtoken"], "mentor$type_mod");
            $message = "Participante atualizado para Mentor com sucesso!";
            break;

          case "jogador":
            $mod->change_type_of_user($data["utoken"], $data["gtoken"], "jogador$type_mod");
            $message = "Participante atualizado para Jogador com sucesso!";
            break;
            
          case "remover_moderador":

            $qtd = Group::get_qtd_users($data["gtoken"]);

            if ( $qtd["moderador"] == 1 )
              finish("error", "few_mod", "É necessário ter no mínimo um moderador no grupo!");

            $novo_tipo = ($tipo_participante == "jogador/moderador") ? "jogador" : "mentor";
            $mod->change_type_of_user($data["utoken"], $data["gtoken"], $novo_tipo);
            $message = "Participante atualizado com sucesso!";
          break;

          case "remover_do_grupo":
          
            $qtd = Group::get_qtd_users($data["gtoken"]);

            if ( $qtd["total"] == 1 ) {

              $mod->change_status_of_user($data["utoken"], $data["gtoken"], "saiu");
              $mod->delete_group($data["gtoken"]);
              $message = "Participante removido e grupo excluído com sucesso!";

            } else {

              $mod->change_status_of_user($data["utoken"], $data["gtoken"], "removido");
              $message = "Participante removido do grupo com sucesso!";

            }
            
          break;
        }

				finish("success", "type_updated", $message);
				
			} catch (Exception $e) {
				$error = unserialize($e->getMessage());
				finish("error", $error["type"], $error["info"]);
			}
			
		break;
      
    /* Exclue um grupo
  	====================*/
    case "excluir-grupo":

      try {

        $mod = new Moderator();
        $mod->getUser($_SESSION["gm_utoken"]);

        $mod->delete_group($data["gtoken"]);
        finish("success", "group_deleted", "Grupo excluído com sucesso!");

      } catch (Exception $e) {
				$error = unserialize($e->getMessage());
				finish("error", $error["type"], $error["info"]);
			}

    break;

    /* Recebe as informações do grupo para o mod editar o grupo
  	====================*/
		case "grupo-info":
			
			try {
				
				$group = new Group();
        $group->getGroup($data["gtoken"]);
        
				$array = [
					"nome" 	=> $group->getNome(),
					"icone" => $group->getIcone(),
				];
        
				finish("success", "group_info", "Info recebidas com sucesso!", $array, "grupo");
				
			} catch (Exception $e) {
				$error = unserialize($e->getMessage());
				finish("error", $error["type"], $error["info"]);
			}
			
    break;
    
    /* Edita o grupo
  	====================*/
		case "editar-grupo":
			
			try {
        
        $mod = new Moderator();
        $mod->getUser($_SESSION["gm_utoken"]);

				$group = new Group();
        $group->getGroup($data["gtoken"]);

        $group->setNome($data["nome"]);
        $group->setIcone($data["icone"]);

        $mod->edit_group($group);

				finish("success", "group_updated", "Grupo atualizado com sucesso!");
				
			} catch (Exception $e) {
				$error = unserialize($e->getMessage());
				finish("error", $error["type"], $error["info"]);
			}
			
		break;

    /* Aceita/Recusa solicitações para entrar no grupo
  	====================*/
		case "participar-grupo":
    
      try {

        // finish("success", "askdoasd", "Usuário participando do grupo! " . $data["status"]);
        $mod = new Moderator();
        $mod->getUser($_SESSION["gm_utoken"]);
        
        switch ($data["status"]) {
          case "aceitar":
            $mod->change_status_of_user($data["utoken"], $data["gtoken"], "participando");
            $message = "O usuário foi aceito, agora é um participante do grupo!";
          break;

          case "nao-aceitar":
          $mod->change_status_of_user($data["utoken"], $data["gtoken"], "recusado");
            $message = "O usuário não foi aceito no grupo!";
            break;

          case "bloquear":
            $mod->change_status_of_user($data["utoken"], $data["gtoken"], "bloqueado");
            $message = "O usuário foi bloqueado e não poderá enviar mais solicitações!";
            break;

          default:
            $message = "Ação desconhecida!";
        }

        finish("success", "status_updated", $message);
        
      } catch (Exception $e) {
        $error = unserialize($e->getMessage());
        finish("error", $error["type"], $error["info"]);
      }
      
    break;

    /* Desbloqueia usuário
  	====================*/
		case "desbloquear-usuario":
    
      try {

        $mod = new Moderator();
        $mod->getUser($_SESSION["gm_utoken"]);
        
        $mod->change_status_of_user($data["utoken"], $data["gtoken"], "desbloqueado");

        finish("success", "user_unblocked", "Usuário desbloqueado com sucesso!");
        
      } catch (Exception $e) {
        $error = unserialize($e->getMessage());
        finish("error", $error["type"], $error["info"]);
      }
      
    break;

		default:
			finish("error", "invalid_action", "Ação desconhecida!");

	}
	


?> 