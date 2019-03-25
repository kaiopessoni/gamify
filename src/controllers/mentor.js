$(document).ready(() => {

  $("#btn-criar-missao").click(() => {
    criarMissao();
  });

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