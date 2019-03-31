<!-- Modal de Confirmação -->
<div id="modal-confirm" class="modal middle">
	<div class="modal-content">
		<p id="confirm-question" class="fs-11">Tem certeza que deseja realizar esta ação?</p>
		<div class="row">
			<div class="col s6 center"><a id="confirm-yes" class="btn-flat waves-effect modal-close">Sim</a></div>
			<div class="col s6 center"><a class="btn-flat waves-effect modal-close" onclick="change_confirm_question()">Não</a></div>
		</div>
	</div>
</div>

<!-- Modal de Loading -->
<div id="modal-loading" class="modal middle">
	<div class="modal-content">
	
		<div class="row valign-wrapper spc-2">
			<div class="col s4">
				<div class="center">
					<div class="preloader-wrapper small active">
						<div class="spinner-layer spinner-teal-only">
							<div class="circle-clipper left">
								<div class="circle"></div>
							</div><div class="gap-patch">
								<div class="circle"></div>
							</div><div class="circle-clipper right">
								<div class="circle"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col s8 fs-12">Carregando...</div>
		</div>
		
	</div>
</div>

<!-- Modal de info do código do grupo -->
<div id="modal-codigo-grupo" class="modal middle">
	<div class="modal-content">
		<h6 class="bold valign-wrapper"><i class="material-icons grey-text text-darken-3">info</i>&nbsp;Código do Grupo</h6>
		<p>Este é o <strong>código único do grupo</strong>, através dele outras pessoas conseguem participar deste grupo caso os moderadores aceitem.</p>
	</div>
</div>

<!-- Modal de Confirmar Missão -->
<div id="modal-confirmar-missao" class="modal middle">
	<div class="modal-content">
		<p class="fs-11 center">Arraste e escolha a recompensa!</p>
		<div class="row valign-wrapper">
      <div class="col s2 fs-8 center">50</div>
			<div class="col s8 center">
        <p class="range-field"><input id="range-recompensa" type="range" min="50" step="50" value="50" /></p>
      </div>
      <div id="label-max-recompensa" class="col s2 fs-8 center"></div>
		</div>
    <div class="row">
      <div class="col s12 center"><a id="btn-confirmar-missao" class="btn-flat waves-effect modal-close">Confirmar</a></div>
    </div>
	</div>
</div>