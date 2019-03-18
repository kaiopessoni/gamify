<?php

require_once "User.php";

class Moderator extends User {

	/* Public Methods
	==================================================*/
	public function edit_group(Group $group) {

		$gtoken = $group->getGtoken();
		Group::group_exists($gtoken);

		$type = User::getType($this->getUtoken(), $gtoken);

		if ( $type == "jogador/moderador" || $type == "mentor/moderador" ) {

			$nome = $group->getNome();
			$icone = $group->getIcone();

			$sql = "UPDATE ". TABLE_GRUPOS ." SET nome = ?, icone = ? WHERE gtoken = ?";
			$stmt = User::$conn->prepare($sql);
			$stmt->bind_param("sis", $nome, $icone, $gtoken);
			$stmt->execute();

		} else throw new Exception( set_error("permission_denied", "Você não é moderador para realizar essa ação!"), 403);

	}

	public function delete_user($utoken, $gtoken) {

		Group::group_exists($gtoken);

		$group = new Group();
		$group->getGroup($gtoken);
		$id_grupo = $group->getId_grupo();

		$user = new User();
		$user->getUser($utoken);
		$id_usuario = $user->getId_usuario();

		$type = User::getType($this->getUtoken(), $gtoken);

		if ( $type == "jogador/moderador" || $type == "mentor/moderador" ) {

			$sql = "DELETE FROM ". TABLE_GRUPOS_USUARIOS ." WHERE id_grupo = ? and id_usuario = ?";
			$stmt = User::$conn->prepare($sql);
			$stmt->bind_param("ii", $id_grupo, $id_usuario);
      $stmt->execute();

		} else throw new Exception( set_error("permission_denied", "Você não é moderador para realizar essa ação!"), 403);

	}

	public function change_type_of_user($utoken, $gtoken, $tipo) {

		Group::group_exists($gtoken);

		$group = new Group();
		$group->getGroup($gtoken);
		$id_grupo = $group->getId_grupo();

		$user = new User();
		$user->getUser($utoken);
		$id_usuario = $user->getId_usuario();

		$type = User::getType($this->getUtoken(), $gtoken);

		if ( $type == "jogador/moderador" || $type == "mentor/moderador" ) {

			$tipos = ["jogador", "mentor", "jogador/moderador", "mentor/moderador"];

			if ( !in_array($tipo, $tipos) )
				throw new Exception( set_error("invalid_type", "O tipo informado é inválido!"), 400);

			$sql = "UPDATE ". TABLE_GRUPOS_USUARIOS ." SET tipo = ? WHERE id_grupo = ? and id_usuario = ?";
			$stmt = User::$conn->prepare($sql);
			$stmt->bind_param("sii", $tipo, $id_grupo, $id_usuario);
			$stmt->execute();

		} else throw new Exception( set_error("permission_denied", "Você não é moderador para realizar essa ação!"), 403);

	}

	public function change_status_of_user($utoken, $gtoken, $status) {

		Group::group_exists($gtoken);

		$group = new Group();
		$group->getGroup($gtoken);
		$id_grupo = $group->getId_grupo();

		$user = new User();
		$user->getUser($utoken);
		$id_usuario = $user->getId_usuario();

		$type = User::getType($this->getUtoken(), $gtoken);

		if ( $type == "jogador/moderador" || $type == "mentor/moderador" ) {

			if ( $status != "participando" && $status != "bloqueado" && $status != "removido" )
				throw new Exception( set_error("invalid_status", "O status informado é inválido!"), 400);

			$sql = "UPDATE ". TABLE_GRUPOS_USUARIOS ." SET status = ? WHERE id_grupo = ? and id_usuario = ?";
			$stmt = User::$conn->prepare($sql);
			$stmt->bind_param("sii", $status, $id_grupo, $id_usuario);
			$stmt->execute();

		} else throw new Exception( set_error("permission_denied", "Você não é moderador para realizar essa ação!"), 403);

	}


}

try {
	
	
	
	$group = new Group();
	$group->getGroup("CD6C14D0");
	
	$mod = new Moderator();
	$mod->getUser("EC270E78");
	
	// Muda o tipo do participante
	/*
	$user = new User();
	$user->getUser("E3DA1C05");
	$mod->change_type_of_user($user->getUtoken(), $group->getGtoken(), "mentor");
	*/
	
	
	// Muda o status do participante no grupo
//	/*
	$user = new User();
	$user->getUser("E3DA1C05");
	$mod->change_status_of_user($user->getUtoken(), $group->getGtoken(), "participando");
//	*/
	
	
	
//	$user->enter_group("D46F3372");
	
//	$mod->change_status_of_user($user->getUtoken(), $group->getGtoken(), "participando");
//	$mod->delete_user(D46F3372, $group->getGtoken());
	
	
	
} catch (Exception $e) {
	$error = unserialize($e->getMessage());
	echo json_encode($error, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

	

