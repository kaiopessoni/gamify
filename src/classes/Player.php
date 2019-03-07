<?php 

require_once "User.php";
require_once "Mission.php";

class Player extends User {

	/* Private Variables
	==================================================*/
	private $nivel, $pontos;

	/* Getters & Setters
	==================================================*/
	public function getNivel() {
		return $this->nivel;
	}

	public function setNivel($nivel) {
		$this->nivel = $nivel;
	}

	public function getPontos() {
		return $this->pontos;
	}

	public function setPontos($pontos) {
		$this->pontos = $pontos;
	}

	/* Public Methods
	==================================================*/

	public function complete_mission($gtoken, $mtoken) {
		
		Group::group_exists($gtoken);
		Mission::mission_exists($mtoken);
		
		$type = User::getType($this->getUtoken(), $gtoken);

		if ( $type == "jogador" || $type == "jogador/moderador" ) {
			
			$mission = new Mission();
			$mission->getMission($mtoken);
			
			if ( date("Y-m-d") > $mission->getPrazo() )
					throw new Exception( set_error("mission_expired", "Não é possível realizar esta ação pois a missão está expirada!"), 403);
			
			$id_usuario = $this->getId_usuario();
			$id_missao 	= $mission->getId_missao();
			
			$sql = "SELECT status FROM ". TABLE_MISSOES_JOGADORES ." WHERE id_usuario = ? and id_missao = ? ";
			$stmt = User::$conn->prepare($sql);
			$stmt->bind_param("ii", $id_usuario, $id_missao);
			$stmt->execute();
			$stmt->store_result();

			if ( $stmt->num_rows == 1 ) {
				
				$stmt->bind_result($status);
				$stmt->fetch();
				
				switch($status):
					case "pendente":
						throw new Exception( set_error("mission_already_asked", "Você já enviou uma solicitação para o mentor!"), 401);
						break;
					case "completada":
						throw new Exception( set_error("mission_already_completed", "Você já completou essa missão!"), 401);
						break;
					case "expirada":
						throw new Exception( set_error("mission_already_completed", "Não é possível completar a missão pois ela está expirada!"), 401);
						break;
				endswitch;
				
			} else  {
				
				$sql = "INSERT INTO ". TABLE_MISSOES_JOGADORES ." (id_usuario, id_missao, status) VALUES (?, ?, 'pendente')";
				$stmt = User::$conn->prepare($sql);
				$stmt->bind_param("ii", $id_usuario, $id_missao);
				$stmt->execute();
				
			}
			
		} else throw new Exception( set_error("permission_denied", "Você não é um jogador para realizar essa ação!"), 403);
		
	}

}

try {
	
	// Completa a missão
	/*
	$player = new Player();
	$player->getUser("6BF4477B");
	$player->complete_mission("CD6C14D0", "62F63EFA");
	*/
	
} catch (Exception $e) {
	$error = unserialize($e->getMessage());
	echo json_encode($error, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}