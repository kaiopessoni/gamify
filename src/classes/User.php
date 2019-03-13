<?php

require_once "../db.php";
require_once "../global.php";
require_once "Group.php";

class User {
	
	/* Private Variables
	==================================================*/
	private $id_usuario, $utoken, $access_key, $nome, $email, $senha, $icone, $tipo;
	
	/* Static Variables
	==================================================*/
	static $conn;
	
	
	/* Getters & Setters
	==================================================*/
	public function getId_usuario() {
		return $this->id_usuario;
	}
	
	public function setId_usuario($id_usuario) {
		$this->id_usuario = $id_usuario;
	}
	
	public function getUtoken() {
    return $this->utoken;
  }

  public function setUtoken($utoken) {
  	$this->utoken = $utoken;
  }

  public function getAccess_key() {
    return $this->access_key;
  }

  public function setAccess_key($access_key) {
    $this->access_key = $access_key;
  }

  public function getNome() {
    return $this->nome;
  }

  public function setNome($nome) {
		
		if ( strlen($nome) > 25 )
			throw new Exception( set_error("invalid_name", "É permitido no máximo 25 caracteres para o 'Nome'!"));
		
    $this->nome = $nome;
  }

  public function getEmail() {
    return $this->email;
  }

  public function setEmail($email) {
		
		if ( !filter_var($email, FILTER_VALIDATE_EMAIL) )
    	throw new Exception( set_error("invalid_email", "Por favor, informe um e-mail válido!"));
		
		$this->email = $email;
  }

  public function getSenha() {
    return $this->senha;
  }

  public function setSenha($senha) {
		
		if ( strlen($senha) == 40 )
			$this->senha = $senha;
		else {
			
			if ( strlen($senha) > 25 )
				throw new Exception( set_error("invalid_name", "É permitido no máximo 25 caracteres para a 'Senha'!"));
			
			$this->senha = sha1($senha);
		}
    
  }

  public function getIcone() {
    return $this->icone;
  }

  public function setIcone($icone) {
		
		if ( !is_numeric($icone) )
    	throw new Exception( set_error("invalid_icon", "Por favor, informe um icone válido!"));
		
    $this->icone = $icone;
  }

  public function getTipo() {
    return $this->tipo;
  }

  public function setTipo($tipo) {
    $this->tipo = $tipo;
  }
	
	
	/* Public Methods
	==================================================*/
	
	// Creates an user
	public function create() {
		
		$access_key = hash("sha1", date("Y-m-d H:i:s") . $this->getEmail() . rand(1000, 10000));
		$utoken 		= mb_strtoupper(substr($access_key, -8));
		$email 			= $this->getEmail();
		$senha 			= $this->getSenha();
		$nome 			= $this->getNome();
		
		// Check if email already exists
		$sql = "SELECT email FROM ". TABLE_USUARIOS ." WHERE email = ? and ativo = 'sim'";
		
		$stmt = User::$conn->prepare($sql);
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$stmt->store_result();
		
		if ( $stmt->num_rows > 0 )
			throw new Exception( set_error("email_already_exists", "O e-mail informado já está cadastrado!"));
		
		// Insert the user
		$sql = "INSERT INTO ". TABLE_USUARIOS ." (utoken, access_key, email, senha, nome, icone, ativo)
						VALUES ('$utoken', '$access_key', ?, ?, ?, 1, 'sim')";
		
		$stmt = User::$conn->prepare($sql);
		$stmt->bind_param("sss", $email, $senha, $nome);
		$stmt->execute();
		
		$this->setUtoken($utoken);
		
	}
	
	// Updates the profile
	public function update() {
		
		$utoken = $this->getUtoken();
		$email 	= $this->getEmail();
		$nome 	= $this->getNome();
		$icone 	= $this->getIcone();
		$senha 	= $this->getSenha();
		
		$sql = "UPDATE ". TABLE_USUARIOS ." SET email = ?, senha = ?, nome = ?, icone = ? WHERE utoken = ? and ativo = 'sim'";
		
		$stmt = User::$conn->prepare($sql);
		$stmt->bind_param("sssis", $email, $senha, $nome, $icone, $utoken);
		$stmt->execute();
	
	}
	
