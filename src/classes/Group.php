<?php

require_once "../db.php";
require_once "../global.php";

class Group {
	
	/* Private Variables
	==================================================*/
	private $id_grupo, $gtoken, $criador, $nome, $icone;
	
	/* Static Variables
	==================================================*/
	static $conn;
	
	/* Getters & Setters
	==================================================*/
	public function getId_grupo() {
    return $this->id_grupo;
  }

  public function setId_grupo($id_grupo) {
    $this->id_grupo = $id_grupo;
  }

  public function getGtoken() {
    return $this->gtoken;
  }

  public function setGtoken($gtoken) {
    $this->gtoken = $gtoken;
  }

  public function getCriador() {
    return $this->criador;
  }

  public function setCriador($criador) {
    $this->criador = $criador;
  }

  public function getNome() {
    return $this->nome;
  }

  public function setNome($nome) {
		
		if ( strlen($nome) > 25 )
			throw new Exception( set_error("invalid_name", "É permitido no máximo 20 caracteres para o 'Nome'!"));
		
    $this->nome = $nome;
  }

  public function getIcone() {
    return $this->icone;
  }

  public function setIcone($icone) {
		
		if ( !is_numeric($icone) )
    	throw new Exception( set_error("invalid_icon", "Por favor, informe um icone válido!"));
		
    $this->icone = $icone;
  }
	
	
	/* Static Methods
	==================================================*/
	public function getGroup($gtoken) {
		
		Group::group_exists($gtoken);
		
		$sql = "SELECT id_grupo, gtoken, criador, nome, icone FROM ". TABLE_GRUPOS ." WHERE gtoken = ? and ativo = 'sim'";
		
		$stmt = Group::$conn->prepare($sql);
		$stmt->bind_param("s", $gtoken);
		$stmt->execute();
		$stmt->store_result();
		
		if ( $stmt->num_rows == 1 ) {
			
			$stmt->bind_result($id_grupo, $gtoken, $criador, $nome, $icone);
			$stmt->fetch();
			$this->setId_grupo($id_grupo);
			$this->setGtoken($gtoken);
			$this->setCriador($criador);
			$this->setNome($nome);
			$this->setIcone($icone);
				
		}
		
	}
	
	/* Static Methods
	==================================================*/
	
	static function group_exists($gtoken) {
		
		$sql = "SELECT gtoken FROM ". TABLE_GRUPOS ." WHERE gtoken = ? and ativo = 'sim'";
		
		$stmt = Group::$conn->prepare($sql);
		$stmt->bind_param("s", $gtoken);
		$stmt->execute();
		$stmt->store_result();
		
		if ( $stmt->num_rows != 1 )
			throw new Exception( set_error("group_doenst_exist", "O grupo informado não existe!"), 403);
		else
			return true;
	}
	
}

Group::$conn = $conn;

//try {
//	
//	Group::group_exists("56A2DEBC");
//	
//} catch (Exception $e) {
//	$error = unserialize($e->getMessage());
//	echo json_encode($error, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
//}

	
	
	
	