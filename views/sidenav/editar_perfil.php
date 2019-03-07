<section id="editar-perfil" class="new-page">
	
	<div class="row nav z-depth-1 teal valign-wrapper">
		<div class="col s12 center">
			<a class="back valign-wrapper left" onclick="hidePage('#editar-perfil')"><i class="material-icons white-text small">chevron_left</i></a>
			<span class="title">Editar Perfil</span>
		</div>
	</div>

	<div class="container content">
		<form id="form-editar-perfil" class="col s12">

			<div class="row">
				<div class="input-field col s12">
					<input type="text" id="nome_perfil" name="nome" class="charCounter" data-length="25" maxlength="25">
					<label for="nome_perfil">Nome</label>
				</div>
			</div>

			<div class="row icones-dropdown">
				<div class="input-field col s12">
					<select class="icons" id="icone_perfil" name="icone">
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
					<label>Ícone do Perfil</label>
				</div>
			</div>
			
			<div class="row">
				<div class="input-field col s12">
					<input type="password" id="senha_atual_perfil" name="senha_atual" maxlength="25">
					<label for="senha_atual_perfil">Senha Atual (opcional)</label>
				</div>
			</div>
			
			<div class="row">
				<div class="input-field col s12">
					<input type="password" id="nova_senha_perfil" name="nova_senha" maxlength="25">
					<label for="nova_senha_perfil">Nova Senha (opcional)</label>
				</div>
			</div>
			
			<div class="row">
				<div class="input-field col s12">
					<input type="password" id="confirmar_senha_perfil" name="confirmar_senha" maxlength="25">
					<label for="confirmar_senha_perfil">Confirmar Senha (opcional)</label>
				</div>
			</div>
			
			<div class="row">
				<div class="input-field col s12">
					<input type="text" id="email_perfil" name="email">
					<label for="email_perfil">E-mail</label>
				</div>
			</div>
			
			<div class="row">
				<div class="input-field col s12 center">
					<button id="btn-editar-perfil" class="btn teal waves-effect waves-light" type="button">Atualizar Perfil</button>
				</div>
			</div>
			

		</form>
	</div>
	
</section>