	// Retrieves the data to the object
	public function getUser($utoken) {
		
		$sql = "SELECT id_usuario, access_key, email, senha, nome, icone FROM ". TABLE_USUARIOS ." WHERE utoken = ? and ativo = 'sim'";
		
		$stmt = User::$conn->prepare($sql);
		$stmt->bind_param("s", $utoken);
		$stmt->execute();
		$stmt->store_result();
		
		if ( $stmt->num_rows == 1 ) {
			
			$stmt->bind_result($id_usuario, $access_key, $email, $senha, $nome, $icone);
			$stmt->fetch();
			
			$this->setId_usuario($id_usuario);
			$this->setUtoken($utoken);
			$this->setAccess_key($access_key);
			$this->setEmail($email);
			$this->setSenha($senha);
			$this->setNome($nome);
			$this->setIcone($icone);
				
		} else throw new Exception( set_error("user_doesnt_exist", "Oops, o usuário informado não existe!"), 500);
		
	}
	
	// Creates a group
	public function create_group(Group $group) {
		
		$gtoken 	= mb_strtoupper(substr(hash("sha1", date("Y-m-d H:i:s") . $this->getEmail() . rand(1000, 10000)), -8));
		$criador 	= $this->getUtoken();
		$nome 		= $group->getNome();
		$icone 		= $group->getIcone();
		
		$sql = "INSERT INTO ". TABLE_GRUPOS ." (gtoken, criador, nome, icone, ativo)
						VALUES (?, ?, ?, ?, 'sim')";
		
		$stmt = User::$conn->prepare($sql);
		$stmt->bind_param("sssi", $gtoken, $criador, $nome, $icone);
		$stmt->execute();
		
		$id_grupo = $stmt->insert_id;
		$id_usuario = $this->getId_usuario();
		
		$stmt->close();
		
		$sql = "INSERT INTO ". TABLE_GRUPOS_USUARIOS ." (id_grupo, id_usuario, nivel, pontos, tipo, status)
						VALUES (?, ?, 1, 0, 'jogador/moderador', 'participando')";
		
		$stmt = User::$conn->prepare($sql);
		$stmt->bind_param("ii", $id_grupo, $id_usuario);
		$stmt->execute();
		
	}
	
	// Deletes a group
	public function delete_group($gtoken) {
		
		Group::group_exists($gtoken);
		
		$group = new Group;
		$group->getGroup($gtoken);
		
		if ( $this->getUtoken() == $group->getCriador() ) {
			
			$sql = "UPDATE ". TABLE_GRUPOS ." SET ativo = 'nao' WHERE gtoken = ? and ativo = 'sim'";
		
			$stmt = User::$conn->prepare($sql);
			$stmt->bind_param("s", $gtoken);
			$stmt->execute();
			
		} else throw new Exception( set_error("user_isnt_the_creator", "Você não têm permissão para excluir este grupo!"));
		
	}
	
	public function enter_group($gtoken) {
		
		Group::group_exists($gtoken);
		
		$group = new Group();
		$group->getGroup($gtoken);
		
		$id_grupo 	= $group->getId_grupo();
		$id_usuario = $this->getId_usuario();
		
		// Recebe o status do usuário com o grupo caso exista
		$sql = "SELECT status FROM ". TABLE_GRUPOS_USUARIOS ." WHERE id_grupo = ? and id_usuario = ?";
		$stmt = User::$conn->prepare($sql);
		$stmt->bind_param("ii", $id_grupo, $id_usuario);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($status);
		$stmt->fetch();
		
		// Caso já exista uma relação entre o grupo e o usuário
		if ( $stmt->num_rows > 0 ) {
			
			switch ($status):
			case "participando":
				throw new Exception( set_error("user_already_in", "Voçê já participa deste grupo!"));
				break;
			case "pendente":
				throw new Exception( set_error("invite_already_sent", "Solicitação para o grupo já enviada!"));
				break;
			case "bloqueado":
				throw new Exception( set_error("user_is_blocked", "Você foi bloqueado neste grupo e não pode enviar mais solicitações!"));
				break;
			endswitch;
			
		} else {
			
			// Coloca o status do usuário com o grupo como pendente
			$sql = "INSERT INTO ". TABLE_GRUPOS_USUARIOS ." (id_grupo, id_usuario, nivel, pontos, tipo, status)
				VALUES (?, ?, 1, 0, 'jogador', 'pendente')";
		
			$stmt = User::$conn->prepare($sql);
			$stmt->bind_param("ii", $id_grupo, $id_usuario);
			$stmt->execute();
			
		}
		
		
	}
	
