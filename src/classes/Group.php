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
  
  static function get_qtd_users($gtoken) {

    $sql = "SELECT count(id_usuario) qtd_jogadores FROM ". TABLE_GRUPOS_USUARIOS ."
            WHERE 
              status = 'participando' AND 
              (tipo = ? OR tipo = ?) AND 
              id_grupo = (SELECT id_grupo FROM ". TABLE_GRUPOS ." WHERE gtoken = ?);";

    $stmt = Group::$conn->prepare($sql);

    $type = "jogador";
    $type2 = "jogador/moderador";

    $stmt->bind_param("ssi", $type, $type2, $gtoken);
    $stmt->execute();
    $stmt->bind_result($qtd_jogadores);
    $stmt->fetch();

    $type = "mentor";
    $type2 = "mentor/moderador";

    $stmt->bind_param("ssi", $type, $type2, $gtoken);
    $stmt->execute();
    $stmt->bind_result($qtd_mentores);
    $stmt->fetch();

    $type = "jogador/moderador";
    $type2 = "mentor/moderador";

    $stmt->bind_param("ssi", $type, $type2, $gtoken);
    $stmt->execute();
    $stmt->bind_result($qtd_moderadores);
    $stmt->fetch();

    return [
      "total"     => $qtd_jogadores + $qtd_mentores,
      "jogador"   => $qtd_jogadores,
      "mentor"    => $qtd_mentores,
      "moderador" => $qtd_moderadores
    ];

  }

  static function delete_group($gtoken) {

    Group::group_exists($gtoken);

    $sql = "UPDATE ". TABLE_GRUPOS ." SET ativo = 'nao' WHERE gtoken = ?";

    $stmt = Group::$conn->prepare($sql);
    $stmt->bind_param("s", $gtoken);
    $stmt->execute();

  }

  static function set_random_mod($gtoken) {

    Group::group_exists($gtoken);
    $qtd = Group::get_qtd_users($gtoken);

    $tipo = ( $qtd["mentor"] > 0 ) ? "mentor" : "jogador";

    $sql = "SELECT id_grupos_usuarios FROM ". TABLE_GRUPOS_USUARIOS ." WHERE tipo = '$tipo' AND status = 'participando' LIMIT 0, 1;";

    $stmt = Group::$conn->prepare($sql);
    $stmt->execute();
    $stmt->bind_result($id);
    $stmt->fetch();
    $stmt->close();

    // Torna moderador
    $sql = "UPDATE ". TABLE_GRUPOS_USUARIOS ." SET tipo = '$tipo/moderador' WHERE id_grupos_usuarios = $id;";
    $stmt = Group::$conn->prepare($sql);
    $stmt->execute();
    $stmt->close();
  }
	
}

Group::$conn = $conn;

try {
	
	// Group::set_random_mod("CD6C14D0");
	
} catch (Exception $e) {
	$error = unserialize($e->getMessage());
	echo json_encode($error, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

	
	
	
	