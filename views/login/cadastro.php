<section id="cadastro" class="new-page">
	
	<div class="row nav z-depth-1 teal valign-wrapper">
		<div class="col s12 center">
			<a class="back valign-wrapper left" onclick="hidePage('#cadastro')"><i class="material-icons white-text small">chevron_left</i></a>
			<span class="title">Cadastrar</span>
		</div>
	</div>

	<div class="container content">
		<form id="form-cadastro" class="col s12 container">
			
			<div class="row">
				<div class="input-field col s12">
					<input type="text" id="nome_cadastro" name="nome">
					<label for="nome_cadastro">Nome</label>
				</div>
			</div>
			
			<div class="row">
				<div class="input-field col s12">
					<input type="text" id="email_cadastro" name="email">
					<label for="email_cadastro">E-mail</label>
				</div>
			</div>
			
			<div class="row">
				<div class="input-field col s12">
					<input type="password" id="senha_cadastro" name="senha">
					<label for="senha_cadastro">Senha</label>
				</div>
			</div>
			
			<div class="row">
				<div class="input-field col s12">
					<input type="password" id="senha_cadastro_2" name="senha2">
					<label for="senha_cadastro_2">Confirmar Senha</label>
				</div>
			</div>
			
			<div class="row">
				<div class="input-field col s12 center">
					<button id="btn-cadastrar" class="btn teal waves-effect waves-light" type="button">Cadastrar</button>
				</div>
			</div>
			

		</form>
	</div>
	
</section>