
$(document).ready(() => {
	
	// Fazer Login
	$("#btn-fazer-login").click(() => {
		fazer_login();
	});
	
	// Cadastrar Usuário
	$("#btn-cadastrar").click(() => {
		cadastrar_usuario();
	});
	
	// Editar Perfil
	$("#btn-editar-perfil").click(() => {
		editar_perfil();
		getGrupos();
	});
	
	// Criar grupo
	$("#btn-criar-grupo").click(() => {
		criar_grupo();
	});
	
	// Entrar em grupo
	$("#btn-entrar-grupo").click(() => {
		entrar_grupo();
  });
  
	// Ativar grupo
	$(".ativar-grupo").click(() => {
		ativar_grupo(confirm_data);
	});
	
});

function fazer_login() {
	
	loading("open");
		
	$.ajax({
		type: "GET",
		url: "/src/ajax/user.php",
		data: $("#form-login").serialize() + "&action=fazer-login",
		success: (data) => {

			console.log(data.code);
			
			if ( data.code == "logged_in" )
				location.href = "/";
			else {
				loading("close");
				toast(data.status, data.message);
			}

		},
		error: () => {
			toast("Verifique sua conexão com a internet!");
			loading("close");
		}
	});
	
}

function cadastrar_usuario() {
	
	loading("open");
		
	$.ajax({
		type: "GET",
		url: "/src/ajax/user.php",
		data: $("#form-cadastro").serialize() + "&action=cadastrar-usuario",
		success: (data) => {

			if ( data.code == "user_created" )
				location.href = "/";
			else {
				loading("close");
				toast(data.status, data.message);
			}

		},
		error: () => {
			toast("Verifique sua conexão com a internet!");
			loading("close");
		}
	});
}

function editar_perfil() {
	
	loading("open");
		
	$.ajax({
		type: "GET",
		url: "/src/ajax/user.php",
		data: $("#form-editar-perfil").serialize() + "&action=editar-perfil",
		success: (data) => {

			if ( data.code == "profile_updated" ) {
        getGrupos();
				getUsuarioInfo();
				hidePage("#editar-perfil")
			}

			loading("close");
			toast(data.status, data.message);

		},
		error: () => {
			toast("Verifique sua conexão com a internet!");
			loading("close");
		}
	});
	
}

function criar_grupo() {
	
	loading("open");
		
	$.ajax({
		type: "GET",
		url: "/src/ajax/user.php",
		data: $("#form-criar-grupo").serialize() + "&action=criar-grupo",
		success: (data) => {

			if ( data.code == "group_created" ) {
				
				getGrupos();
				
				hidePage("#criar-grupo");
				
				$("#nome_criar_grupo").val("");
				M.updateTextFields();

				$("#icone_criar_grupo").find("option[value='']").prop("selected", true);
				$("#icone_criar_grupo").formSelect();
				
			}

			loading("close");
			toast(data.status, data.message);

		},
		error: () => {
			toast("Verifique sua conexão com a internet!");
			loading("close");
		}
	});
	
}

function entrar_grupo() {
	
	loading("open");
		
	$.ajax({
		type: "GET",
		url: "/src/ajax/user.php",
		data: "codigo=" + $("#codigo_entrar_grupo").val() + "&action=entrar-grupo",
		success: (data) => {

			if ( data.code == "invite_sent" ) {
				
				hidePage("#entrar-em-grupo");
				
				$("#codigo_entrar_grupo").val("");
				M.updateTextFields();
				
			}

			loading("close");
			toast(data.status, data.message);

		},
		error: () => {
			toast("Verifique sua conexão com a internet!");
			loading("close");
		}
	});
	
}

function ativar_grupo(data) {

    
  if ( data.gtoken == active_group ) {
    toast("error", "Este grupo já está ativo!");
    return;
  }

  active_group = data.gtoken;
  
  toast("success", "Grupo ativado com sucesso!");
  getGrupos();
  


}