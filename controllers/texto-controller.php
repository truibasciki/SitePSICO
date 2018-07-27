<?php
class TextoController extends MainController
{
	// URL: dominio.com/texto/
	public function index() {
	
		// Título da página
	$this->title = 'Texto';
		
	// Parametros da função
	$parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
	
	$modelo = $this->load_model('texto/texto-model');
		
	/** Carrega os arquivos do view **/
		
	// /views/_includes/header.php
        require ABSPATH . '/views/_includes/header.php';		
		
	// /views/home/login-view.php
        require ABSPATH . '/views/texto/texto-view.php';
		
	// /views/_includes/footer.php
        require ABSPATH . '/views/_includes/footer.php';
		
	}
}
	?>