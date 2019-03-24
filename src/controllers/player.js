
function completarMissao(data) {

  loading("start");

  $.ajax({
		type: "GET",
		url: "/src/ajax/player.php",
		data: "mtoken="+ data.mtoken +"&gtoken="+ active_group +"&action=completar-missao",
		success: (data) => {
     
      if ( data.code == "mission_completed" )
        getGrupos();

			loading("close");
			toast(data.status, data.message);
     
		},
		error: () => {
			toast("Verifique sua conexão com a internet!");
			loading("close");
		}
  });
  
}