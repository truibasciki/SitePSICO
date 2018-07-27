<?php
class TextoModel extends MainModel
{
	/**
	 * Construtor para essa classe
	 *
	 * Configura o DB, o controlador, os parâmetros e dados do usuário.
	 *
	 * @since 0.1
	 * @access public
	 * @param object $db Objeto da nossa conexão PDO
	 * @param object $controller Objeto do controlador
	 */
	public function __construct( $db = false, $controller = null ) {
		// Configura o DB (PDO)
		$this->db = $db;
		
		// Configura o controlador
		$this->controller = $controller;

		// Configura os parâmetros
		$this->parametros = $this->controller->parametros;

		// Configura os dados do usuário
		$this->userdata = $this->controller->userdata;
		
	}
	
	// Crie seus próprios métodos daqui em diante
	
	public function listar_textos () {	
		// Configura as variáveis que vamos utilizar
		$id = $where = $query_limit = null;
		
		// Verifica se um parâmetro foi enviado para carregar uma notícia
		if ( is_numeric( chk_array( $this->parametros, 0 ) ) ) {
			
			// Configura o ID para enviar para a consulta
			$id = array ( chk_array( $this->parametros, 0 ) );
			
			// Configura a cláusula where da consulta
			$where = " WHERE texto.id = ? ";
		}
		
		// Configura a página a ser exibida
		$pagina = ! empty( $this->parametros[1] ) ? $this->parametros[1] : 1;
		
		// A paginação inicia do 0
		$pagina--;
		
		// Configura o número de posts por página
		$posts_por_pagina = $this->posts_por_pagina;
		
		// O offset dos posts da consulta
		$offset = $pagina * $posts_por_pagina;
		
		/* 
		Esta propriedade foi configurada no noticias-adm-model.php para
		prevenir limite ou paginação na administração.
		*/
		if ( empty ( $this->sem_limite ) ) {
		
			// Configura o limite da consulta
			$query_limit = " LIMIT $offset,$posts_por_pagina ";
		
		}
		
		// Faz a consulta
		$query = $this->db->query(
			'SELECT texto.*, usuario.nome FROM texto join usuario on texto.idUsuario = usuario.id ' . $where . ' ORDER BY texto.id DESC' . $query_limit,
			$id
		);
		
		// Retorna
		return $query->fetchAll();
	} // listar_noticias
	
