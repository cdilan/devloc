<?php
/**
 * PageLines Customize functions.php.
 *
 * @author Simon Prosser
 */
/*
// ---- ADDING NEW TEMPLATES ---- //

	Want another page template for drag and drop? Easy :)
	1. Add File called page.[page-id].php to this folder.
	2. Add Template Name: Your Page Name to that file ( see page.base.php for an example. )
	3. Thats it! We do the rest for you!
	
// ---- ADDING NEW SECTIONS ---- //

	Adding new sections is really easy in 2.0
	1. Copy your section.[sectionname].php file into the sections folder
	2. It will be auto loaded for you.
	3. You can now enable/disable the section in the extensions menu.

// FILTERS EXAMPLE ---------//

	// The following filter will add the font  Ubuntu into the font array $thefoundry.
	// This makes the font available to the framework and the user via the admin panel.
*/
add_filter ( 'pagelines_foundry', 'my_google_font' );

function my_google_font( $thefoundry ) {
	$myfont = array( 'Ubuntu' => array(
			'name' => 'Ubuntu',
			'family' => '"Ubuntu", arial, serif',
			'web_safe' => true,
			'google' => true,
			'monospace' => false
			)
		);
	return array_merge( $thefoundry, $myfont );
}

// ------ lanoba ------ //

function lb_shortcode () {
    if ( is_user_logged_in() ) {
    	global $current_user;
    	$id = $current_user->id;
    	$display_name = $current_user->display_name;
    	$saida = '	<div id="lanoba" class="logado">
	    				<div class="profile_user">
							<div class="profile_avatar">
								'.get_avatar($id,64).'
							</div>    				
	    					<div class="profile_display_name">
	    						'.$display_name.'
	    					</div>
	    				</div>
	    				<div class="profile_logout">
							<a href="'.wp_logout_url().'" title="Logout">Logout</a>
		    			</div>
	    			</div>';
    	return ($saida);
    }else{
    	$iframe = (lb_LoginWidget('',0,0,0));
    	$saida = '<div id="lanoba" class="deslogado" >'.$iframe.'</div>';
    	return ($saida);
    }
}

add_shortcode( 'lanoba', 'lb_shortcode' );
add_filter('widget_text', 'do_shortcode'); //abilita shortcodes nos widgets

/*
// ====================================================
// = Atualisa usermeta do jogador via formidable  =
// ====================================================
*/

add_action('frm_after_create_entry','altera_usermeta_via_form_basico', 20,2);
add_action('frm_after_update_entry','altera_usermeta_via_form_basico', 20,2);

function altera_usermeta_via_form_basico ($entry_id, $form_id) {

	if ($form_id == 6) {
		update_user_meta ($_POST['item_meta'][84], 'first_name', $_POST['item_meta'][85]);
		update_user_meta ($_POST['item_meta'][84], 'last_name', $_POST['item_meta'][86]);
		update_user_meta ($_POST['item_meta'][84], 'nickname', $_POST['item_meta'][87]);
	}
}



/*
// ===============================================================================
// = altera user de assinante pra jogador via formidable Termo de Uso e Conduta  =
// ===============================================================================
*/

add_action('frm_after_create_entry', 'altera_assinante_para_jogador', 20, 2);
add_action('frm_after_update_entry', 'altera_assinante_para_jogador', 20, 2);
function altera_assinante_para_jogador($entry_id, $form_id){
  if($form_id == 22){ //id do formulario-Termo Uso e Conduta
		$rafa = get_userdata($_POST['item_meta'][230]);
		$rafa -> set_role('jogador');
		update_user_meta ($_POST['item_meta'][230], 'show_admin_bar_front' , 'false');
  }
}

/*
// ====================================================
// = Atualisa usermeta do jogador via formidable  =
// ====================================================
*/

function mostra_avatar() {
	if (is_user_logged_in()) {
		global $current_user;
		$id = $current_user->id;
		$saida = '	<div class="profile_avatar">
						'.get_avatar($id,64).'
					</div>';
		return ($saida);
	}
}
add_shortcode( 'mostra_avatar', 'mostra_avatar' );

/*
// ====================================================
// = botões mais utilizados usando shortcode          =
// ====================================================
*/

function botao_facil ( $atts ) {
	extract( shortcode_atts( array(
		'text' => 'voltar',
		'class' => '',
		'link' => '#',
		'width' => '40px',
		'text_transform' => 'none',
		'target' => '_self',
		'display' => 'inline',
		), $atts ) );


	if ($class=="") $class=$text;
	if ($text=="" and $class=="" and $link==""){
		return(false);
	}

	switch ($text) {
		case 'voltar':
			$hecho = '	<div class="botao-base" style="	min-width: 57px;
														text-transform: uppercase;
														display: block;">
							<a href="javascript:history.back()">'
							.$text.'
							</a>
						</div>';
			break;
		
		default:
			$hecho ='	<div class="botao-base '.$class.'" style="	width:'.$width.';
																	text-transform: '.$text_transform.';
																	display:'.$display.';" >
							<a href="'.$link.'" target="'.$target.'">'.$text.'</a>
							<style>a:hover{width:'.$width.';padding: 0px 9px 2px;}</style>
						</div>';
			break;
	}
	return($hecho);
}
add_shortcode( 'botao', 'botao_facil');