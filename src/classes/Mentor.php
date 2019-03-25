<?php

require_once "User.php";
require_once "Mission.php";
require_once "Player.php";

class Mentor extends User {

	/* Private Variables
	==================================================*/
	private $missoes;

	/* Getters & Setters
	==================================================*/
	public function getMissoes() {
		return $this->missoes;
	}

	public function setMissoes(Mission $missoes) {
		$this->missoes = $missoes;
	}

	/* Public Methods
	==================================================*/
	public function create_mission($gtoken, Mission $missao) {

		Group::group_exists($gtoken);

		$group = new Group();
		$group->getGroup($gtoken);

		$type = User::getType($this->getUtoken(), $gtoken);

		if ( $type == "mentor" || $type == "mentor/moderador" ) {

			$mtoken 		= mb_strtoupper(substr(hash("sha1", date("Y-m-d H:i:s") . $gtoken . rand(1000, 10000)), -8));
			$id_usuario = $this->getId_usuario();
			$id_grupo 	= $group->getId_grupo();
			$nome 			= $missao->getNome();
			$descricao 	= $missao->getDescricao();
			$prazo 			= str_replace('/', '-', $missao->getPrazo());
			$prazo 			= date('Y-m-d', strtotime($prazo));
			$recompensa = $missao->getRecompensa();

			$sql = "INSERT INTO ". TABLE_MISSOES ." (mtoken, id_usuario, id_grupo, nome, descricao, prazo, recompensa, ativo)
							VALUES (?, ?, ?, ?, ?, ?, ?, 'sim')";

			$stmt = User::$conn->prepare($sql);
			$stmt->bind_param("siissss", $mtoken, $id_usuario, $id_grupo, $nome, $descricao, $prazo, $recompensa);
			$stmt->execute();

		} else throw new Exception( set_error("permission_denied", "Você não é um mentor para criar uma missão!"), 403);

	}

	public function update_mission($gtoken, Mission $mission) {
		
		$mtoken = $mission->getMtoken();
		Mission::mission_exists($mtoken);

		$type = User::getType($this->getUtoken(), $gtoken);

		if ( $type == "mentor" || $type == "mentor/moderador" ) {

			if ( $mission->getCriador() == $this->getId_usuario() ) {
				
				$nome 			= $mission->getNome();
				$descricao 	= $mission->getDescricao();
				$prazo 			= str_replace('/', '-', $missao->getPrazo());
				$prazo 			= date('Y-m-d', strtotime($prazo));
				$recompensa = $mission->getRecompensa();

				$sql = "UPDATE ". TABLE_MISSOES ." SET nome = ?, descricao = ?, prazo = ?, recompensa = ? WHERE mtoken = ? and ativo = 'sim'";
				$stmt = Mission::$conn->prepare($sql);
				$stmt->bind_param("sssis", $nome, $descricao, $prazo, $recompensa, $mtoken);
				$stmt->execute();
				
			} else throw new Exception( set_error("permission_denied", "Você não é o criador da missão!"), 403);

		} else throw new Exception( set_error("permission_denied", "Você não é um mentor para realizar essa ação!"), 403);
		
	}

	public function delete_mission($gtoken, $mtoken) {

		Mission::mission_exists($mtoken);
		
		$type = User::getType($this->getUtoken(), $gtoken);

		if ( $type == "mentor" || $type == "mentor/moderador" ) {
			
			$mission = new Mission();
			$mission->getMission($mtoken);
			
			if ( $mission->getCriador() == $this->getId_usuario() ) {
				
				$sql = "UPDATE ". TABLE_MISSOES ." SET ativo = 'nao' WHERE mtoken = ? and ativo = 'sim'";
		
				$stmt = User::$conn->prepare($sql);
				$stmt->bind_param("s", $mtoken);
				$stmt->execute();
				
			} else throw new Exception( set_error("permission_denied", "Você não é o criador da missão!"), 403);
			
		} else throw new Exception( set_error("permission_denied", "Você não é um mentor para realizar essa ação!"), 403);
		
	}

