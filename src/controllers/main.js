/* Variáveis globais
==============================*/

var active_group;     // Grupo ativo
var tipo; 				    // Tipo do usuário no grupo
var confirm_action;   // Tipo de ação confirm
var confirm_data;     // Dados 


$(document).ready(() => {
	
	if ( window.location.pathname != "/login/" ) {
		getGrupos();
		getUsuarioInfo();
  }
  
  // Confirma ação
  $("#confirm-yes").click(() => {
    confirm();
  });
	
});



/* Funções auxiliares
==============================*/

function loading(action) {
	if ( action == "open" )
		$("#modal-loading").modal("open");
	else
		$("#modal-loading").modal("close");
}

function toast(status, message) {
	let color = ( status == "success" ) ? "green" : "red";
	M.toast({html: "<strong>" + message + "</strong>", classes: color})
}

// Sincroniza os dados
function sync() {
  getGrupos();
  getUsuarioInfo();
  toast("success", "Sincronização realizada com sucesso!");
}

// Abre a modal confirm e seta os dados a serem enviados
function confirmTrigger(action, data) {

  // Muda a pergunta do confirm
  if ( action == "ativar-grupo" )
    change_confirm_question("Deseja ativar este grupo?");

	$("#modal-confirm").modal("open");
  confirm_action = action;
  confirm_data = data;
}

// Muda a questão do confirm
function change_confirm_question(question) {
  // Default question
  question = question || "Tem certeza que deseja realizar esta ação?"; 
  $("#confirm-question").html(question);
}

// Excuta a ação confirmada | chamada quando clica em sim (confirm modal)
function confirm() {

  switch (confirm_action) {
    default:
      toast("error", "Ação não especificada!");
    break;
    case "change-user-type":
      changeUserType(confirm_data);
    break;
    case "ativar-grupo":
      ativarGrupo(confirm_data);
      break;
    case "sair-grupo":
      sairGrupo(confirm_data);
      break;
    case "excluir-grupo":
      excluirGrupo(confirm_data);
      break;
    case "completar-missao":
      completarMissao(confirm_data);
      break;
    }
  
  change_confirm_question(); // Volta a pergunta padrão
  confirm_action = false;
}




/* Ajax
==============================*/

