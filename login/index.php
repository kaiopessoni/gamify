<?php

	session_save_path($_SERVER["DOCUMENT_ROOT"] . '/session');

  session_start();

	if ( isset($_SESSION["gm_user_active"]) )
		header("Location: /");

?>
<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<title>Gamify - Login</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no"/>
		<link rel="manifest" href="/assets/js/manifest.json?v0.0.1">
		<link rel="stylesheet" href="/assets/sass/main.css?v0.0.1">
		<link rel="shortcut icon" type="image/png" href="/assets/images/logo/icon-192x192.png"/>
	</head>
	<body>
	
		<!----------- Includes ---------->
		
		<!-- Login -->
		<?php require_once "../views/login/cadastro.php"; ?>
		<?php require_once "../views/login/esqueceu_senha.php"; ?>
		
		<!-- Modais -->
		<?php require_once "../views/modais.php"; ?>
	
		<section id="login" class="container">
		
			<div class="center" style="padding-top: 10%;"><img src="/assets/images/logo/logo%20wide.png" width="250" alt="logo-gamify"></div>
			
			<form id="form-login" class="col s12 container">

				<div class="row">
					<div class="input-field col s12">
						<input type="text" id="email_login" name="email">
						<label for="email_login">E-mail</label>
					</div>
				</div>

				<div class="row">
					<div class="input-field col s12">
						<input type="password" id="senha_login" name="senha">
						<label for="senha_login">Senha</label>
					</div>
				</div>

				<div class="row">
					<div class="col s12">
						<p>
							<a onclick="showPage('#esqueceu-senha')" class="fs-9">ESQUECEU SUA SENHA?</a> <br>
							<a onclick="showPage('#cadastro')"  class="fs-9">NÃO TEM CADASTRO?</a>
						</p>
					</div>
				</div>

				<div class="row">
					<div class="input-field col s12 center">
						<a id="btn-fazer-login" class="btn teal waves-effect waves-light">Fazer Login</a>
					</div>
				</div>

			</form>
			
		</section>
		
		<!---------- Scripts Imports ---------->
		<script src="/assets/js/jquery-3.2.1.min.js"></script>
		<script src="/assets/js/materialize.min.js"></script>
		<script src="/assets/js/main.js?v0.0.3"></script>
		<script src="/src/controllers/main.js?v0.0.0"></script>
		<script src="/src/controllers/user.js?v0.0.0"></script>
	</body>
</html>