<section id="criar-missao" class="new-page">
	
	<div class="row nav z-depth-1 teal valign-wrapper">
		<div class="col s12 center">
			<a class="back valign-wrapper left" onclick="hidePage('#criar-missao')"><i class="material-icons white-text small">chevron_left</i></a>
			<span class="title">Criar Missão</span>
		</div>
	</div>

	<div class="container content">
		<form id="form-criar-missao" class="col s12">

			<div class="row">
				<div class="input-field col s12">
					<input type="text" id="nome_missao" name="nome_missao" class="charCounters" data-length="30" maxlength="30">
					<label for="nome_missao">Nome da Missão</label>
				</div>
			</div>
			
			<div class="row">
        <div class="input-field col s12">
          <textarea id="descricao_missao" name="descricao_missao" class="materialize-textarea" maxlength="250"></textarea>
          <label for="descricao_missao">Descrição da Missão</label>
        </div>
      </div>
      
			<div class="row mb">
        <div class="input-field col s12">
           <input type="text" id="prazo_missao" name="prazo_missao" class="datepicker">
          <label for="prazo_missao">Prazo da Missão</label>
        </div>
      </div>

			<div class="row">
				<div class="input-field col s12">
					<select name="recompensa_missao">
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
					<button id="btn-criar-missao" class="btn teal waves-effect waves-light" type="button">Criar Missão</button>
				</div>
			</div>

		</form>
	</div>
	
</section>