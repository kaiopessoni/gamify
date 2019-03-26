<section id="editar-missao" class="new-page">
	
	<div class="row nav z-depth-1 teal valign-wrapper">
		<div class="col s12 center">
			<a class="back valign-wrapper left" onclick="hidePage('#editar-missao')"><i class="material-icons white-text small">chevron_left</i></a>
			<span class="title">Editar Missão</span>
		</div>
	</div>

	<div class="container content">
		<form id="form-editar-missao" class="col s12">

			<div class="row">
				<div class="input-field col s12">
					<input type="text" id="nome_missao2" name="nome" class="charCounters" data-length="30" maxlength="30">
					<label for="nome_missao2">Nome da Missão</label>
				</div>
			</div>
			
			<div class="row">
        <div class="input-field col s12">
          <textarea id="descricao_missao2" name="descricao" class="materialize-textarea"></textarea>
          <label for="descricao_missao2">Descrição da Missão</label>
        </div>
      </div>
      
			<div class="row mb">
        <div class="input-field col s12">
          <input type="text" id="prazo_missao2" name="prazo" class="datepicker">
          <label for="prazo_missao2">Prazo da Missão</label>
        </div>
      </div>

			<div class="row">
				<div class="input-field col s12">
					<select id="recompensa_missao2" name="recompensa">
						<option value="" disabled selected>Escolha a recompensa</option>
						<option value="250">250 pontos</option>
						<option value="500">500 pontos</option>
						<option value="1000">1000 pontos</option>
					</select>
					<label>Recompensa</label>
				</div>
			</div>
			
			<div class="row">
				<div class="input-field col s12 center">
					<button id="btn-editar-missao" class="btn teal waves-effect waves-light" type="button">Atualizar Missão</button>
				</div>
			</div>

		</form>
	</div>
	
</section>