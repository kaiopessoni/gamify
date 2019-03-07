<?php

require_once "../global.php";
require_once "../db.php";

class Mission {

	/* Private Variables
	==================================================*/
	private $id_missao, $criador, $mtoken, $nome, $descricao, $prazo, $recompensa;

	/* Static Variables
	==================================================*/
	static $conn;

	/* Getters & Setters
	==================================================*/
	public function getId_missao() {
		return $this->id_missao;
	}

	public function setId_missao($id_missao) {
		$this->id_missao = $id_missao;
	}	
	
	public function getCriador() {
		return $this->criador;
	}

	public function setCriador($criador) {
		$this->criador = $criador;
	}

	public function getMtoken() {
		return $this->mtoken;
	}

	public function setMtoken($mtoken) {
		$this->mtoken = $mtoken;
	}

	public function getNome() {
		return $this->nome;
	}

	public function setNome($nome) {
		
		if ( strlen($nome) > 40 )
			throw new Exception( set_error("invalid_name", "É permitido no máximo 40 caracteres para o 'Nome'!"));
		
		$this->nome = $nome;
	}

	public function getDescricao() {
		return $this->descricao;
	}

	public function setDescricao($descricao) {
		$this->descricao = $descricao;
	}

	public function getPrazo() {
		return $this->prazo;
	}

	public function setPrazo($prazo) {
		$this->prazo = $prazo;
	}

	public function getRecompensa() {
		return $this->recompensa;
	}

	public function setRecompensa($recompensa) {
		
		if ( $recompensa > 1000 )
				throw new Exception( set_error("invalid_award", "É permitido no máximo uma recompensa de 1000 pontos!"));
		
		if ( $recompensa < 250 )
				throw new Exception( set_error("invalid_award", "É permitido no mínimo uma recompensa de 250 pontos!"));
		
		$this->recompensa = $recompensa;
	}

	
	/* Public Methods
	==================================================*/
	public function getMission($mtoken) {

		Mission::mission_exists($mtoken);
		
		$sql = "SELECT id_missao, id_usuario, mtoken, nome, descricao, prazo, recompensa FROM ". TABLE_MISSOES ." WHERE mtoken = ? and ativo = 'sim'";

		$stmt = Mission::$conn->prepare($sql);
		$stmt->bind_param("s", $mtoken);
		$stmt->execute();
		$stmt->store_result();

		if ( $stmt->num_rows == 1 ) {

			$stmt->bind_result($id_missao, $criador, $mtoken, $nome, $descricao, $prazo, $recompensa);
			$stmt->fetch();

			$this->setId_missao($id_missao);
			$this->setCriador($criador);
			$this->setMtoken($mtoken);
			$this->setNome($nome);
			$this->setDescricao($descricao);
			$this->setPrazo($prazo);
			$this->setRecompensa($recompensa);
			
		}

	}


	/* Static Methods
	==================================================*/
	static function mission_exists($mtoken) {

		$sql = "SELECT mtoken FROM ". TABLE_MISSOES ." WHERE mtoken = ? and ativo = 'sim'";

		$stmt = Mission::$conn->prepare($sql);
		$stmt->bind_param("s", $mtoken);
		$stmt->execute();
		$stmt->store_result();

		if ( $stmt->num_rows != 1 )
			throw new Exception( set_error("mission_doenst_exist", "A missão informada não existe!"), 403);
		else
			return true;

	}

}

Mission::$conn = $conn;

try {
	
//	Mission::mission_exists("90C0AC08");
//	
//	$m = new Mission();
//	
//	$m->getMission("90C0AC08");
//	
//	echo $m->getCriador();
	
} catch (Exception $e) {
	$error = unserialize($e->getMessage());
	echo json_encode($error, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}