	public function getGroups() {
		
		$id_usuario = $this->getId_usuario();
		
		// Recebe os ids dos grupos que o usuário faz parte
		$sql = "SELECT id_grupo FROM ". TABLE_GRUPOS_USUARIOS ." WHERE id_usuario = ? and status = 'participando'";
		$stmt = User::$conn->prepare($sql);
		$stmt->bind_param("i", $id_usuario);
		$stmt->execute();
		$stmt->bind_result($id_grupo);
		$stmt->store_result();
		
		$grupos = [];
		
		if ( $stmt->num_rows > 0 ) {
			
			while ( $stmt->fetch() ) { $ids_grupos[] = $id_grupo; }
			$stmt->close();

			foreach ( $ids_grupos as $i => $id_grupo ) {

				// Recebe informações básicas do grupo
				$sql = "SELECT
									g.gtoken,
									g.nome,
									g.icone,
									(SELECT count(id_usuario) FROM ". TABLE_GRUPOS_USUARIOS ." WHERE id_grupo = ?) 'qtd_participantes',
									(SELECT count(id_missao) FROM ". TABLE_MISSOES ." WHERE id_grupo = ? AND g.ativo = 'sim') 'qtd_missoes'
								FROM 
									". TABLE_GRUPOS ." g
								INNER JOIN ". TABLE_GRUPOS_USUARIOS ." gu
									ON g.id_grupo = gu.id_grupo
								WHERE 
									gu.id_usuario = ? AND
									gu.id_grupo = ?;";
				
				$stmt = User::$conn->prepare($sql);
				$stmt->bind_param("iiii", $id_grupo, $id_grupo, $id_usuario, $id_grupo);
				$stmt->execute();
				$stmt->bind_result($gtoken, $nome_grupo, $icone_grupo, $qtd_participantes, $qtd_missoes);
				$stmt->fetch();
				$stmt->close();
				
				// Recebe o pódio do grupo
				$sql = "SELECT nome FROM ". TABLE_GRUPOS_USUARIOS ."
								INNER JOIN ". TABLE_USUARIOS ." ON ". TABLE_USUARIOS .".id_usuario = ". TABLE_GRUPOS_USUARIOS .".id_usuario
								WHERE
									(tipo = 'jogador' or tipo = 'jogador/moderador') AND
									id_grupo = ?
								ORDER BY
									pontos DESC
								LIMIT 0, 3";
				
				$stmt = User::$conn->prepare($sql);
				$stmt->bind_param("i", $id_grupo);
				$stmt->execute();
				$stmt->bind_result($nome_podio);
				$stmt->store_result();
				
				$podio = ["-", "-", "-"];
				
				if ( $stmt->num_rows > 0 ) {
					
					$j = 0;
					
					while ( $stmt->fetch() ) { 
						$podio[$j] = $nome_podio; 
						$j++;
					}
					
				}
				
				$stmt->close();
				
				
				// Recebe os participantes do grupo
				$sql = "SELECT utoken, nome, tipo, icone FROM ". TABLE_GRUPOS_USUARIOS ."
								INNER JOIN ". TABLE_USUARIOS ." ON ". TABLE_USUARIOS .".id_usuario = ". TABLE_GRUPOS_USUARIOS .".id_usuario
								WHERE	ativo = 'sim' AND status = 'participando' AND id_grupo = ?";
				
				$stmt = User::$conn->prepare($sql);
				$stmt->bind_param("i", $id_grupo);
				$stmt->execute();
				$stmt->bind_result($utoken, $nome_participante, $tipo, $icone_participante);
				
				while ( $stmt->fetch() ) { 
					$participantes[] = [
						"utoken" 	=> $utoken,
						"nome" 		=> $nome_participante,
						"tipo" 		=> $tipo,
						"icone" 	=> $icone_participante
					];
				}
				
				$stmt->close();
				
				$bloqueados = [];
				
				// Recebe usuários bloqueados se for moderador
				if ( User::getType($this->getUtoken(), $gtoken) == "jogador/moderador" || User::getType($this->getUtoken(), $gtoken) == "mentor/moderador" ) {
					
					$sql = "SELECT utoken, nome FROM ". TABLE_GRUPOS_USUARIOS ."
									INNER JOIN ". TABLE_USUARIOS ." ON ". TABLE_USUARIOS .".id_usuario = ". TABLE_GRUPOS_USUARIOS .".id_usuario
									WHERE	ativo = 'sim' AND status = 'bloqueado' AND id_grupo = ?";
									$stmt = User::$conn->prepare($sql);
					$stmt->bind_param("i", $id_grupo);
					$stmt->execute();
					$stmt->bind_result($utoken, $nome_bloqueado);

					while ( $stmt->fetch() ) { 
						$bloqueados[] = [
							"utoken" 	=> $utoken,
							"nome" 		=> $nome_bloqueado
						];
					}
				
					$stmt->close();
				}
				
				$grupos[] = [
					"gtoken"  					=> $gtoken,
					"nome"							=> $nome_grupo,
					"icone"							=> $icone_grupo,
					"podio" 						=> $podio,
					"qtd_participantes" => $qtd_participantes,
					"qtd_missoes" 			=> $qtd_missoes,
					"participantes"			=> $participantes,
					"bloqueados"				=> $bloqueados
				];
				
				$podio 					= [];
				$participantes 	= [];
				$bloqueados 		= [];
				
			}
			
		}
		
		return $grupos;
		
//		echo "<pre>";
//		echo json_encode($grupos, JSON_PRETTY_PRINT);
//		echo "</pre>";
		
	}
	