	/**
	 * Obtém o texto e atualiza os dados se algo for postado
	 *
	 * Obtém apenas um texto da base de dados para preencher o formulário de
	 * edição.
	 * Configura a propriedade $this->form_data.
	 *
	 * @since 0.1
	 * @access public
	 */
	public function obtem_texto () {
		
		// Verifica se o primeiro parâmetro é "edit"
		if ( chk_array( $this->parametros, 0 ) != 'edit' ) {
			return;
		}
		
		// Verifica se o segundo parâmetro é um número
		if ( ! is_numeric( chk_array( $this->parametros, 1 ) ) ) {
			return;
		}
		
		// Configura o ID da notícia
		$texto_id = chk_array( $this->parametros, 1 );
		
		/* 
		Verifica se algo foi postado e se está vindo do form que tem o campo
		insere_noticia.
		
		Se verdadeiro, atualiza os dados conforme a requisição.
		*/
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['insere_texto'] ) ) {
		
			// Remove o campo insere_notica para não gerar problema com o PDO
			unset($_POST['insere_texto']);
			
			// Verifica se a data foi enviada
			$data = chk_array( $_POST, 'texto_data' );
			
			/*
			Inverte a data para os formatos dd-mm-aaaa hh:mm:ss
			ou aaaa-mm-dd hh:mm:ss
			*/
			$nova_data = $this->inverte_data( $data );
			
			// Adiciona a data no $_POST		
			$_POST['texto_data'] = $nova_data;
			
			// Tenta enviar a imagem
			//$imagem = $this->upload_imagem();
			
			// Verifica se a imagem foi enviada
			//if ( $imagem ) {
				// Adiciona a imagem no $_POST
				//$_POST['noticia_imagem'] = $imagem;
			//}
			
			// Atualiza os dados
			$query = $this->db->update('texto', 'id', $texto_id, $_POST);
			
			// Verifica a consulta
			if ( $query ) {
				// Retorna uma mensagem
				$this->form_msg = '<p class="success">Texto atualizado com sucesso!</p>';
			}
			
		}
		
		// Faz a consulta para obter o valor
		$query = $this->db->query(
			'SELECT * FROM texto WHERE noticia_id = ? LIMIT 1',
			array( $texto_id )
		);
		
		// Obtém os dados
		$fetch_data = $query->fetch();
		
		// Se os dados estiverem nulos, não faz nada
		if ( empty( $fetch_data ) ) {
			return;
		}
		
		// Configura os dados do formulário
		$this->form_data = $fetch_data;
		
	} // obtem_texto
	
	/**
	 * Insere Textos
	 *
	 * @since 0.1
	 * @access public
	 */
	public function insere_texto() {
	
		/* 
		Verifica se algo foi postado e se está vindo do form que tem o campo
		insere_texto.
		*/
		if ( 'POST' != $_SERVER['REQUEST_METHOD'] || empty( $_POST['insere_texto'] ) ) {
			return;
		}
		
		/*
		Para evitar conflitos apenas inserimos valores se o parâmetro edit
		não estiver configurado.
		*/
		if ( chk_array( $this->parametros, 0 ) == 'edit' ) {
			return;
		}
		
		// Só pra garantir que não estamos atualizando nada
		if ( is_numeric( chk_array( $this->parametros, 1 ) ) ) {
			return;
		}
			
		// Tenta enviar a imagem
		//$imagem = $this->upload_imagem();
		
		// Verifica se a imagem foi enviada
		//if ( ! $imagem ) {
			//return;		
		//}
		
		// Remove o campo insere_notica para não gerar problema com o PDO
		unset($_POST['insere_texto']);
		
		// Insere a imagem em $_POST
		//$_POST['noticia_imagem'] = $imagem;
		
		// Configura a data
		$data = chk_array( $_POST, 'texto_data' );
		$nova_data = $this->inverte_data( $data );
					
		// Adiciona a data no POST
		$_POST['texto_data'] = $nova_data;
		
		// Insere os dados na base de dados
		$query = $this->db->insert( 'texto', $_POST );
		
		// Verifica a consulta
		if ( $query ) {
		
			// Retorna uma mensagem
			$this->form_msg = '<p class="success">Texto atualizado com sucesso!</p>';
			return;
			
		} 
		
		// :(
		$this->form_msg = '<p class="error">Erro ao enviar dados!</p>';

	} // insere_texto
	
	/**
	 * Apaga o Texto
	 *
	 * @since 0.1
	 * @access public
	 */
	public function apaga_texto () {
		
		// O parâmetro del deverá ser enviado
		if ( chk_array( $this->parametros, 0 ) != 'del' ) {
			return;
		}
		
		// O segundo parâmetro deverá ser um ID numérico
		if ( ! is_numeric( chk_array( $this->parametros, 1 ) ) ) {
			return;
		}
		$current = $this->parametros[2] ? $this->parametros[2] : 1;
		
		// Para excluir, o terceiro parâmetro deverá ser "confirma"
		if ( chk_array( $this->parametros, 3 ) != 'confirma' ) {
		
			// Configura uma mensagem de confirmação para o usuário
			$mensagem  = '<p class="alert">Tem certeza que deseja apagar a notícia?</p>';
			$mensagem .= '<p><a href="' . $_SERVER['REQUEST_URI'] . '/confirma/">Sim</a> | ';
			$mensagem .= '<a href="' . HOME_URI . '/texto/index/page/'.$current.'">Não</a></p>';
			
			// Retorna a mensagem e não excluir
			return $mensagem;
		}
		
		// Configura o ID da notícia
		$texto_id = (int)chk_array( $this->parametros, 1 );
		
		// Executa a consulta
		$query = $this->db->delete( 'texto', 'id', $texto_id );
		
		// Redireciona para a página de administração de notícias
		echo '<meta http-equiv="Refresh" content="0; url=' . HOME_URI . '/texto/index/page/'.$current.'">';
		echo '<script type="text/javascript">window.location.href = "' . HOME_URI . '/texto/index/page/'.$current.'";</script>';
		
	} // apaga_texto
		
		
		


	public function paginacao () {
	
		if ( chk_array( $this->parametros, 0 ) == 'del' ) {
			return;
		}
	
		/* 
		Verifica se o primeiro parâmetro não é um número. Se for é um single
		e não precisa de paginação.
		*/
		if ( is_numeric( chk_array( $this->parametros, 0) ) ) {	
			return;
		}
		
		
		// Obtém o número total de notícias da base de dados
		$query = $this->db->query(
			'SELECT COUNT(*) as total FROM texto '
		);
		$total = $query->fetch();
		$total = $total['total'];
		
		// Configura o caminho para a paginação
		$caminho_texto = HOME_URI . '/texto/index/page/';
		
		// Itens por página
		$posts_per_page = $this->posts_por_pagina;
		
		// Obtém a última página possível
		$last = ceil($total/$posts_per_page);
		
		// Configura a primeira página
		$first = 1;
		
		// Configura os offsets
		$offset1 = 2;
		$offset2 = 5;
		
		// Página atual
		$current = $this->parametros[1] ? $this->parametros[1] : 1;
		
		// Exibe a primeira página e reticências no início
		echo "
			<nav aria-label='Page navigation example'>
			<ul class='pagination'>";
			
		if($current == 1){
			echo "<li class='page-item disabled'><a class='page-link' href='#' tabindex='-1'>Anterior</a></li> ";			
		}else{
			echo "<li class='page-item'><a class='page-link' href='$caminho_texto". ($current-1) ."'>Anterior</a></li>";
		}
		if ( $current > 4 ) {
			
			echo "<li class='page-item'><a class='page-link' href='$caminho_texto$first'>$first</a></li><li class='page-item disabled'><a class='page-link' href='#' tabindex='-1'>...</a></li>";
		}
		
		// O primeiro loop toma conta da parte esquerda dos números
		for ( $i = ( $current - $offset1 ); $i < $current; $i++ ) {
			if ( $i > 0 ) {
				echo "<li class='page-item'><a class='page-link' href='$caminho_texto$i'>$i</a></li>";
				
				// Diminiu o offset do segundo loop
				$offset2--;
			}
		}
		
		// O segundo loop toma conta da parte direita dos números
		// Obs.: A primeira expressão realmente não é necessária
		for ( ; $i < $current + $offset2; $i++ ) {
			if($i == $current){
				echo "<li class='page-item active'><a class='page-link' href='$caminho_texto$i'>$i</a></li>";
			}
			else if ( $i <= $last ) {
				echo "<li class='page-item'><a class='page-link' href='$caminho_texto$i'>$i</a></li>";
			}
		}
		
		// Exibe reticências e a última página no final
		if ( $current <= ( $last - $offset1 ) ) {
			echo " <li class='page-item disabled'><a class='page-link' href='#' tabindex='-1'>...</a></li>
			<li class='page-item'><a class='page-link' href='$caminho_texto$last'>$last</a></li>";
		}
		if($current == $last){
			echo "<li class='page-item disabled'><a class='page-link' href='#' tabindex='-1'>Próxima</a></li>";			
		}else{
			echo "<li class='page-item'><a class='page-link' href='$caminho_texto". ($current+1) ."'>Próxima</a></li>";
		}
		
		echo "
			</ul>
			</nav>";
	} // paginacao

}
?>