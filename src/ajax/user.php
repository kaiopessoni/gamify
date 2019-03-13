<?php 

	/* Inicialização
  ========================================*/
	header('Content-Type: application/json');
	require_once "../db.php";
	require_once "../global.php";
	require_once "../classes/User.php";
	require_once "../classes/Group.php";
	session_start();
	
	set_time_limit(300);

	/* Recebimento dos dados
  ========================================*/
	get_data($data);
	
	/* Controller
  ========================================*/

	switch ($data["action"]) {
			
		/* Recebe as informações do usuário para editar o perfil
  	====================*/
		case "editar-perfil":
			
			try {
				
				$user = new User();
				$user->getUser($_SESSION["gm_utoken"]);
				
				$nome 						= $data["nome"];
				$icone 						= @$data["icone"];
				$senha_atual 			= $data["senha_atual"];
				$nova_senha 			= $data["nova_senha"];
				$confirmar_senha 	= $data["confirmar_senha"];
				$email 						= $data["email"];
				
				if ( empty($nome) || empty($icone) || empty($email) )
					finish("error", "empty_field", "Por favor, preencha todos os campos!");
				
				$user->setNome($nome);
				$user->setIcone($icone);
				$user->setEmail($email);
				
				if ( $senha_atual != "" ) {
					
					if ( empty($nova_senha) || empty($confirmar_senha) )
						finish("error", "empty_field", "Por favor, caso deseje alterar a senha, preencha todos os campos!");
					
					if ( $nova_senha != $confirmar_senha )
						finish("error", "invalid_new_pass", "Por favor, confirme a nova senha!");
					
					if ( sha1($senha_atual) != $user->getSenha() )
						finish("error", "invalid_pass", "Senha atual incorreta!");
					
					if ( $senha_atual == $nova_senha )
						finish("error", "invalid_new_pass", "Sua nova senha não pode ser igual a sua atual!");
					
					$user->setSenha($nova_senha);
				}
				
				$user->update();
				
				finish("success", "profile_updated", "Perfil atualizado com sucesso!");
				
			} catch (Exception $e) {
				$error = unserialize($e->getMessage());
				finish("error", $error["type"], $error["info"]);
			}
			
		break;
			
		/* Recebe as informações do usuário para editar o perfil
  	====================*/
		case "usuario-info":
			
			try {
				
				$user = new User();
				$user->getUser($_SESSION["gm_utoken"]);

				$array = [
					"nome" 	=> $user->getNome(),
					"icone" => $user->getIcone(),
					"email" => $user->getEmail()
				];
				
				finish("success", "user_info", "Info recebidas com sucesso!", $array, "info");
				
			} catch (Exception $e) {
				$error = unserialize($e->getMessage());
				finish("error", $error["type"], $error["info"]);
			}
			
		break;
			
		/* Login
  	====================*/
		case "fazer-login":
			
			try {
				
				$email = $data["email"];
				$senha = $data["senha"];

				if ( empty($email))
					finish("error", "empty_field", "Por favor, informe seu e-mail!");
				
				if ( empty($senha))
					finish("error", "empty_field", "Por favor, informe sua senha!");
				
				$utoken = User::login($email, $senha);
				
				$_SESSION["gm_user_active"] = true;
				$_SESSION["gm_utoken"] 			= $utoken;

				finish("success", "logged_in", "Logado com sucesso!");
				
			} catch (Exception $e) {
				$error = unserialize($e->getMessage());
				finish("error", $error["type"], $error["info"]);
			}
			
		break;
			
		/* Cadastrar usuário
  	====================*/
		case "cadastrar-usuario":
			
			try {
				
				$nome 	= $data["nome"];
				$email 	= $data["email"];
				$senha 	= $data["senha"];
				$senha2 = $data["senha2"];
				
				if ( empty($nome) || empty($email) || empty($senha) || empty($senha2) )
					finish("error", "empty_field", "Por favor, preencha todos os campos!");
					
				if ( $senha != $senha2 )
					finish("error", "different_passwords", "As senhas informadas não são identicas!");
				
				$user = new User();
				$user->setNome($nome);
				$user->setEmail($email);
				$user->setSenha($senha);
				$user->create();
				
				$_SESSION["gm_user_active"] = true;
				$_SESSION["gm_utoken"] 			= $user->getUtoken();
				
				finish("success", "user_created", "Usuário criado com sucesso!");
				
			} catch (Exception $e) {
				$error = unserialize($e->getMessage());
				finish("error", $error["type"], $error["info"]);
			}
			
		break;
		
		/* Criar grupo
  	====================*/
		case "criar-grupo":
			try {
				
				$nome = $data["nome"];
				$icone = @$data["icone"];
				
				if ( empty($nome) || empty($icone) )
					finish("error", "empty_field", "Por favor, preencha todos os campos!");
				
				$user = new User();
				$user->getUser($_SESSION["gm_utoken"]);
				
				$group = new Group();
				$group->setNome($nome);
				$group->setIcone($icone);
				$user->create_group($group);
				
				finish("success", "group_created", "Grupo criado com sucesso!");
				
			} catch (Exception $e) {
				$error = unserialize($e->getMessage());
				finish("error", $error["type"], $error["info"]);
			}
		break;
		
		/* Entrar em grupo
  	====================*/
		case "entrar-grupo":
			try {
				
				$gtoken = $data["codigo"];
				
				if ( empty($gtoken) )
					finish("error", "empty_field", "Por favor, preencha todos os campos!");
				
				if ( strlen($gtoken) != 8 )
					finish("error", "invalid_gtoken", "Por favor, informe um código válido!");
				
				$user = new User();
				$user->getUser($_SESSION["gm_utoken"]);
				$user->enter_group($gtoken);
				
				finish("success", "invite_sent", "Solicitação enviada com sucesso!");
				
			} catch (Exception $e) {
				$error = unserialize($e->getMessage());
				finish("error", $error["type"], $error["info"]);
			}
		break;
		
		/* Grupos usuário
  	====================*/
		case "grupos-usuario":
			try {
				
				$user = new User();
				$user->getUser($_SESSION["gm_utoken"]);
				$grupos = $user->getGroups();
				
				finish("success", "groups_received", "Grupos recebidos com scesso!", $grupos, "grupos");
				
			} catch (Exception $e) {
				$error = unserialize($e->getMessage());
				finish("error", $error["type"], $error["info"]);
			}
		break;
			
		/* Missões grupo
  	====================*/
		case "missoes-grupo":
			try {
				
				$user = new User();
				$user->getUser($_SESSION["gm_utoken"]);
				$missoes = $user->getMissions($data["gtoken"]);
				
				finish("success", "missions_received", "Missões recebidas com scesso!", $missoes, "missoes");
				
			} catch (Exception $e) {
				$error = unserialize($e->getMessage());
				finish("error", $error["type"], $error["info"]);
			}
		break;
			
		/* Ranking grupo
  	====================*/
		case "ranking-grupo":
			try {
				
				$user = new User();
				$user->getUser($_SESSION["gm_utoken"]);
				$ranking = $user->getRanking($data["gtoken"]);
				
				finish("success", "ranking_received", "Ranking recebido com scesso!", $ranking, "ranking");
				
			} catch (Exception $e) {
				$error = unserialize($e->getMessage());
				finish("error", $error["type"], $error["info"]);
			}
		break;
			
		default:
			finish("error", "invalid_action", "Ação desconhecida!");

	}
	


	

?> 