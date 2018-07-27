<?php 
// Evita acesso direto a este arquivo
if ( ! defined('ABSPATH')) exit; 
?>

<section> <!--class="sections"--> 
		<div class="container">
			
			<h2 class="text-center  text-uppercase">
				<strong>Textos</strong>
			</h2>
			<hr class="divider">
			<div class="row">                
				<!-- inicio textos -->
				<div class="col-lg-12 col-md-12 mx-auto">                   
					
					<?php 
					$modelo->form_confirma = $modelo->apaga_texto();
					$modelo->posts_por_pagina = 4;
					$lista = $modelo->listar_textos(); 
					
					$current = $this->parametros[1] ? $this->parametros[1] : 1;
					
					echo $modelo->form_confirma;
					?>
					
					<?php foreach( $lista as $texto ):?>
					<div class="post-preview">
						<a href="texto1.html">
							<h4 class="post-title">
								<?php echo $texto['titulo']?>
							</h4>
						</a>
						<?php if (isset($_SESSION['userdata']) || true){
						if ( /*$_SESSION['userdata']['Admin'] == 'S'|| */true ) {
							$texto_uri = HOME_URI . '/texto/index/';
							$edit_uri = $texto_uri . 'edit/';
							$delete_uri = $texto_uri . 'del/';
							?>
						<a href="<?php echo $edit_uri . $texto['id'] .'/'.$current?>">
						Editar
					</a> 
					
					<a href="<?php echo $delete_uri . $texto['id'] . '/'.$current?>">
						Apagar
					</a>
					<?php
							}
						}
						?>
						<h5 class="post-subtitle">
							<?php echo custom_echo($texto['texto'],200)?>
						</h5>
						
						<p class="post-meta">Postado por
							<a href="#"><?php echo $texto['nome']?></a>
							<?php echo date_format(new DateTime($texto['data']),'d/m/Y')?></p>
					</div>
						<hr class="border-hr">
						<?php endforeach; ?>
								</div>
								<!-- fim textos -->				
								
							</div>
							<?php $modelo->paginacao();?>
							
						</div>
					</section>							
				