	public function respond_player($gtoken, $mtoken, $utoken, $status, $recompensa_final = null) {

		Group::group_exists($gtoken);
		Mission::mission_exists($mtoken);
		
		$type = User::getType($this->getUtoken(), $gtoken);

		if ( $type == "mentor" || $type == "mentor/moderador" ) {
			
			$mission = new Mission();
			$mission->getMission($mtoken);
			
			$player = new Player();
			$player->getUser($utoken);
			
			$group = new Group();
			$group->getGroup($gtoken);
			
			$id_grupo 	= $group->getId_grupo();
			$id_missao 	= $mission->getId_missao();
			$id_usuario = $player->getId_usuario();
			
			if ( $mission->getCriador() == $this->getId_usuario() ) {
				
				if ( $mission->getPrazo() > date("Y-m-d") )
					throw new Exception( set_error("mission_expired", "Não é possível realizar esta ação pois a missão está expirada!"), 403);
				
				// CONFIRMA A MISSÃO COMPLETADA E ADICIONA OS PONTOS
				if ( $status == "completada" ) {
					
					if ( $recompensa_final > $mission->getRecompensa() || $recompensa_final == NULL )
						throw new Exception( set_error("invalid_award", "A recompensa informada é inválida!"), 403);
					
					$sql = "UPDATE ". TABLE_MISSOES_JOGADORES ." SET status = ?, recompensa_final = ? WHERE id_missao = ? and id_usuario = ?";
					$stmt = User::$conn->prepare($sql);
					$stmt->bind_param("siii", $status, $recompensa_final, $id_missao, $id_usuario);
					$stmt->execute();
					$stmt->close();
					
					// Recebe os pontos do jogador
					$sql = "SELECT pontos FROM ". TABLE_GRUPOS_USUARIOS ." WHERE id_grupo = ? and id_usuario = ?";
					$stmt = User::$conn->prepare($sql);
					$stmt->bind_param("ii", $id_grupo, $id_usuario);
					$stmt->execute();
					$stmt->bind_result($pontos);
					$stmt->fetch();
					$stmt->close();
					
					// Define a quantidade de pontos e o nível do jogador
					$pontos += $recompensa_final;
					$nivel = floor($pontos / 1000);
					$nivel = ( $nivel < 1 ) ? 1 : $nivel;
					
					$sql = "UPDATE ". TABLE_GRUPOS_USUARIOS ." SET pontos = ?, nivel = ? WHERE id_grupo = ? and id_usuario = ?";
					$stmt = User::$conn->prepare($sql);
					$stmt->bind_param("iiii", $pontos, $nivel, $id_grupo, $id_usuario);
					$stmt->execute();
					
				} else {
					
					$sql = "UPDATE ". TABLE_MISSOES_JOGADORES ." SET status = ? WHERE id_missao = ? and id_usuario = ?";
					$stmt = User::$conn->prepare($sql);
					$stmt->bind_param("sii", $status, $id_missao, $id_usuario);
					$stmt->execute();
					
				}
		
			} else throw new Exception( set_error("permission_denied", "Você não é o criador da missão!"), 403);
			
		} else throw new Exception( set_error("permission_denied", "Você não é um mentor para realizar essa ação!"), 403);
		
	}

	
}

try {
	
	// $group = new Group();
	// $group->getGroup("CD6C14D0");
	
	// $mentor = new Mentor();
	// $mentor->getUser("EC270E78");
	
	// Cria uma missão
	/*
	$missao = new Mission();
	$missao->setNome("Prova 1 Gestão de TI");
	$missao->setDescricao("Prova 1 Gestão de TI desc");
	$missao->setPrazo("2/10/2018");
	$missao->setRecompensa(750);
	$mentor->create_mission($group->getGtoken(), $missao);
	*/
	
	
	// Aceita a missão ou recusa
//	/*
	// $mission = new Mission();
	// $mission->getMission("62F63EFA");
	// $mentor->respond_player("CD6C14D0", "62F63EFA", "6BF4477B", "completada", 900);
//	*/
	
	
	
} catch (Exception $e) {
	$error = unserialize($e->getMessage());
	echo json_encode($error, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