// Recebe as informações dos grupos em que o usuário está
function getGrupos() {
	
	$("#grupos-content").hide();
	$("#grupos-loading").show();
	
	$.ajax({
		type: "GET",
		url: "/src/ajax/user.php",
		data: "action=grupos-usuario",
		success: (data) => {
			
			$("#grupos-content").html("");
			
			if ( data.grupos.length > 0 ) {
				
				var settings_grupo = ""; 															// Dropdown settings do grupo 
				var group_list = '<ul class="row mt-0 container">';		// Card do grupo
				var info_grupos = ""; 																// New page de info do grupo
				var settings_participantes = ""; 											// Dropdown settings do participante 
				var lista_particpantes = ""; 													// Lista de participantes
				var lista_bloqueados = ""; 														// Lista de participantes
				
				active_group = active_group || data.grupos[0].gtoken;
				$.each(data.grupos, (key, grupo) => {
				
					// Acha o tipo do usuário no grupo
					$.each(grupo.participantes, (key, participante) => {
						if ( participante.utoken == utoken )
							tipo = participante.tipo;
					});

					// Settings de cada grupo
					settings_grupo += " <ul id='group-settings-dropdown-"+ grupo.gtoken +"' class='dropdown-content'> \
                                <li><a onclick=\"showPage('#info-grupo-"+ grupo.gtoken +"')\">Ver Informações</a></li> \
                                <li><a onclick=\"confirmTrigger('sair-grupo', {'gtoken': '"+ grupo.gtoken +"'})\">Sair do Grupo</a></li>";
					
					if ( tipo == "jogador/moderador" || tipo == "mentor/moderador" )
						settings_grupo += " <li><a class='edit-group' data-gtoken="+ grupo.gtoken +">Editar Grupo</a></li> \
                                <li><a onclick=\"confirmTrigger('excluir-grupo', {'gtoken': '"+ grupo.gtoken +"'})\">Excluir Grupo</a></li> \
                              </ul>";
					else 
						settings_grupo += '</ul>';

					let active = ( active_group == grupo.gtoken ) ? "active" : "";
					
					// Card de cada grupo
					group_list += "<li class='col s12 border-bottom spc-5'> \
													<div class='row'> \
														<div class='col s3 center'> \
															<img src='assets/images/icons/icon-"+ grupo.icone +".png' class='avatar' onclick=\"confirmTrigger('ativar-grupo', {'gtoken': '"+ grupo.gtoken +"'})\"> \
															<br><span class='group-active "+ active +"' id='group-active-"+ grupo.gtoken +"'>Ativo</span> \
														</div> \
														<div class='col s9'> \
															<div class='row valign-wrapper'> \
																<div class='col s10 truncate bold' onclick=\"showPage('#info-grupo-"+ grupo.gtoken +"')\">"+ grupo.nome +"</div> \
																<div class='col s2 center valign-wrapper'> \
																	<i class='material-icons more-icon dropdown-trigger settings-dropdown' data-target='group-settings-dropdown-"+ grupo.gtoken +"'>more_vert</i> \
																</div> \
															</div> \
															<div class='row spc-3'> \
																<div class='col s9'> \
																	<ul class='top-rank'> \
																		<li class='gold truncate'>1º "+ grupo.podio[0] +"</li> \
																		<li class='silver truncate'>2º "+ grupo.podio[1] +"</li> \
																		<li class='bronze truncate'>3º "+ grupo.podio[2] +"</li> \
																	</ul> \
																</div> \
																<div class='col s3'> \
																	<div class='row'> \
																		<div class='col s12 valign-wrapper teal-text' style='margin-top: .5rem'> \
																			<i class='material-icons fs-15'>supervisor_account</i> \
																			<span class='players-counter'>"+ grupo.qtd_participantes +"</span> \
																		</div> \
																		<div class='col s12 valign-wrapper teal-text' style='margin-top: .3rem'> \
																			<i class='material-icons fs-15'>format_list_bulleted</i> \
																			<span class='missions-counter'>"+ grupo.qtd_missoes +"</span> \
																		</div> \
																	</div> \
																</div> \
															</div> \
														</div> \
													</div> \
													<div class='spc-5'></div> \
												</li>";
					
					// New page INFO do grupo
					info_grupos += '<section id="info-grupo-'+ grupo.gtoken +'" class="new-page"> \
														<div class="row nav z-depth-1 teal valign-wrapper"> \
															<div class="col s12 center"> \
																<a class="back valign-wrapper left" onclick="hidePage(\'#info-grupo-'+ grupo.gtoken +'\')"><i class="material-icons white-text small">chevron_left</i></a> \
																<span class="title">Informações do Grupo</span> \
															</div> \
														</div> \
														<div class="container content"> \
															<!-- Informações do Grupo --> \
															<div class="row valign-wrapper"> \
																<!-- Ícone e Código do Grupo --> \
																<div class="col s4 center"> \
																	<img src="assets/images/icons/icon-'+ grupo.icone +'.png" class="avatar"> \
																	<br>  \
																	<span class="codigo-grupo">'+ grupo.gtoken +'</span> \
																	<br> \
																	<a href="#modal-codigo-grupo" class="modal-trigger" style="font-size: .85rem">O que é isso?</a> \
																</div> \
																<div class="col s8"> \
																	<!-- Nome do Grupo --> \
																	<div class="row"> \
																		<div class="col s12 bold">'+ grupo.nome +'</div> \
																	</div> \
																	<!-- Qtd Participantes / Missões do grupo --> \
																	<div class="row spc-5"> \
																		<div class="col s12 teal-text valign-wrapper"> \
																			<i class="material-icons">supervisor_account</i> \
																			<span class="players-counter bold">'+ grupo.qtd_participantes +'</span>&nbsp;participantes \
																		</div> \
																	</div> \
																	<div class="row"> \
																		<div class="col s12 teal-text valign-wrapper"> \
																			<i class="material-icons">format_list_bulleted</i> \
																			<span class="missions-counter bold">'+ grupo.qtd_missoes +'</span>&nbsp;missões \
																		</div> \
																	</div> \
																</div> \
															</div> \
															<div class="divider spc-5"></div>';
					
					var badges = {
						"jogador": '<i class="material-icons user-badge">gamepad</i>',
						"jogador/moderador": '<i class="material-icons user-badge">verified_user</i> <i class="material-icons user-badge">gamepad</i>',
						"mentor": '<i class="material-icons user-badge">school</i>',
						"mentor/moderador": '<i class="material-icons user-badge">verified_user</i> <i class="material-icons user-badge">school</i>'
					};
					
					lista_particpantes = '<ul id="user-list" class="row mb">';
					
					$.each(grupo.participantes, (key, participante) => {
					
							if ( tipo == "jogador/moderador" || tipo == "mentor/moderador" ) {
                
                settings_participantes += "<ul id='user-settings-dropdown-"+ grupo.gtoken +"-"+ participante.utoken +"' class='dropdown-content'>";

                if ( participante.tipo == "jogador" || participante.tipo == "mentor" )
                  settings_participantes += "<li><a onclick=\"confirmTrigger('change-user-type', {'type': 'moderador', 'gtoken': '"+ grupo.gtoken +"', 'utoken': '"+ participante.utoken +"'})\">Tornar Moderador</a></li>";

                if ( participante.tipo != "mentor" && participante.tipo != "mentor/moderador" )
                  settings_participantes += "<li><a onclick=\"confirmTrigger('change-user-type', {'type': 'mentor', 'gtoken': '"+ grupo.gtoken +"', 'utoken': '"+ participante.utoken +"'})\">Tornar Mentor</a></li>";

                if ( participante.tipo != "jogador" && participante.tipo != "jogador/moderador" )
                  settings_participantes += "<li><a onclick=\"confirmTrigger('change-user-type', {'type': 'jogador', 'gtoken': '"+ grupo.gtoken +"', 'utoken': '"+ participante.utoken +"'})\">Tornar Jogador</a></li>";

                if ( participante.tipo == "jogador/moderador" || participante.tipo == "mentor/moderador" )
                  settings_participantes += "<li><a onclick=\"confirmTrigger('change-user-type', {'type': 'remover_moderador', 'gtoken': '"+ grupo.gtoken +"', 'utoken': '"+ participante.utoken +"'})\">Tirar Moderador</a></li>";

                settings_participantes += "<li class='mb-0'><a onclick=\"confirmTrigger('change-user-type', {'type': 'remover_do_grupo', 'gtoken': '"+ grupo.gtoken +"', 'utoken': '"+ participante.utoken +"'})\">Remover</a></li>";

                settings_participantes += "</ul>";
								
								lista_particpantes += '	<li class="col s12 spc-5"> \
																				<div class="row valign-wrapper"> \
																					<div class="col s2 valign-wrapper"><img src="assets/images/icons/icon-'+ participante.icone +'.png" class="avatar small"></div> \
																					<div class="col s8"> \
																						<div class="row"> \
																							<div class="col s12 user-name">'+ participante.nome +'</div> \
																							<div class="col s12"> \
																								'+ badges[participante.tipo] +' \
																							</div> \
																						</div> \
																					</div> \
																					<div class="col s2 center"><i class="material-icons more-icon dropdown-trigger settings-dropdown" data-target="user-settings-dropdown-'+ grupo.gtoken +'-'+ participante.utoken +'">more_vert</i></div> \
																				</div> \
																			</li>';
								
								if ( grupo.bloqueados.length > 0 ) {
									
									lista_bloqueados += '	<div class="row spc-13"> \
																					<div class="col s12 bold"> \
																						Usuários Bloqueados \
																						<div class="divider"></div> \
																					</div> \
																					<div class="col s12"> \
																						<ul class="row">';
									
									$.each(grupo.bloqueados, (key, bloqueado) => {
										
										lista_bloqueados += '	<li class="col s12 spc-3"> \
																						<div class="row valign-wrapper"> \
																							<div class="col s8 truncate">'+ bloqueado.nome +'</div> \
																							<div class="col s4"><span class="valign-wrapper right fs-9">Desbloquear&nbsp;<i class="material-icons fs-12">block</i></span></div> \
																						</div> \
																					</li>';
									});
																							
									lista_bloqueados += '			</ul> \
																					</div> \
																				</div>';
									
								} else {
									lista_bloqueados = '	<div class="row spc-13"> \
																					<div class="col s12 bold"> \
																						Usuários Bloqueados \
																						<div class="divider"></div> \
																					</div> \
																					<div class="col s12"> \
																						<p class="center">Nenhum usuário foi bloqueado!</p> \
																					</div> \
																				</div>';
								}
								
							} else {
								
								lista_particpantes += '	<li class="col s12 spc-5"> \
																					<div class="row valign-wrapper"> \
																						<div class="col s2 valign-wrapper"><img src="assets/images/icons/icon-'+ participante.icone +'.png" class="avatar small"></div> \
																						<div class="col s10"> \
																							<div class="row"> \
																								<div class="col s12 user-name">'+ participante.nome +'</div> \
																								<div class="col s12"> \
																									'+ badges[participante.tipo] +' \
																								</div> \
																							</div> \
																						</div> \
																					</div> \
																				</li>';
							}
							
					});
					
					lista_particpantes += '</ul>';
					
					
					info_grupos += settings_participantes;
					info_grupos += lista_particpantes;
					info_grupos += lista_bloqueados;
					
					info_grupos += '	</div> \
													</section>';
					
				});
				
				group_list += "</ul>";
				
				
				
				$("#grupos-content").html("");
				$("#grupos-content").append(settings_grupo);
				$("#grupos-content").append(group_list);
				$("#info-grupos").html(info_grupos);
				
				$('.dropdown-trigger').dropdown();
				$(".settings-dropdown").dropdown({
					constrainWidth: false
				});
				
			} else {
				
				active_group = "NULL";
				
				$("#grupos-content")
				.html('	<p class="center" style="margin-top: 40%">Você ainda não está em nenhum grupo!</p> \
								<p class="center">Clique <a onclick="showPage(\'#criar-grupo\')">aqui</a> para criar um grupo <br> ou <a onclick="showPage(\'#entrar-em-grupo\')">aqui</a> \
								para entrar em um grupo!</p>');
				
			}
			
			$("#grupos-loading").hide();
			$("#grupos-content").fadeIn();
			
			
			if ( active_group != "NULL" ) {
				getMissoes();
				getRanking();
			} else {
				
				$("#missoes-content").show().html('<p class="center" style="margin-top: 50%">Você ainda não está em nenhum grupo <br> para aparecer as missões!</p>');
				$("#ranking-content").show().html('<p class="center" style="margin-top: 50%">Você ainda não está em nenhum grupo <br> para aparecer o ranking!</p>');
				
			}
			
		},
		error: () => {
			toast("Verifique sua conexão com a internet!");
			loading("stop");
		}
	});
	
}

