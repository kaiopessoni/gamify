
var gtoken_to_edit;

$(document).ready(() => {

  $("#btn-editar-grupo").click(() => {
    editarGrupo(gtoken_to_edit);
  });

});

// Recebe os dados do grupo para edição
$(document).on("click", ".edit-group", function () {
  gtoken_to_edit = $(this).data("gtoken");
  getGrupoInfo(gtoken_to_edit);
  showPage("#editar-grupo");
});

function changeUserType(data) {

  loading("open");
  let gtoken = data.gtoken;
		
	$.ajax({
		type: "GET",
		url: "/src/ajax/mod.php",
		data: "type="+ data.type +"&utoken="+ data.utoken +"&gtoken="+ data.gtoken +"&action=change-user-type",
		success: (data) => {

      toast(data.status, data.message);

      if ( data.code == "type_updated" ) {
        getGrupos();
        setTimeout(() => {
          showPage("#info-grupo-" + gtoken);
        }, 1000);
      }

      loading("close");

		},
		error: () => {
			toast("Verifique sua conexão com a internet!");
			loading("close");
		}
	});

}

function excluirGrupo(data) {

  loading("open");
  let gtoken = data.gtoken;
		
	$.ajax({
		type: "GET",
		url: "/src/ajax/mod.php",
		data: "gtoken="+ data.gtoken +"&action=excluir-grupo",
		success: (data) => {

      loading("close");
      toast(data.status, data.message);

      if ( data.code == "group_deleted" ) {
        active_group = null;
        getGrupos();
      }


		},
		error: () => {
			toast("Verifique sua conexão com a internet!");
			loading("close");
		}
	});

}

function getGrupoInfo(gtoken) {
  
  $.ajax({
    type: "GET",
		url: "/src/ajax/mod.php",
		data: "gtoken="+ gtoken +"&action=grupo-info",
		success: (data) => {
      
      $("#nome_grupo").val(data.grupo.nome);
			M.updateTextFields();
			
			$("#icone_grupo").find("option[value='"+ data.grupo.icone +"']").prop("selected", true);
			$("#icone_grupo").formSelect();
		},
		error: () => {
			toast("Verifique sua conexão com a internet!");
			loading("close");
		}
	});
}

function editarGrupo(gtoken) {

  loading("start");

  $.ajax({
		type: "GET",
		url: "/src/ajax/mod.php",
		data: $("#form-editar-grupo").serialize() + "&gtoken="+ gtoken +"&action=editar-grupo",
		success: (data) => {
     
      if ( data.code == "group_updated" ) {
        getGrupos();
				hidePage("#editar-grupo")
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