	public function getMissions($gtoken) {
		
		Group::group_exists($gtoken);
		
		$group = new Group();
		$group->getGroup($gtoken);
		
		$id_grupo = $group->getId_grupo();
		
		$sql = "SELECT mtoken, utoken 'criador', u.nome, m.nome, descricao, prazo, recompensa, status, recompensa_final	FROM ". TABLE_MISSOES ." m 
						INNER JOIN usuarios u ON m.id_usuario = u.id_usuario
						LEFT JOIN ". TABLE_MISSOES_JOGADORES ." mj ON m.id_usuario = mj.id_usuario
						WHERE  id_grupo = ? and m.ativo = 'sim'
						ORDER BY prazo DESC";
		
		$stmt = User::$conn->prepare($sql);
		$stmt->bind_param("i", $id_grupo);
		$stmt->execute();
		$stmt->bind_result($mtoken, $criador, $nome_criador, $nome_missao, $descricao, $prazo, $recompensa, $status, $recompensa_final);
		$stmt->store_result();
		
		$missoes = [];
		
		if ( $stmt->num_rows > 0 ) {
			
			while ( $stmt->fetch() ) {
				
				if ( User::getType($this->getUtoken(), $gtoken) == "jogador" || User::getType($this->getUtoken(), $gtoken) == "jogador/moderador" ) {
					
					$status = ( $status == NULL ) ? "ativa" : $status;
					$recompensa_final = ( $recompensa_final == NULL ) ? "" : $recompensa_final;
					
					if ( $status != "completada" && date("Y-m-d") > $prazo )
						$status = "expirada";
					
					
				} else
					$recompensa_final = $status = "";
					
				
				$prazo 			= date('d-m-Y', strtotime($prazo));
				$prazo 			= str_replace('-', '/', $prazo);
				
				$missoes[] = [
					"mtoken" 						=> $mtoken,
					"criador" 					=> $criador,
					"nome_criador"			=> $nome_criador,
					"nome" 							=> $nome_missao,
					"descricao" 				=> $descricao,
					"prazo" 						=> $prazo,
					"recompensa" 				=> $recompensa,
					"status" 						=> $status,
					"recompensa_final" 	=> $recompensa_final
				];
				
			}
			
		}
		
		return $missoes;
		
		echo "<pre>";
		echo json_encode($missoes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		echo "</pre>";
		
	}
	
	public function getRanking($gtoken) {
		
		Group::group_exists($gtoken);
		
		$group = new Group();
		$group->getGroup($gtoken);
		
		$id_grupo = $group->getId_grupo();
		
		$sql = "SELECT utoken, nome, nivel, pontos, icone, status
						FROM ". TABLE_USUARIOS ." u
						INNER JOIN ". TABLE_GRUPOS_USUARIOS ." gu
							ON u.id_usuario = gu.id_usuario
						WHERE 
							tipo = 'jogador' OR tipo = 'jogador/moderador' AND u.ativo = 'sim' AND id_grupo = ?;";
		
		$stmt = User::$conn->prepare($sql);
		$stmt->bind_param("i", $id_grupo);
		$stmt->execute();
		$stmt->bind_result($utoken, $nome, $nivel, $pontos, $icone, $status);
		$stmt->store_result();
		
		$ranking = [];
		
		if ( $stmt->num_rows > 0 ) {
			
			while ( $stmt->fetch() ) {
				
				if ( $status != "participando" )
					continue;
				
				$ranking[] = [
					"utoken"	=> $utoken,
					"nome"		=> $nome,
					"nivel"		=> $nivel,
					"pontos"	=> $pontos,
					"icone"		=> $icone
				];
				
			}
			
		}
		
		return $ranking;
		
//		echo "<pre>";
//		echo json_encode($ranking, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
//		echo "</pre>";
		
	}
	
	/* Static Methods
	==================================================*/
	
	// Return true if the login is correct
	public static function login($email, $password) {
		
		$sql = "SELECT utoken, senha FROM ". TABLE_USUARIOS ." WHERE email = ? and ativo = 'sim'";
		
		$stmt = User::$conn->prepare($sql);
		$stmt->bind_param("s", $email);
		$stmt->execute();
		$stmt->store_result();
		
		if ( $stmt->num_rows == 1 ) {
			
			$stmt->bind_result($utoken, $senha);
			$stmt->fetch();
			
			if ( sha1($password) != $senha )
				throw new Exception( set_error("login_failed", "E-mail ou senha incorretos!"));
			else
				return $utoken;
			
		} else throw new Exception( set_error("login_failed", "E-mail ou senha incorretos!"));
		
	}
	
	// Check if the access key is valid
	public static function verify_access_key($access_key) {
		
		$sql = "SELECT access_key FROM ". TABLE_USUARIOS ." WHERE access_key = ? and ativo = 'sim'";
		
		$stmt = User::$conn->prepare($sql);
		$stmt->bind_param("s", $access_key);
		$stmt->execute();
		$stmt->store_result();
		
		// If access_key exists return true
		if ( $stmt->num_rows == 1 )
			return true;
		else
			throw new Exception( set_error("invalid_access_key", "Oops, a chave de acesso fornecida é inválida!"));
		
	}
	
	// Delete user (virtually)
	public static function delete($utoken) {
		
		$sql = "SELECT utoken FROM ". TABLE_USUARIOS ." WHERE utoken = ?";
		
		$stmt = User::$conn->prepare($sql);
		$stmt->bind_param("s", $utoken);
		$stmt->execute();
		$stmt->store_result();
		
		if ( $stmt->num_rows < 1 )
			throw new Exception( set_error("user_doesnt_exist", "Oops, o usuário informado não existe!"), 500);
		
		$sql = "UPDATE ". TABLE_USUARIOS ." SET ativo = 'nao' WHERE utoken = ? and ativo != 'nao'";
		
		$stmt = User::$conn->prepare($sql);
		$stmt->bind_param("s", $utoken);
		$stmt->execute();
		
	}
	
	// Get the user type
	public static function getType($utoken, $gtoken) {
		
		$user = new User();
		$user->getUser($utoken);
		
		$group = new Group();
		$group->getGroup($gtoken);
		
		$id_usuario = $user->getId_usuario();
		$id_grupo = $group->getId_grupo();
		
		$sql = "SELECT tipo FROM ". TABLE_GRUPOS_USUARIOS ." WHERE id_usuario = ? AND id_grupo = ?";
		
		$stmt = User::$conn->prepare($sql);
		$stmt->bind_param("ii", $id_usuario, $id_grupo);
		$stmt->execute();
		$stmt->store_result();
		
		if ( $stmt->num_rows == 0 )
			throw new Exception( set_error("user_isnt_in_group", "O usuário não está no grupo!"));
		
		$stmt->bind_result($tipo);
		$stmt->fetch();
		return $tipo;
	}
	
}

User::$conn = $conn;


try {
	
//	$user = new User();
//	$user->getUser("6BF4477B");
//	$grupos = $user->getGroups();
//	$user->getMissions("CD6C14D0");
//	$user->getRanking("CD6C14D0");
	
	// Criar usuário
	/*
	$user = new User();
	$user->setNome("Person 1");
	$user->setEmail("person1@gmail.com");
	$user->setSenha("person1");
	$user->create();
	*/
	
	// Criar grupo
	/*
	$user = new User();
	$user->getUser("4FB71448");
	
	$group = new Group();
	$group->setNome("Person 1 Group");
	$group->setIcone(1);
	$user->create_group($group);
	*/
	
	// Entra em grupo
	/*
	$user = new User();
	$user->getUser("4FB71448");
	$user->enter_group("CD6C14D0");
	
	
	$users = ["F29A2D9A", "2C367557", "562B653D", "EC270E78", "E3DA1C05"];
	foreach ( $users as $utoken ) {
		
		$user = new User();
		$user->getUser($utoken);
		$user->enter_group("CD6C14D0");
		
	}
	*/
	
	
	
	
} catch (Exception $e) {
	$error = unserialize($e->getMessage());
	echo json_encode($error, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

	
	
	
	