// Recebe as informações das missões do grupo ativo
function getMissoes() {
	
	$("#missoes-content").hide();
	$("#missoes-loading").show();
	
	$.ajax({
		type: "GET",
		url: "/src/ajax/user.php",
		data: "gtoken="+ active_group + "&action=missoes-grupo",
		success: (data) => {
			console.log(data)
			$("#missoes-content").html("");
			
			if ( data.missoes.length > 0 ) {
				
				var settings_missao = "";
				var lista_missoes = '<ul class="row mt-0 container">';
				var modais_missao = "";
				
				$.each(data.missoes, (key, missao) => {
					
					
					modais_missao += '<div id="modal-descricao-missao-'+ missao.mtoken +'" class="modal middle"> \
															<div class="modal-content"> \
																<h6 class="bold">'+ missao.nome +'</h6> \
																<p>'+ missao.descricao +'</p> \
															</div> \
														</div>\n';
					
					if ( tipo == "jogador" || tipo == "jogador/moderador" ) {
            
            let hide_action = (missao.status == "ativa") ? "" : "hide";
            let col_desc    = (missao.status == "ativa") ? "s4" : "s7";

						// Jogador
						lista_missoes += "<li class='col s12 border-bottom spc-13'> \
                                <div class='row valign-wrapper'> \
                                  <div class='col s9 truncate bold'>"+ missao.nome +"</div> \
                                  <div class='col s3 right-align'> \
                                    <span class='mission-status "+ missao.status +"'>"+ missao.status +"</span> \
                                  </div> \
                                </div> \
                                <div class='row'> \
                                  <div class='col s1'> \
                                    <i class='material-icons mission-icon'>school</i> \
                                  </div> \
                                  <div class='col s11'> \
                                    <span class='truncate mission-text'>"+ missao.nome_criador +"</span> \
                                  </div> \
                                </div> \
                                <div class='row valign-wrapper'> \
                                  <div class='col s5'> \
                                    <div class='row'> \
                                      <div class='col s12'> \
                                        <div class='row'> \
                                          <div class='col s2'><i class='material-icons mission-icon'>today</i></div> \
                                          <div class='col s9 mission-text'>&nbsp"+ missao.prazo +"</div> \
                                        </div> \
                                      </div> \
                                      <div class='col s12'> \
                                        <div class='row'> \
                                          <div class='col s2'><i class='material-icons mission-icon'>star</i></div> \
                                          <div class='col s9 mission-text'>&nbsp"+ missao.recompensa +" pontos</div> \
                                        </div> \
                                      </div> \
                                    </div> \
                                  </div> \
                                  <div class='col "+ col_desc +" center mission-action'> \
                                      <i class='material-icons mission-action-icon desc modal-trigger' href='#modal-descricao-missao-"+ missao.mtoken +"'>subject</i><br> \
                                      <span class='mission-text'>Ver Descrição</span> \
                                  </div> \
                                  <div class='col s3 center mission-action "+ hide_action +"'> \
                                    <i class='material-icons mission-action-icon' onclick=\"confirmTrigger('completar-missao', {'mtoken': '"+ missao.mtoken +"'})\">check</i><br> \
                                    <span class='mission-text'>Completar</span> \
                                  </div> \
                                </div> \
                                <div class='spc-13'></div> \
                              </li>";
						
						
					} else {
						
						// Criador da missão
						if ( missao.criador == utoken ) {

							settings_missao += '<ul id="mission-settings-dropdown-'+ missao.mtoken +'" class="dropdown-content"> \
																		<li><a onclick="showPage(\'#editar-missao\')">Editar Missão</a></li> \
																		<li><a onclick="confirm(\'excluir_missao\', \'UID\')">Excluir Missão</a></li> \
																	</ul>\n';
							
							lista_missoes += '<li class="col s12 border-bottom spc-13"> \
																	<div class="row valign-wrapper"> \
																		<div class="col s10 truncate bold">'+ missao.nome +'</div> \
																		<div class="col s2 center"> \
																			<i class="material-icons more-icon dropdown-trigger settings-dropdown" data-target="mission-settings-dropdown-'+ missao.mtoken +'">more_vert</i> \
																		</div> \
																	</div> \
																	<div class="row "> \
																		<div class="col s1"> \
																			<i class="material-icons mission-icon">school</i> \
																		</div> \
																		<div class="col s11"> \
																			 <span class="truncate mission-text">'+ missao.nome_criador +'</span> \
																		</div> \
																	</div> \
																	<div class="row valign-wrapper"> \
																		<div class="col s5"> \
																			<div class="row"> \
																				<div class="col s12"> \
																					<div class="row"> \
																						<div class="col s2"><i class="material-icons mission-icon">today</i></div> \
																						<div class="col s9 mission-text">&nbsp;'+ missao.prazo +'</div> \
																					</div> \
																				</div> \
																				<div class="col s12"> \
																					<div class="row"> \
																						<div class="col s2"><i class="material-icons mission-icon">star</i></div> \
																						<div class="col s9 mission-text">&nbsp;'+ missao.recompensa +' pontos</div> \
																					</div> \
																				</div> \
																			</div> \
																		</div> \
																		<div class="col s7 center mission-action"> \
																				<i class="material-icons mission-action-icon desc modal-trigger" href="#modal-descricao-missao-'+ missao.mtoken +'">subject</i><br> \
																				<span class="mission-text">Ver Descrição</span> \
																		</div> \
																	</div> \
																	<div class="spc-13"></div> \
																</li>';
						} else {
							
							// Mentor
							lista_missoes += '<li class="col s12 border-bottom spc-13"> \
																	<div class="row valign-wrapper"> \
																		<div class="col s12 truncate bold">'+ missao.nome +'</div> \
																	</div> \
																	<div class="row "> \
																		<div class="col s1"> \
																			<i class="material-icons mission-icon">school</i> \
																		</div> \
																		<div class="col s11"> \
																			 <span class="truncate mission-text">'+ missao.nome_criador +'</span> \
																		</div> \
																	</div> \
																	<div class="row valign-wrapper"> \
																		<div class="col s5"> \
																			<div class="row"> \
																				<div class="col s12"> \
																					<div class="row"> \
																						<div class="col s2"><i class="material-icons mission-icon">today</i></div> \
																						<div class="col s9 mission-text">&nbsp;'+ missao.prazo +'</div> \
																					</div> \
																				</div> \
																				<div class="col s12"> \
																					<div class="row"> \
																						<div class="col s2"><i class="material-icons mission-icon">star</i></div> \
																						<div class="col s9 mission-text">&nbsp;'+ missao.recompensa +' pontos</div> \
																					</div> \
																				</div> \
																			</div> \
																		</div> \
																		<div class="col s7 center mission-action"> \
																				<i class="material-icons mission-action-icon desc modal-trigger" href="#modal-descricao-missao-'+ missao.mtoken +'">subject</i><br> \
																				<span class="mission-text">Ver Descrição</span> \
																		</div> \
																	</div> \
																	<div class="spc-13"></div> \
																</li>';
						}
					}
				});
				
				
			} else
				$("#missoes-content").html('<p class="center" style="margin-top: 40%">Este grupo ainda não possui nenhuma missão!</p>');
			
			if ( tipo == "mentor" || tipo == "mentor/moderador" )
				$("#missoes-content").append('<div class="fixed-action-btn"> \
																				<a onclick="showPage(\'#criar-missao\')" class="btn-floating btn-large teal"><i class="large material-icons waves-effect">add</i></a> \
																			</div>');
			
			$("#missoes-content").append(modais_missao);
			$("#missoes-content").append(settings_missao);
			$("#missoes-content").append(lista_missoes);
			
			$('.modal').modal();
			$('.modal.middle').modal({
				endingTop: '33%'
			});
			$('.dropdown-trigger').dropdown();
			$(".settings-dropdown").dropdown({
				constrainWidth: false
			});
			
			$("#missoes-loading").hide();
			$("#missoes-content").fadeIn();

		},
		error: () => {
			toast("Verifique sua conexão com a internet!");
			loading("stop");
		}
	});
	
}

