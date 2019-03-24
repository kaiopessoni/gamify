<section id="editar-grupo" class="new-page">
	
	<style>.icones-dropdown .dropdown-content {max-height:  350px;}</style>

	<div class="row nav z-depth-1 teal valign-wrapper">
		<div class="col s12 center">
			<a class="back valign-wrapper left" onclick="hidePage('#editar-grupo')"><i class="material-icons white-text small">chevron_left</i></a>
			<span class="title">Editar Grupo</span>
		</div>
	</div>

	<div class="container content">
		<form id="form-editar-grupo" class="col s12">

			<div class="row">
				<div class="input-field col s12">
					<input type="text" id="nome_grupo" name="nome" class="charCounter" data-length="20" maxlength="20">
					<label for="nome_grupo">Nome do Grupo</label>
				</div>
			</div>

			<div class="row icones-dropdown">
				<div class="input-field col s12">
					<select id="icone_grupo" name="icone" class="icons">
						<option value="" disabled selected>Escolha um ícone</option>
						<option value="1" data-icon="assets/images/icons/icon-1.png">Ícone 1</option>
						<option value="2" data-icon="assets/images/icons/icon-2.png">Ícone 2</option>
						<option value="3" data-icon="assets/images/icons/icon-3.png">Ícone 3</option>
						<option value="4" data-icon="assets/images/icons/icon-4.png">Ícone 4</option>
						<option value="5" data-icon="assets/images/icons/icon-5.png">Ícone 5</option>
						<option value="6" data-icon="assets/images/icons/icon-6.png">Ícone 6</option>
						<option value="7" data-icon="assets/images/icons/icon-7.png">Ícone 7</option>
						<option value="8" data-icon="assets/images/icons/icon-8.png">Ícone 8</option>
						<option value="9" data-icon="assets/images/icons/icon-9.png">Ícone 9</option>
						<option value="10" data-icon="assets/images/icons/icon-10.png">Ícone 10</option>
						<option value="11" data-icon="assets/images/icons/icon-11.png">Ícone 11</option>
						<option value="12" data-icon="assets/images/icons/icon-12.png">Ícone 12</option>
						<option value="13" data-icon="assets/images/icons/icon-13.png">Ícone 13</option>
					</select>
					<label>Ícone do grupo</label>
				</div>
			</div>
			
			<div class="row">
				<div class="input-field col s12 center">
					<button id="btn-editar-grupo" class="btn teal waves-effect waves-light" type="button">Atualizar Grupo</button>
				</div>
			</div>
			

		</form>
	</div>
	
</section>