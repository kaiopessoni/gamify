<section id="info-grupo" class="new-page">
	
	<div class="row nav z-depth-1 teal valign-wrapper">
		<div class="col s12 center">
			<a class="back valign-wrapper left" onclick="hidePage('#info-grupo')"><i class="material-icons white-text small">chevron_left</i></a>
			<span class="title">Informações do Grupo</span>
		</div>
	</div>

	<div class="container content">
	
		<!-- Informações do Grupo -->
		<div class="row valign-wrapper">
			<!-- Ícone e Código do Grupo -->
			<div class="col s4 center">
				<img src="assets/images/icons/icon-4.png" class="avatar">
				<br> 
				<span class="codigo-grupo"><?php echo strtoupper(substr(sha1(123 . time()), 0, 8));?></span>
				<br>
				<a href="#modal-codigo-grupo" class="modal-trigger" style="font-size: .85rem">O que é isso?</a>
			</div>
			
			<div class="col s8">
				<!-- Nome do Grupo -->
				<div class="row">
					<div class="col s12 bold">ADS Fatec 6º Ciclo</div>
				</div>
				
				<!-- Qtd Participantes / Missões do grupo -->
				<div class="row spc-5">
					<div class="col s12 teal-text valign-wrapper">
						<i class="material-icons">supervisor_account</i>
						<span class="players-counter bold">25</span>&nbsp;participantes
					</div>
				</div>
				<div class="row">
					<div class="col s12 teal-text valign-wrapper">
						<i class="material-icons">format_list_bulleted</i>
						<span class="missions-counter bold">7</span>&nbsp;missões
					</div>
				</div>
			</div>
			
		</div>
		<div class="divider spc-5"></div>
	
		<!-- Lista de participantes -->
		
		<ul id="user-settings-dropdown" class="dropdown-content">
			<li><a>Tornar Moderador</a></li>
			<li><a>Tornar Mentor</a></li>
			<li><a>Tornar Jogador</a></li>
			<li><a>Tirar Moderador</a></li>
			<li class="mb-0"><a onclick="confirm('excluir_grupo', 'UID')">Remover</a></li>
		</ul>
		
		<ul id="user-list" class="row mb">
		
			<li class="col s12 spc-5">
				<div class="row valign-wrapper">
					<div class="col s2 valign-wrapper"><img src="assets/images/icons/icon-2.png" class="avatar small"></div>
					<div class="col s8">
						<div class="row">
							<div class="col s12 user-name">Kaio Pessoni</div>
							<div class="col s12">
								<i class="material-icons user-badge">verified_user</i>
								<i class="material-icons user-badge">gamepad</i>
							</div>
						</div>
					</div>
					<div class="col s2 center"><i class="material-icons more-icon dropdown-trigger settings-dropdown" data-target="user-settings-dropdown">more_vert</i></div>
				</div>
			</li>
		
			<li class="col s12 spc-5">
				<div class="row valign-wrapper">
					<div class="col s2 valign-wrapper"><img src="assets/images/icons/icon-8.png" class="avatar small"></div>
					<div class="col s8">
						<div class="row">
							<div class="col s12 user-name">Silvia Martins</div>
							<div class="col s12">
								<i class="material-icons user-badge">verified_user</i>
								<i class="material-icons user-badge">school</i>
							</div>
						</div>
					</div>
					<div class="col s2 center"><i class="material-icons more-icon dropdown-trigger settings-dropdown" data-target="user-settings-dropdown">more_vert</i></div>
				</div>
			</li>
			
			<li class="col s12 spc-5">
				<div class="row valign-wrapper">
					<div class="col s2 valign-wrapper"><img src="assets/images/icons/icon-12.png" class="avatar small"></div>
					<div class="col s8">
						<div class="row">
							<div class="col s12 user-name">Pedro Goulart</div>
							<div class="col s12">
								<i class="material-icons user-badge">gamepad</i>
							</div>
						</div>
					</div>
					<div class="col s2 center"><i class="material-icons more-icon dropdown-trigger settings-dropdown" data-target="user-settings-dropdown">more_vert</i></div>
				</div>
			</li>
			
			<li class="col s12 spc-5">
				<div class="row valign-wrapper">
					<div class="col s2 valign-wrapper"><img src="assets/images/icons/icon-5.png" class="avatar small"></div>
					<div class="col s8">
						<div class="row">
							<div class="col s12 user-name">Luís Fernando</div>
							<div class="col s12">
								<i class="material-icons user-badge">gamepad</i>
							</div>
						</div>
					</div>
					<div class="col s2 center"><i class="material-icons more-icon dropdown-trigger settings-dropdown" data-target="user-settings-dropdown">more_vert</i></div>
				</div>
			</li>
			
		</ul>
	
		<!-- Usuários Bloqueados -->
		<div class="row spc-13">
			<div class="col s12 bold">
				Usuários Bloqueados
				<div class="divider"></div>
			</div>
			
			<div class="col s12">
				<ul class="row">
				
					<li class="col s12 spc-3">
						<div class="row valign-wrapper">
							<div class="col s8 truncate">Gabriel Rodrigues</div>
							<div class="col s4"><span class="valign-wrapper right fs-9">Desbloquear&nbsp;<i class="material-icons fs-12">block</i></span></div>
						</div>
					</li>
					
					<li class="col s12 spc-3">
						<div class="row valign-wrapper">
							<div class="col s8 truncate">Isabela Nicolau</div>
							<div class="col s4"><span class="valign-wrapper right fs-9">Desbloquear&nbsp;<i class="material-icons fs-12">block</i></span></div>
						</div>
					</li>
					
				</ul>
			</div>
		</div>
	
	</div>
	
	<div id="modal-codigo-grupo_" class="modal middle">
    <div class="modal-content">
    	<h6 class="bold valign-wrapper"><i class="material-icons grey-text text-darken-3">info</i>&nbsp;Código do Grupo</h6>
      <p>Este é o <strong>código único do grupo</strong>, através dele outras pessoas conseguem participar deste grupo caso os moderadores aceitem.</p>
    </div>
  </div>
	
</section>