function getRanking() {
	
	$("#ranking-content").hide();
	$("#ranking-loading").show();
	
	$.ajax({
		type: "GET",
		url: "/src/ajax/user.php",
		data: "gtoken="+ active_group + "&action=ranking-grupo",
		success: (data) => {

			$("#ranking-content").html("");
      
			if ( data.ranking.length > 0 ) {
				
				var lista_ranking = '<ul class="row container spc-5">';
				var header = "";
				
				$.each(data.ranking, (key, player) => {
					
					if ( (tipo == "jogador" || tipo == "jogador/moderador") && (player.utoken == utoken) ) {
						
						let next_level = player.nivel + 1;
						let next_points = next_level * 1000;
						let xp_bar = player.pontos * 100 / next_points;
						
						header = '<div class="row container valign-wrapper spc-5"> \
												<div class="col s3 center valign-wrapper"><img src="assets/images/icons/icon-'+ player.icone +'.png" class="avatar"></div> \
												<div class="col s9"> \
													<div class="row"> \
														<div class="col s12 bold">'+ player.nome +'</div> \
														<div class="col s12"> \
															<div class="row fs-7"> \
																<div class="col s3 left-align">Nível '+ player.nivel +'</div> \
																<div class="col s6 center-align">'+ player.pontos +' / '+ next_points +' pts</div> \
																<div class="col s3 right-align">Nível '+ next_level +'</div> \
															</div> \
														</div> \
														<div class="col s12"> \
															<div id="xp-container"><div id="xp-bar" style="width: '+ xp_bar +'%"></div></div> \
														</div> \
													</div> \
												</div> \
											</div> \
											<div class="divider container spc-5"></div>';
						
					}
					
					let posicao = key + 1
					
					lista_ranking += '<li class="col s12 border-bottom spc-8"> \
															<div class="row valign-wrapper"> \
																<div class="col s1">'+ posicao +'º</div> \
																<div class="col s2 center-align"> \
																	<img src="assets/images/icons/icon-'+ player.icone +'.png" class="avatar small"> \
																</div> \
																<div class="col s9"> \
																	<div class="row"> \
																		<div class="col s12">'+ player.nome +'</div> \
																		<div class="col s6 ranking-text valign-wrapper"><i class="material-icons ranking-icon">whatshot</i>&nbsp;Nível '+ player.nivel +'</div> \
																		<div class="col s6 ranking-text valign-wrapper"><i class="material-icons ranking-icon">star</i>&nbsp;'+ player.pontos +' pontos</div> \
																	</div> \
																</div> \
															</div> \
															<div class="spc-5"></div> \
														</li>';
					
				});
				
				lista_ranking += '</ul>';
				
				$("#ranking-content").append(header);
				$("#ranking-content").append(lista_ranking);
				
			} else
				$("#ranking-content").html('<p class="center" style="margin-top: 40%">No momento não há nenhum jogador neste grupo!</p>');
			
			$("#ranking-loading").hide();
			$("#ranking-content").fadeIn();

		},
		error: () => {
			toast("Verifique sua conexão com a internet!");
			loading("close");
		}
	});
	
}

function getNotificacoes() {
	
}

// Recebe as informações do usuário e passa para o form de editar perfil
function getUsuarioInfo() {
	
	$.ajax({
		type: "GET",
		url: "/src/ajax/user.php",
		data: "action=usuario-info",
		success: (data) => {
			
			$("#nome_perfil").val(data.info.nome);
			$("#email_perfil").val(data.info.email);
			$("#senha_atual_perfil").val("");
			$("#nova_senha_perfil").val("");
			$("#confirmar_senha_perfil").val("");
			M.updateTextFields();
			
			$("#icone_perfil").find("option[value='"+ data.info.icone +"']").prop("selected", true);
			$("#icone_perfil").formSelect();

		},
		error: () => {
			toast("Verifique sua conexão com a internet!");
			loading("stop");
		}
	});
	
}