<?php

class Landing_Page_Info
{
    public function __construct()
    {
    	include_once plugin_dir_path( __FILE__ ).'sem_keywords.php';
	    include_once plugin_dir_path( __FILE__ ).'sem_varname.php';
	    
        //Ajout menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
		
        //enregistrement form
        add_action('admin_init', array($this, 'register_settings'));
        
        //Modification du meta description 
        add_action('get_header', array($this, 'add_meta_tags'));
     
        //remplacer le contenu qui possède les variables
        add_filter('the_content', array($this, 'replace_text_sem_wps')); 
        add_filter('the_excerpt', array($this, 'replace_text_sem_wps'));
	    
        add_filter('pre_get_document_title', array($this,'change_the_title'),20);
        
        add_action( 'wp_ajax_delete_keyword_by_row_number', array('Sem_Keyword', 'delete_keyword_by_row_number') );
		add_action( 'wp_ajax_delete_keyword_by_row_number', array('Sem_Keyword', 'delete_keyword_by_row_number') );
		wp_localize_script('script', 'ajaxurl', admin_url( 'admin-ajax.php' ) );
    }

    //Menu du gauche
    public function add_admin_menu()
	{
	    $hook = add_menu_page('Dynamic SEM setting', 'Dynamic SEM', 'manage_options', 'sem_tools', array($this, 'menu_html'));
	    add_action('load-'.$hook, array($this, 'process_action'));
	    add_action('load-'.$hook, array('Sem_Varname', 'init_varnames'));
	    add_action('load-'.$hook, array('Sem_Keyword', 'save_keywords'));
	    add_action('load-'.$hook, array($this, 'remove_text_footer_right'));
		 
	    //add_action('admin_enqueue_scripts', 'sem_scripts	_styles');
		wp_register_style( 'custom-style', plugins_url( '../public/css/style.css', __FILE__ ) );
	    wp_enqueue_style( 'custom-style' );
	    wp_enqueue_style('jquery_ui', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css' );
	    wp_enqueue_style( 'cssbootstrap4', 'https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css' );
	    wp_enqueue_style('font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css');
	    
		wp_enqueue_script('jquery_ui', 'https://code.jquery.com/ui/1.12.1/jquery-ui.js', array('jquery') );
	    wp_enqueue_script('popper', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js', array('jquery') );
	    wp_enqueue_script('jsbootstrap4', 'https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js', array('jquery') );
	    
	    wp_register_script( 'sem_plugin', plugins_url('../public/js/sem_plugin.js', __FILE__), array('jquery'));
    	wp_enqueue_script( 'sem_plugin' );
    	
	}

	public function remove_text_footer_right() {
		add_action( 'admin_menu', array($this, 'oz_admin_dashboard_footer_right' ), 100);  
	}
	//Contenu du menu
	public function menu_html()
	{
	    echo '<h1>'.get_admin_page_title().'</h1>';
	    ?>
	    <form method="post" action="#">
	    	<?php settings_fields('sem_tools_settings') ?>
		    <?php do_settings_sections('sem_tools_settings') ?>
			<?php submit_button(); ?>
		</form>
	    <?php	    
	}

	//Les sections et champs
	public function register_settings()
	{
		
	    register_setting('sem_tools_settings', 'page_dropdown');
	    register_setting('sem_tools_settings', 'primary_url');
	    register_setting('sem_tools_settings', 'modal_url');
	    register_setting('sem_tools_settings', 'meta_title');
	    register_setting('sem_tools_settings', 'meta_description');
	    register_setting('sem_tools_settings', 'meta_keywords');

	    add_settings_section('sem_tools_section', '', array($this, 'section_html'), 'sem_tools_settings');
	    add_settings_section('parametre_section', 'Paramètres', array($this, 'parametre_html'),'sem_tools_settings', 'sem_tools_section');
	    add_settings_section('table_section', 'Tableau url/variable', array($this, 'table_html'),'sem_tools_settings', 'sem_tools_section');
	}
	
	public function section_html()
	{	
	}

	//contenu section
	//-------------------------- start ----------------------//
	public function parametre_html()
	{
		$meta_title = get_post_meta( get_option("page_dropdown"), '_sem_tools_title', true );
		$meta_description = get_post_meta( get_option("page_dropdown"), '_sem_tools_description', true );
		$meta_keywords = get_post_meta( get_option("page_dropdown"), '_sem_tools_keywords', true );
		?>
		<div class="wrap">
			<div class="form-group row">
		    	<label for="" class="col-sm-3 col-form-label">Page de référence:</label>
		    	<div class="col-sm-6">
		      		<select name="page_dropdown" class="sem_input page_dropdown form-control" autocomplete="off"> 
					    <option value=""><?php echo attribute_escape(__('-landing source-')); ?></option>
			
					    <?php // Tableau d'arguments pour personalisé la liste des pages
					        $args = array(
			
						    'sort_order' => 'ASC', // ordre
						    'sort_column' => 'post_title', // par titre (post_date = par date ,post_modified = dernière modification, post_author = Par auteur)
						    'hierarchical' => 1, // Hiérarchie des sous pages
						    'exclude' => '', // page a exclure avec leurs ID ex: (2,147)
						    'include' => '', // page a inclure avec leurs ID (5,10)
						    'meta_key' => '',  // Inclure uniquement les pages qui ont cette clé des champs personnalisés
						    'meta_value' => '', // Inclure uniquement les pages qui ont cette valeur de champ personnalisé
						    'authors' => '', // Inclure uniquement les pages écrites par l'auteur(ID)
						    'child_of' => 0, // niveau des sous-pages
						    'parent' => -1, // Affiche les pages qui ont cet ID en tant que parent. La valeur par défaut -1
						    'exclude_tree' => '', // Le contraire de «child_of», «exclude_tree 'supprimera tous les sous pages par ID.
						    'number' => '', // Défini le nombre de pages à afficher
						    'offset' => 0, //  nombre de pages à passer au-dessus
						    'post_type' => 'page', // post type
						    'post_status' => 'publish' // publish = Publier, private = page privé
			
							); 
			
				        $pages = get_pages($args);
				        foreach ($pages as $page) {
				            /*$option = '<option value="'.get_page_link($page->ID).'">';*/
				            $selected = selected(get_option("page_dropdown"), $page->ID);
				            $option = '<option value="'.$page->ID.'" '.  $selected . '>';
				            $option .= $page->post_title;
				            $option .= '</option>';
				            echo $option;
				        }
			
					    ?>
			
					</select>
		    	</div>
		  	</div>
		  	<div class="form-group row">
		    	<label for="" class="col-sm-3 col-form-label">Chemin url primaire:</label>
		    	<div class="col-sm-6">
		      		<div class="input-group">
					    <div class="input-group-prepend">
					      <div class="input-group-text" id="btnGroupAddon">/</div>
					    </div>
					    <input type="text" name="primary_url" class="form-control" placeholder="url primaire" aria-label="Input group example" aria-describedby="btnGroupAddon" value="<?php echo get_option('primary_url') ?>" autocomplete="off">
					</div>
		    	</div>
		  	</div>
		  	<div class="form-group row">
		    	<label for="" class="col-sm-3 col-form-label">Modèle url:</label>
		    	<div class="col-sm-6">
		    		<div class="input-group">
					    <div class="input-group-prepend">
					      <div class="input-group-text" id="btnGroupAddon">/</div>
					    </div>
		      			<input type="text" name="modal_url" class="sem_modal_url form-control" placeholder="modèle url" aria-label="Input group example" aria-describedby="btnGroupAddon" value="<?php  echo (get_option('modal_url')) ? get_option('modal_url') : '' ?>" autocomplete="off"/>
		      		</div>	
		    	</div>
		  	</div>
		  	<div class="form-group row">
		    	<label for="" class="col-sm-3 col-form-label">Modèle Meta Title:</label>
		    	<div class="col-sm-6">
		      		<input type="text" name="meta_title" class="sem_input meta_title form-control" value="<?php echo $meta_title ?>" autocomplete="off"/>
		    	</div>
		  	</div>
		  	<div class="form-group row">
		    	<label for="" class="col-sm-3 col-form-label"></label>
		    	<div class="col-sm-6">
		      		<input type="checkbox" id="title_check" name="sem_title" <?php echo ((get_option('sem_title') == 'on') ? 'checked':''); ?> autocomplete="off"/>
	    			<label for="title_check" class="title_label"> Utiliser également pour l'onglet </label>
		    	</div>
		  	</div>
		    <div class="form-group row">
		    	<label for="" class="col-sm-3 col-form-label">Modèle Meta Description:</label>
		    	<div class="col-sm-6">
		      		<textarea name="meta_description" rows="5" class="sem_input meta_description form-control" autocomplete="off"><?php echo $meta_description ?></textarea>
		    	</div>
		  	</div>
			<div class="form-group row">
		    	<label for="" class="col-sm-3 col-form-label">Modèle Meta Keywords:</label>
		    	<div class="col-sm-6">
		      		<input type="text" name="meta_keywords_url" class="sem_input meta_keywords_url form-control" value="<?php echo $meta_keywords ?>" autocomplete="off"/>
		    	</div>
		  	</div>
		    	
	    </div>
	    <?php
	}
	
	public function table_html()
	{
	   //Change footer script
	   add_filter( 'admin_footer_text', array($this, 'oz_alter_wp_admin_bottom_left_text' ));
	   add_action( 'admin_menu', array($this, 'oz_admin_dashboard_footer_right' ), 999); 
	   $keyword_count = Sem_Keyword::get_all_sem_keyword();
	   $varname_count = Sem_Keyword::get_all_sem_varname();
	   ?>
		<div class="wrap">
		    <table class="wp-list-table widefat plugins striped" id="sem_variables_table">
				<thead>
					<tr>
						<th><input type="checkbox" class="check all_check" name="active[]" autocomplete="off"></th>
						<th width="30%">URL Temporaire</th>
						<th>
							[variable1]
							<i class="fa fa-pencil-square-o" data-toggle="tooltip" data-placement="top" title="Copier le variable" onclick="copyVariable(this, '[variable1]');"></i> 
						</th>
						<th>
							[variable2]
							<i class="fa fa-pencil-square-o" data-toggle="tooltip" data-placement="top" title="Copier le variable" onclick="copyVariable(this, '[variable2]');"></i> 
						</th>
						<th> 
							[variable3]
							<i class="fa fa-pencil-square-o" data-toggle="tooltip" data-placement="top" title="Copier le variable" onclick="copyVariable(this, '[variable3]');"></i> 
						</th>
						<th>
							[variable4]
							<i class="fa fa-pencil-square-o" data-toggle="tooltip" data-placement="top" title="Copier le variable" onclick="copyVariable(this, '[variable4]');"></i> 
						</th>
						<th>
							[variable5]
							<i class="fa fa-pencil-square-o" data-toggle="tooltip" data-placement="top" title="Copier le variable" onclick="copyVariable(this, '[variable5]');"></i> 
						</th>
						<th>
						</th>
					</tr>
				</thead>
			
				<tbody>
					<?php if($keyword_count > 0) { ?>
						<?php for($i = 1 ; $i <= $keyword_count ; $i++) { ?>
						<tr class="row-tab" data-line="<?php echo $i; ?>">
							<td><input type="checkbox" class="check" name="lp_item[]" value="<?php echo $i; ?>" autocomplete="off"></td>
							<td>
								<input type="text" readonly name="url[<?php echo $i; ?>]" placeholder="url<?php echo $i; ?>" class="sem_table temporary_url url<?php echo $i; ?>"/>
							</td>
							<?php for($j = 1 ; $j <= $varname_count ; $j++) { ?>
								<td>
									<input type="text" name="keyword[<?php echo $i; ?>][<?php echo $j; ?>]" placeholder="variable<?php echo $j; ?>.<?php echo $i; ?>" class="sem_table" data_var="variable<?php echo $j; ?>" value="<?php echo Sem_Keyword::get_keyword($i, $j); ?>" />
								</td>
							<?php } ?>
							<td>
								<a href="#" targer="_blank"><i class="fa fa-eye"></i></a>
							</td>
						</tr>
						<?php } ?>
					<?php } else { ?>
					    <?php $varname_count = 5; ?>
						<tr class="row-tab" data-line="">
							<td><input type="checkbox" class="check" name="lp_item[]" value="" autocomplete="off"></td>
							<td>
								<input type="text" readonly name="url[1]" placeholder="url1" class="sem_table temporary_url url1"/>
							</td>
							<?php for($j = 1 ; $j <= $varname_count ; $j++) { ?>
								<td>
									<input type="text" name="keyword[1][<?php echo $j; ?>]" placeholder="variable1.<?php echo $j; ?>" class="sem_table" data_var="variable<?php echo $j; ?>" value="" />
								</td>
							<?php } ?>
							<td>
								<a href="#" targer="_blank"><i class="fa fa-eye"></i></a>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			
				<tfoot>
					
				</tfoot>
			
			</table>
			<br>
			<div class="add-line">
				<button class="btn btn-primary pull-left" id="remove_line">Supprimer la ligne</button>
				<button class="btn btn-primary pull-right" id="add_line">Ajouter une ligne</button>
			</div>
			
			<p>Activer / Désactiver les variantes</p>
			<div class="">
				<label class="switch" id="switch">
			    	<input class="switch-input" type="checkbox" name="sem_active" <?php echo ((get_option('sem_active') == 'on') ? 'checked':''); ?>/>
			    	<span class="switch-label" data-on="On" data-off="Off"></span> 
			    	<span class="switch-handle"></span> 
			    </label>
			</div>
		</div>
		<script type="text/javascript">
			$('#add_line').click(function(event){
				event.preventDefault();
				var last_line = $('tbody .row-tab:last').attr('data-line');
				var line = parseInt(last_line) + 1;
				var html = '';
				html += '<tr class="row-tab" data-line="'+line+'"><td><input type="checkbox" class="check" name="lp_item[]" value="<?php echo "'+line+'"; ?>" autocomplete="off"></td><td><input type="text" readonly name="url["'+line+'"]" placeholder="url"'+line+'" class="sem_table temporary_url url"'+line+'"/></td>';
				for(var i = 1 ; i<=5 ; i++){
					html += '<td><input type="text" name="keyword['+line+']['+i+']" placeholder="variable'+line+'.'+i+'" class="sem_table" data_var="variable'+i+'" value=""/></td>';
				}
				html += '<td><a href="#" class="disabled" targer="_blank"><i class="fa fa-eye"></i></a></td>';
				html += '</tr>';	
		        $('#sem_variables_table tbody').append(html);
		    });
		</script>
	    <?php
	}
	//------------------------ end ------------------------//

	//action
	public function process_action()
	{
		//exit($_POST['page_dropdown']);
		if(isset($_POST['page_dropdown']) && isset($_POST['meta_title']) && isset($_POST['meta_description']) && isset($_POST['meta_keywords_url'])){
			update_post_meta( $_POST['page_dropdown'], "_sem_tools_title", $_POST['meta_title']);
			update_post_meta( $_POST['page_dropdown'], "_sem_tools_description", $_POST['meta_description']);
			update_post_meta( $_POST['page_dropdown'], "_sem_tools_keywords", $_POST['meta_keywords_url']);
			update_option('page_dropdown', $_POST['page_dropdown'], true);
			update_option('primary_url', $_POST['primary_url'], true);
			update_option('modal_url', $_POST['modal_url'], true);
			update_option('sem_active', $_POST['sem_active'], true);
			update_option('sem_title', $_POST['sem_title'], true);
		}
	}
	
    public static function data_in_head($text)
    {
    	global $wp_query;
		$page_id = get_option('page_dropdown');
		$pattern = '/\[variable[0-9]\]/';
		$matches = preg_match_all($pattern, $text, $array);			
		
		$row = $wp_query->query_vars['row'];
		
		$remplacements = array();
		foreach($array[0] as $key=>$value)
		{
			$remplacements[$key] = Sem_Keyword::get_keyword($row, $value[strlen($value)-1]);
		}

		return str_replace($array[0], $remplacements, $text);
    }

	function replace_text_sem_wps($text){
		global $post;
        $page_id = get_option('page_dropdown');
      
        if($page_id == $post->ID){
			return self::data_in_head($text);
        }
	}

	function change_the_title() {
		global $post;
        $page_id = get_option('page_dropdown');
      
        if($page_id == $post->ID && get_option('sem_title') == 'on'){
	    	return self::data_in_head(get_post_meta($page_id, "_sem_tools_title", true));
        }	
	}

	public function add_meta_tags() 
	{
	    global $post;
        $page_id = get_option('page_dropdown');
      
        if($page_id == $post->ID){       	
			
	        add_action('get_header', array($this, 'add_title'), 100);
		    add_action('get_header', array($this, 'add_description'), 100);
		    add_action('get_header', array($this, 'add_keywords'), 100); 
		    add_action('get_footer', array($this, 'title_wordpress_in_footer'), 100); 
		    add_action('wp_footer', function(){ ob_end_flush(); }, 100);
        } 
	}
	
	public function add_meta_tag_title($html) {
		global $post;
        $page_id = get_option('page_dropdown');
	    $pattern = '/<meta name(.*)=(.*)"title"(.*)>/i';
	    $pattern_title = '/<\/title>/i';
	    if(preg_match($pattern, $html)){
	    	$html = preg_replace($pattern, '</title><meta name="title" content="'.self::data_in_head(get_post_meta($page_id, "_sem_tools_title", true)).'">', $html);	
	    }else{
	    	$html = preg_replace($pattern_title, '</title><meta name="title" content="'.self::data_in_head(get_post_meta($page_id, "_sem_tools_title", true)).'">', $html);
	    }
	    return $html;
	}

	public function add_title($html) {
	    ob_start(array($this, 'add_meta_tag_title'));
	}

	public function add_meta_tag_description($html) {
        $page_id = get_option('page_dropdown');
	    $pattern = '/<meta name(.*)=(.*)"description"(.*)>/i';
	    $pattern_title = '/<\/title>/i';
	    if(preg_match($pattern, $html)){
	    	$html = preg_replace($pattern, '</title><meta name="description" content="'.self::data_in_head(get_post_meta($page_id, "_sem_tools_description", true)).'">', $html);	
	    }else{
	    	$html = preg_replace($pattern_title, '</title><meta name="description" content="'.self::data_in_head(get_post_meta($page_id, "_sem_tools_description", true)).'">', $html);
	    }
	    return $html;
	}

	public function add_description($html) {
	    ob_start(array($this, 'add_meta_tag_description'));
	}

	public function add_meta_tag_keywords($html) {
        $page_id = get_option('page_dropdown');
	    $pattern = '/<meta name(.*)=(.*)"keywords"(.*)>/i';
	    $pattern_title = '/<\/title>/i';
	    if(preg_match($pattern, $html)){
	    	$html = preg_replace($pattern, '</title><meta name="keywords" content="'.self::data_in_head(get_post_meta($page_id, '_sem_tools_keywords', true)).'">', $html);	
	    }else{
	    	$html = preg_replace($pattern_title, '</title><meta name="keywords" content="'.self::data_in_head(get_post_meta($page_id, '_sem_tools_keywords', true)).'">', $html);
	    }
	    return $html;
	}

	public function add_keywords($html) {
	    ob_start(array($this, 'add_meta_tag_keywords'));
	}
	
	public function oz_alter_wp_admin_bottom_left_text( $text ) {
		return sprintf( __( 'Dynamic SEM © <a href="%s" title="Alternateeve Technology" target="_blank">Alternateeve Technology</a> Inc.' ), 'https://www.alternateeve.com' );
	}
	
	public function oz_admin_dashboard_footer_right() {
	    remove_filter( 'update_footer', 'core_update_footer' );
	}
	
}

