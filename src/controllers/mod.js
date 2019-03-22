function change_user_type(data) {

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

function excluir_grupo(data) {

  loading("open");
  let gtoken = data.gtoken;
		
	$.ajax({
		type: "GET",
		url: "/src/ajax/mod.php",
		data: "&gtoken="+ data.gtoken +"&action=excluir-grupo",
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