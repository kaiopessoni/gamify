var mtoken_to_edit;

$(document).ready(() => {

  $("#btn-criar-missao").click(() => {
    criarMissao();
  });

  $("#btn-editar-missao").click(() => {
    editarMissao(mtoken_to_edit);
  });

});

// Recebe os dados da missão para edição
$(document).on("click", ".edit-mission", function () {
  mtoken_to_edit = $(this).data("mtoken");
  getMissaoInfo(mtoken_to_edit);
  showPage("#editar-missao");
});

function criarMissao() {
  
  loading("start");

  $.ajax({
		type: "GET",
		url: "/src/ajax/mentor.php",
		data: $("#form-criar-missao").serialize() + "&gtoken="+ active_group +"&action=criar-missao",
		success: (data) => {

      if ( data.code == "mission_created" ) {
        sync();
        hidePage("#criar-missao");
        
        // Limpa o formulário
        $("#form-criar-missao").trigger("reset");
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

function getMissaoInfo(mtoken) {
  
  $.ajax({
    type: "GET",
		url: "/src/ajax/mentor.php",
		data: "gtoken="+ active_group +"&mtoken="+ mtoken +"&action=missao-info",
		success: (data) => {

      $("#nome_missao2").val(data.missao.nome);
      $("#descricao_missao2").val(data.missao.descricao);
      $("#prazo_missao2").val(data.missao.prazo);

      M.updateTextFields();
      M.textareaAutoResize($('#descricao_missao2'));
			
			$("#recompensa_missao2").find("option[value='"+ data.missao.recompensa +"']").prop("selected", true);
      $("#recompensa_missao2").formSelect();
      
		},
		error: () => {
			toast("Verifique sua conexão com a internet!");
			loading("close");
		}
	});
}

function editarMissao(mtoken) {
  loading("start");

  $.ajax({
		type: "GET",
		url: "/src/ajax/mentor.php",
		data: $("#form-editar-missao").serialize() + "&mtoken="+ mtoken + "&gtoken="+ active_group +"&action=editar-missao",
		success: (data) => {

      if ( data.code == "mission_updated" ) {
        sync();
        hidePage("#editar-missao");
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