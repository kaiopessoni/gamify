<?php 

	/* Inicialização
  ========================================*/
	header('Content-Type: application/json');
	require_once "../db.php";
	require_once "../global.php";
	require_once "../classes/User.php";
	require_once "../classes/Group.php";
	require_once "../classes/Mentor.php";
	require_once "../classes/Mission.php";
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
    
    /* Recebe as informações do grupo para o mod editar o grupo
  	====================*/
		case "missao-info":
			
      try {
        
        $missao = new Mission();
        $missao->getMission($data["mtoken"]);
        
        $array = [
          "nome"        => $missao->getNome(),
          "descricao"   => $missao->getDescricao(),
          "prazo"       => $missao->getPrazo(),
          "recompensa"  => $missao->getRecompensa()
        ];
        
        finish("success", "missao_info", "Info da missao recebida com sucesso!", $array, "missao");
        
      } catch (Exception $e) {
        $error = unserialize($e->getMessage());
        finish("error", $error["type"], $error["info"]);
      }
      
    break;

        /* Edita o grupo
  	====================*/
		case "editar-missao":
			
      try {
        
        $mentor = new Mentor();
        $mentor->getUser($_SESSION["gm_utoken"]);
        
        $missao = new Mission();
        $missao->getMission($data["mtoken"]);
        
        $missao->setNome($data["nome"]);
        $missao->setDescricao($data["descricao"]);
        $missao->setRecompensa($data["recompensa"]);
        
        $mentor->update_mission($data["gtoken"], $missao, $data["prazo"]);
        
        finish("success", "mission_updated", "Missão atualizada com sucesso!");
        
        
      } catch (Exception $e) {
        $error = unserialize($e->getMessage());
        finish("error", $error["type"], $error["info"]);
      }
      
    break;

    /* Exclue uma missão
  	====================*/
    case "excluir-missao":

      try {

        $mentor = new Mentor();
        $mentor->getUser($_SESSION["gm_utoken"]);

        $mentor->delete_mission($data["gtoken"], $data["mtoken"]);
        finish("success", "mission_deleted", "Missão excluída com sucesso!");

      } catch (Exception $e) {
				$error = unserialize($e->getMessage());
				finish("error", $error["type"], $error["info"]);
			}

    break;

    /* Recusa uma missão completada
  	====================*/
    case "recusar-missao":

      try {

        $mentor = new Mentor();
        $mentor->getUser($_SESSION["gm_utoken"]);

        $mentor->respond_player($data["gtoken"], $data["mtoken"], $data["utoken"], "ativa");

        finish("success", "mission_denied", "Missão recusada com sucesso!");

      } catch (Exception $e) {
				$error = unserialize($e->getMessage());
				finish("error", $error["type"], $error["info"]);
			}

    break;

    case "confirmar-missao":

      try {

        $mentor = new Mentor();
        $mentor->getUser($_SESSION["gm_utoken"]);

        $mentor->respond_player($data["gtoken"], $data["mtoken"], $data["utoken"], "completada", $data["recompensa"]);

        finish("success", "mission_confirmed", "Missão confirmada com sucesso!");

      } catch (Exception $e) {
				$error = unserialize($e->getMessage());
				finish("error", $error["type"], $error["info"]);
			}

    break;

		default:
			finish("error", "invalid_action", "Ação desconhecida!");

	}
	

?> 