<?php

  session_save_path($_SERVER["DOCUMENT_ROOT"] . '/session');
  
	if  (session_status() == PHP_SESSION_NONE )
    session_start();

	if ( !isset($_SESSION["gm_user_active"]) )
		header("Location: /login");

?>
<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<title>Gamify</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no"/>
		<link rel="manifest" href="assets/js/manifest.json?v0.0.1">
		<link rel="stylesheet" href="assets/sass/main.css?v0.0.2">
		<link rel="shortcut icon" type="image/png" href="assets/images/logo/icon-192x192.png"/>
	</head>
	<body>
		
		<!----------- Includes ---------->
		
		<!-- Grupos -->
		<?php require_once "views/grupos/criar_grupo.php"; ?>
		<?php require_once "views/grupos/editar_grupo.php"; ?>
		<?php require_once "views/grupos/info_grupo.php"; ?>
		<?php require_once "views/grupos/entrar_grupo.php"; ?>
		
		<!-- Missões -->
		<?php require_once "views/missoes/criar_missao.php"; ?>
		<?php require_once "views/missoes/editar_missao.php"; ?>
		
		<!-- Sidenav -->
		<?php require_once "views/sidenav/sidenav.php"; ?>
		<?php require_once "views/sidenav/sobre.php"; ?>
		<?php require_once "views/sidenav/editar_perfil.php"; ?>
		
		<!-- Modais -->
		<?php require_once "views/modais.php"; ?>
		
		<!-- Infos grupos -->
		<div id="info-grupos"></div>
		
		<!-- Menus (need to be the last) -->
		<?php require_once "views/navbar.php"; ?>
		
		<!----------- MAIN PAGE ---------->
		<div id="main-page">
			
			<!-- Section Grupos -->
			<section id="grupos">
			
				<div class="fixed-action-btn click-to-toggle">
					<a class="btn-floating btn-large teal"><i class="material-icons waves-effect fs-2">menu</i></a>
					<ul>
						<li>
							<a onclick="showPage('#entrar-em-grupo')" class="btn-floating pink"><i class="material-icons waves-effect">group_add</i></a>
							<a onclick="showPage('#entrar-em-grupo')" href="#" class="btn-floating mobile-fab-tip">Entrar em grupo</a>
						</li>
						<li>
							<a onclick="showPage('#criar-grupo')" class="btn-floating blue"><i class="material-icons waves-effect">add</i></a>
							<a onclick="showPage('#criar-grupo')" class="btn-floating mobile-fab-tip">Criar grupo</a>
						</li>
					</ul>
				</div>
				
				<div id="grupos-loading" class="center loading" >
					<div class="preloader-wrapper small active">
						<div class="spinner-layer spinner-teal-only">
							<div class="circle-clipper left">
								<div class="circle"></div>
							</div>
							<div class="gap-patch">
								<div class="circle"></div>
							</div>
							<div class="circle-clipper right">
								<div class="circle"></div>
							</div>
						</div>
					</div>
				</div>
				
				<div id="grupos-content" style="display: none;"></div>
				
			</section>
			
			<!-- Section Missões -->
			<section id="missoes">
			
				<div id="missoes-loading" class="center loading" style="display: none;">
					<div class="preloader-wrapper small active">
						<div class="spinner-layer spinner-teal-only">
							<div class="circle-clipper left">
								<div class="circle"></div>
							</div>
							<div class="gap-patch">
								<div class="circle"></div>
							</div>
							<div class="circle-clipper right">
								<div class="circle"></div>
							</div>
						</div>
					</div>
				</div>
				
				<div id="missoes-content" style="display: none;"></div>
				
			</section>

			<!-- Section Ranking -->
			<section id="ranking">
			
				<div id="ranking-loading" class="center loading" style="display: none;">
					<div class="preloader-wrapper small active">
						<div class="spinner-layer spinner-teal-only">
							<div class="circle-clipper left">
								<div class="circle"></div>
							</div>
							<div class="gap-patch">
								<div class="circle"></div>
							</div>
							<div class="circle-clipper right">
								<div class="circle"></div>
							</div>
						</div>
					</div>
				</div>
				
				<div id="ranking-content" style="display: none;"></div>
			
			</section>
		
		</div> 
		
		
		<!---------- NOTIFICAÇÕES ---------->
		<section id="notificacoes" style="display: none;">
			
      <div id="notificacoes-loading" class="center loading" style="display: none;">
        <div class="preloader-wrapper small active">
          <div class="spinner-layer spinner-teal-only">
            <div class="circle-clipper left">
              <div class="circle"></div>
            </div>
            <div class="gap-patch">
              <div class="circle"></div>
            </div>
            <div class="circle-clipper right">
              <div class="circle"></div>
            </div>
          </div>
        </div>
      </div>
				
      <div id="notificacoes-content" style="display: none;"></div>
			
		</section>
		
		
		<!---------- Scripts Imports ---------->
		<script> var utoken = "<?php echo $_SESSION["gm_utoken"]; ?>"; </script>
		<script src="/assets/js/jquery-3.2.1.min.js"></script>
		<script src="/assets/js/materialize.min.js"></script>
		<script src="/assets/js/main.js?v0.0.4"></script>
		<script src="/src/controllers/main.js?v0.0.0"></script>
		<script src="/src/controllers/user.js?v0.0.0"></script>
		<script src="/src/controllers/mentor.js?v0.0.0"></script>
		<script src="/src/controllers/mod.js?v0.0.0"></script>
		<script src="/src/controllers/player.js?v0.0.0"></script>
	</body>
</html>