var activePage = "main-page";

$(document).ready(() => {
	
	// Initializations
	// ==========================================================================
	$('.sidenav').sidenav();
	$('.tabs').tabs();
	$('.dropdown-trigger').dropdown();
	$(".charCounter").characterCounter();
	$('.fixed-action-btn').floatingActionButton();
	$('select').formSelect();
	$('.modal').modal();
	$('.datepicker').datepicker();
	
	// Options
	// ==========================================================================
	$('.modal.middle').modal({
		endingTop: '33%'
	});
	
	$("#modal-loading").modal({
		dismissible: false,
		endingTop: '40%'
	});
	
	$(".settings-dropdown").dropdown({
		constrainWidth: false
	});
	
	$('.fixed-action-btn').floatingActionButton({
		hoverEnabled: false
	});
	
	// Event Handlers
	// ==========================================================================
	
	// Notificações Click
	$("#btn-notifications").click(() => {
		
		if (activePage == "notificacoes")
			return;
		
		activePage = "notificacoes";
		
		$("#main-page, #nav-grupos").hide();
		$(".navbar-fixed").css("height", "10vh");
		$("#notificacoes").fadeToggle();
	});
	
	// Grupos Click
	$("#btn-grupos").click(() => {
		
		if (activePage == "main-page")
			return;
		
		activePage = "main-page";
		
		$("#notificacoes").hide();
		$("#main-page, #nav-grupos").fadeToggle();
		$(".navbar-fixed").css("height", "18vh");
	});
	
	// Descrição Click
	$(".desc").click(() => {
		$("#modal-descricao-missao").modal("open");
	});
	
	// Sidenav Item Click
	$(".sidenav li a").click(() => {
		$(".sidenav").sidenav("close");
	});
	
});


function hidePage(id) { 
	$(id).fadeToggle();
	$("#grupo-content").show();
	$("#missoes-content").show();
	$("#ranking-content").show();
	$("#notificacoes-content").show();
}

function showPage(id) {	
	
	$(id).fadeToggle();
	$("#grupo-content").hide();
	$("#missoes-content").hide();
	$("#ranking-content").hide();
	$("#notificacoes-content").hide();
}

function confirm(type, uid) {
	$("#modal-confirm").modal("open");
	$("#confirm-desc").html(confirm_types[type].descricao);
}

var confirm_types = {
	"completar_missao": {
		"descricao": "Tem certeza que completou esta missão?",
		"id": 1
	},
	"excluir_missao": {
		"descricao": "Tem certeza que deseja excluir esta missão?",
		"id": 2
	},
	"excluir_grupo": {
		"descricao": "Tem certeza que deseja excluir este grupo?",
		"id": 2
	},
	"ativar_grupo": {
		"descricao": "Ativar este grupo?",
		"id": 2
	}
};
