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
    
    return $gtoken;
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
		$sql = "SELECT id_grupos_usuarios, status FROM ". TABLE_GRUPOS_USUARIOS ." WHERE id_grupo = ? and id_usuario = ?";
		$stmt = User::$conn->prepare($sql);
		$stmt->bind_param("ii", $id_grupo, $id_usuario);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($id_row, $status);
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
      
      // Caso já exista o registro do usuário com o grupo, atualiza o status para pendente
			$sql = "UPDATE ". TABLE_GRUPOS_USUARIOS ." SET status = 'pendente' WHERE id_grupos_usuarios = ?";

      $stmt = User::$conn->prepare($sql);
      $stmt->bind_param("i", $id_row);
      $stmt->execute();
			
		} else {
			
			// Coloca o status do usuário com o grupo como pendente
			$sql = "INSERT INTO ". TABLE_GRUPOS_USUARIOS ." (id_grupo, id_usuario, nivel, pontos, tipo, status)
				      VALUES (?, ?, 1, 0, 'jogador', 'pendente')";
		
			$stmt = User::$conn->prepare($sql);
			$stmt->bind_param("ii", $id_grupo, $id_usuario);
			$stmt->execute();
			
		}
		
  }
  
  public function exit_group($gtoken) {

    Group::group_exists($gtoken);
		
		$group = new Group();
		$group->getGroup($gtoken);
		
		$id_grupo 	= $group->getId_grupo();
    $id_usuario = $this->getId_usuario();
    
		// Recebe o status do usuário com o grupo caso exista
		$sql = "UPDATE ". TABLE_GRUPOS_USUARIOS ." SET status = 'saiu' WHERE id_grupo = ? and id_usuario = ?";
		$stmt = User::$conn->prepare($sql);
		$stmt->bind_param("ii", $id_grupo, $id_usuario);
		$stmt->execute();
			
  }
	
	public function getGroups() {
		
		$id_usuario = $this->getId_usuario();
		
		// Recebe os ids dos grupos que o usuário faz parte
		$sql = "SELECT gu.id_grupo 
            FROM ". TABLE_GRUPOS_USUARIOS ." gu
            INNER JOIN ". TABLE_GRUPOS ." g
              ON g.id_grupo = gu.id_grupo
            WHERE id_usuario = ? and status = 'participando' and g.ativo = 'sim';";
            
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
									(SELECT count(id_usuario) FROM ". TABLE_GRUPOS_USUARIOS ." WHERE id_grupo = ? AND status ='participando') 'qtd_participantes',
									(SELECT count(id_missao) FROM ". TABLE_MISSOES ." m WHERE id_grupo = ? AND g.ativo = 'sim' AND m.ativo = 'sim') 'qtd_missoes'
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
									status = 'participando' AND id_grupo = ?
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
                WHERE	ativo = 'sim' AND status = 'participando' AND id_grupo = ?
                ORDER BY tipo DESC, nome ASC";
				
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
					
          $sql = "SELECT gtoken, utoken, u.nome FROM ". TABLE_GRUPOS_USUARIOS ." gu
                  INNER JOIN ". TABLE_USUARIOS ." u ON u.id_usuario = gu.id_usuario
                  INNER JOIN ". TABLE_GRUPOS ." g ON g.id_grupo = gu.id_grupo
                  WHERE u.ativo = 'sim' AND status = 'bloqueado' AND gu.id_grupo = ?";

          $stmt = User::$conn->prepare($sql);
					$stmt->bind_param("i", $id_grupo);
					$stmt->execute();
					$stmt->bind_result($gtoken_sql, $utoken, $nome_bloqueado);

					while ( $stmt->fetch() ) { 
						$bloqueados[] = [
							"gtoken" 	=> $gtoken_sql,
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
		
	}
	
	public function getMissions($gtoken) {
		
		Group::group_exists($gtoken);
		
		$group = new Group();
		$group->getGroup($gtoken);
    
    $tipo = User::getType($this->getUtoken(), $gtoken);

		$id_grupo = $group->getId_grupo();
		
		$sql = "SELECT mtoken, utoken 'criador', u.nome, m.nome, descricao, prazo, recompensa	FROM ". TABLE_MISSOES ." m 
						INNER JOIN usuarios u ON m.id_usuario = u.id_usuario
						WHERE  id_grupo = ? and m.ativo = 'sim'
						ORDER BY m.id_missao DESC";
		
		$stmt = User::$conn->prepare($sql);
		$stmt->bind_param("i", $id_grupo);
		$stmt->execute();
		$stmt->bind_result($mtoken, $criador, $nome_criador, $nome_missao, $descricao, $prazo, $recompensa);
		$stmt->store_result();
		
		$missoes = [];
		
		if ( $stmt->num_rows > 0 ) {
			
			while ( $stmt->fetch() ) {
				
				if ( $tipo == "jogador" || $tipo == "jogador/moderador" ) {
          
          $sql = "SELECT status, recompensa_final from ". TABLE_MISSOES_JOGADORES ." WHERE 
                  id_missao = (select id_missao from missoes where mtoken = ?) AND
                  id_usuario = (select id_usuario from usuarios where utoken = ?)";

          $utoken = $this->getUtoken();
          $stmt2 = User::$conn->prepare($sql);
          $stmt2->bind_param("ss", $mtoken, $utoken);
          $stmt2->execute();
          $stmt2->bind_result($status, $recompensa_final);
          $stmt2->store_result();
          $stmt2->fetch();

					if ( strtotime(date("Y-m-d")) > strtotime($prazo) && $status != "completada" )
						$status = "expirada";
          else if ( $status != "pendente" && $status != "completada" )
            $status = "ativa";

				} else {
          $status = "";
					$recompensa_final = "";
        }
					
				$prazo 			= date('d-m-Y', strtotime($prazo));
				$prazo 			= str_replace('-', '/', $prazo);
				
				$missoes[] = [
					"mtoken" 						=> $mtoken,
					"criador" 					=> $criador,
					"nome_criador"			=> $nome_criador,
					"nome" 							=> $nome_missao,
					"descricao" 				=> nl2br($descricao),
					"prazo" 						=> $prazo,
					"recompensa" 				=> $recompensa,
					"status" 						=> $status,
					"recompensa_final" 	=> $recompensa_final
				];
				
			}
			
    }

    if ( $tipo == "jogador" || $tipo == "jogador/moderador" ) {

      // Ordena as missões
      $ordered_missions = [];

      foreach ( $missoes as $mission ) {
        if ( $mission["status"] == "ativa" )
          $ordered_missions[] = $mission;
      }

      foreach ( $missoes as $mission ) {
        if ( $mission["status"] == "pendente" )
          $ordered_missions[] = $mission;
      }

      foreach ( $missoes as $mission ) {
        if ( $mission["status"] == "completada" )
          $ordered_missions[] = $mission;
      }

      foreach ( $missoes as $mission ) {
        if ( $mission["status"] == "expirada" )
          $ordered_missions[] = $mission;
      }
      
      return $ordered_missions;

    } else return $missoes;
    
    
		
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
              (tipo = 'jogador' OR tipo = 'jogador/moderador') AND gu.status = 'participando' AND u.ativo = 'sim' AND id_grupo = ?
            ORDER BY pontos DESC;";
		
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
		
  }
  
  public function getNotifications($gtoken) {

    Group::group_exists($gtoken);

    $notifications["participar_grupo"]    = [];
    $notifications["confirmar_missoes"]   = [];
    $notifications["missoes_completadas"] = [];

    $utoken = $this->getUtoken();
    $tipo = User::getType($utoken, $gtoken);
    
    
    if ( $tipo == "jogador/moderador" || $tipo == "mentor/moderador" ) {

      $sql = "SELECT utoken, gtoken, u.nome, g.nome FROM ". TABLE_GRUPOS_USUARIOS ." gu
              INNER JOIN ". TABLE_USUARIOS ." u ON u.id_usuario = gu.id_usuario
              INNER JOIN ". TABLE_GRUPOS ." g ON g.id_grupo = gu.id_grupo
              WHERE g.ativo = 'sim' AND gtoken = ? AND gu.status = 'pendente'
              ORDER BY id_grupos_usuarios ASC;";
      
      $stmt = User::$conn->prepare($sql);
      $stmt->bind_param("s", $gtoken);
      $stmt->execute();
      $stmt->bind_result($utoken_sql, $gtoken_sql, $nome_usuario, $nome_grupo);
      $stmt->store_result();

      if ( $stmt->num_rows > 0 ) {
        while ( $stmt->fetch() ) {

          $notifications["participar_grupo"][] = [
            "utoken"	      => $utoken_sql,
            "gtoken"		    => $gtoken_sql,
            "nome_usuario"  => $nome_usuario,
            "nome_grupo"	  => $nome_grupo,
          ];

        }
      }

    }

    if ( $tipo == "mentor" || $tipo == "mentor/moderador" ) {

      $sql = "SELECT utoken, mtoken, u.nome, m.nome, prazo FROM ". TABLE_MISSOES_JOGADORES ." mj
              INNER JOIN ". TABLE_MISSOES ." m ON m.id_missao = mj.id_missao
              INNER JOIN ". TABLE_USUARIOS ." u ON u.id_usuario = mj.id_usuario
              INNER JOIN ". TABLE_GRUPOS ." g ON g.id_grupo = m.id_grupo
              WHERE g.ativo = 'sim' AND mj.status = 'pendente' AND gtoken = ?
              AND m.id_usuario = (SELECT id_usuario FROM ". TABLE_USUARIOS ." WHERE utoken = ?)
              ORDER BY id_missoes_jogadores;";
      
      $stmt = User::$conn->prepare($sql);
      $stmt->bind_param("ss", $gtoken, $utoken);
      $stmt->execute();
      $stmt->bind_result($utoken_sql, $mtoken_sql, $nome_jogador, $nome_missao, $prazo);
      $stmt->store_result();

      $date_now = Date("Y-m-d");

      if ( $stmt->num_rows > 0 ) {
        while ( $stmt->fetch() ) {

          // Se a missão não tiver expirada
          if ( strtotime($prazo) >= strtotime($date_now) ) {
            $notifications["confirmar_missoes"][] = [
              "utoken"	      => $utoken_sql,
              "mtoken"		    => $mtoken_sql,
              "nome_jogador"  => $nome_jogador,
              "nome_missao"	  => $nome_missao,
            ];
          }

        }
      }

    }

    if ( $tipo == "jogador" || $tipo == "jogador/moderador" ) {

      $sql = "SELECT u.nome, m.nome, mj.recompensa_final FROM ". TABLE_MISSOES_JOGADORES ." mj
              INNER JOIN ". TABLE_MISSOES ." m ON m.id_missao = mj.id_missao
              INNER JOIN ". TABLE_USUARIOS ." u ON u.id_usuario = m.id_usuario
              INNER JOIN ". TABLE_GRUPOS ." g ON g.id_grupo = m.id_grupo
              WHERE g.ativo = 'sim' AND mj.status = 'completada' AND gtoken = ?
              AND mj.id_usuario = (SELECT id_usuario FROM ". TABLE_USUARIOS ." WHERE utoken = ?)
              ORDER BY id_missoes_jogadores ASC;";
      
      $stmt = User::$conn->prepare($sql);
      $stmt->bind_param("ss", $gtoken, $utoken);
      $stmt->execute();
      $stmt->bind_result($nome_jogador, $nome_missao, $recompensa_final);
      $stmt->store_result();

      if ( $stmt->num_rows > 0 ) {
        while ( $stmt->fetch() ) {

          $notifications["missoes_completadas"][] = [
            "nome_mentor"       => $nome_jogador,
            "nome_missao"	      => $nome_missao,
            "recompensa_final"  => $recompensa_final,
          ];

        }
      }

    }




		
		

		
		return $notifications;

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

  // kaio - 6BF4477B
  // ana - EC270E78
  // luis - F29A2D9A
  // neto - E3DA1C05

	// $user = new User();
  // $user->getUser("6BF4477B");
  // $notifications = $user->getNotifications("CD6C14D0");

  // echo "<pre>";
  // finish("success", "notifications_received", "Notificações recebida com scesso!", $notifications, "notificacoes");
  // echo json_encode($user->getMissions("CD6C14D0"), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  // echo "</pre>";

  // $user->exit_group("CD6C14D0");
	
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

	
	
	
	