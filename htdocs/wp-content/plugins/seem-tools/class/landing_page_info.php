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
        add_action('wp_head', array($this, 'add_meta_tags'));
      
        //add_action('get_header', array($this, 'clean_meta_generator'), 100);
        add_filter('pre_get_document_title', array($this, 'change_the_title'), 999, 1);
    }

    //Menu du gauche
    public function add_admin_menu()
	{
	    $hook = add_menu_page('SEM Tools setting', 'SEM Tools', 'manage_options', 'sem_tools', array($this, 'menu_html'));
	    add_action('load-'.$hook, array($this, 'process_action'));
	    add_action('load-'.$hook, array('Sem_Varname', 'init_varnames'));
	    add_action('load-'.$hook, array('Sem_Keyword', 'save_keywords'));
	    
	    //add_action('admin_enqueue_scripts', 'sem_scripts_styles');
		wp_register_style( 'custom-style', plugins_url( '../public/css/style.css', __FILE__ ) );
	    wp_enqueue_style( 'custom-style' );
	    
	    wp_register_script( 'sem_plugin', plugins_url('../public/js/sem_plugin.js', __FILE__), array('jquery'));
    	wp_enqueue_script( 'sem_plugin' );
    	
    	wp_enqueue_style('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css' );
		wp_enqueue_script('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js', array('jquery') );
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

	//Le section et champs
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
	    add_settings_section('table_section', 'Tableau Url/Variable', array($this, 'table_html'),'sem_tools_settings', 'sem_tools_section');
	}


	//section parent
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
		$text_area_balise = "<head>\n";
		$text_area_balise .= "<meta name=\"title\" content=\"modèle meta title\">\n";
		$text_area_balise .= "<meta name=\"description\" content=\"modèle meta description\">\n";
		$text_area_balise .= "<meta name=\"keywords\" content=\"modèle meta keywords\">\n";
		$text_area_balise .= "</head>";
		?>
	    <table width="100%">
	    	<tr>
	    		<td width="15%"><label for="">Page de référence:</label></td>
	    		<td width="45%">
	    			<select name="page_dropdown" class="sem_input page_dropdown" autocomplete="off"> 
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
				</td>
				<td width="40%"></td>
	    	</tr>
	    	<tr>
	    		<td><label for="">Chemin URL primaire:</label></td>
	    		<td><input type="text" name="primary_url" class="sem_input primary_url" value="<?php echo get_option('primary_url') ?>"/></td>
	    		<td></td>
	    	</tr>
	    	<tr>
	    		<td><label for="">Modèle URL:</label></td>
	    		<td>
	    			<select class="sem_modal_url_multiple" name="modal_url[]" multiple="multiple" autocomplete="off">
	    				<option value="/" selected="selected">/</option>
	    				<option value="recruter" <?php selected(selected( true, in_array('recruter', get_option('modal_url') ) )) ?>>recruter</option>
	    				<option value="developpeur" <?php selected(selected( true, in_array('developpeur', get_option('modal_url') ) )) ?>>developpeur</option>
					   	<option value="$var1" <?php selected(selected( true, in_array('$var1', get_option('modal_url') ) )) ?>>$var1</option>
					   	<option value="$var2" <?php selected(selected( true, in_array('$var2', get_option('modal_url') ) )) ?>>$var2</option>
					   	<option value="$var3" <?php selected(selected( true, in_array('$var3', get_option('modal_url') ) )) ?>>$var3</option>
					   	<option value="$var4" <?php selected(selected( true, in_array('$var4', get_option('modal_url') ) )) ?>>$var4</option>
					   	<option value="$var5" <?php selected(selected( true, in_array('$var5', get_option('modal_url') ) )) ?>>$var5</option>
					</select>
	    		</td>
	    		<td></td>
	    	</tr>
	    	<tr>
	    		<td><label for="">Modèle Meta Title:</label></td>
	    		<td><input type="text" name="meta_title" class="sem_input meta_title" value="<?php echo $meta_title ?>"/></td>
	    		<td width="50%" rowspan="3">
					<textarea class="sem_input sem_area"><?php echo $text_area_balise ?></textarea>
				</td>
	    	</tr>
	    	<tr>
	    		<td><label for="">Modèle Meta Description:</label></td>
	    		<td><textarea name="meta_description" rows="5" class="sem_input meta_description"><?php echo $meta_description ?></textarea></td>
	    	</tr>
	    	<tr>
	    		<td><label for="">Modèle Meta Keywords:</label></td>
	    		<td><input type="text" name="meta_keywords_url" class="sem_input meta_keywords_url" value="<?php echo $meta_keywords ?>" readonly/></td>
	    	</tr>
	    	
	    </table>
	    
	    <?php
	}
	
	public function table_html()
	{	?>
	    <table class="wp-list-table widefat plugins" id="sem_vars_table">
			<thead>
				<tr>
					<th>URL Temporaire</th>
					<th>Variable 1</th>
					<th>Variable 2</th>
					<th>Variable 3</th>
					<th>Variable 4</th>
					<th>Variable 5</th>
				</tr>
			</thead>
		
			<tbody>
				<tr>
					<td>
						<input type="text" readonly name="url[]" placeholder="url1" class="sem_table url1"/>
					</td>
					<td>
						<input type="text" name="keyword[1][1]" placeholder="var1.1" class="sem_table" id="var1.1" value="<?php Sem_Keyword::get_keyword(1, 1) ?>" />
					</td>
					<td>
						<input type="text" name="keyword[1][2]" placeholder="var1.2" class="sem_table" id="var1.2" value="<?php Sem_Keyword::get_keyword(1, 2) ?>" />
					</td>
					<td>
						<input type="text" name="keyword[1][3]" placeholder="var1.3" class="sem_table" id="var1.3" value="<?php Sem_Keyword::get_keyword(1, 3) ?>" />
					</td>
					<td>
						<input type="text" name="keyword[1][4]" placeholder="var1.4" class="sem_table" id="var1.4" value="<?php Sem_Keyword::get_keyword(1, 4) ?>" />
					</td>
					<td>
						<input type="text" name="keyword[1][5]" placeholder="var1.5" class="sem_table" id="var1.5" value="<?php Sem_Keyword::get_keyword(1, 5) ?>" />
					</td>
				</tr>
				<tr>
					<td>
						<input type="text" readonly name="url[]" placeholder="url1" class="sem_table url2"/>
					</td>
					<td>
						<input type="text" name="keyword[2][1]" placeholder="var2.1" class="sem_table" value="<?php Sem_Keyword::get_keyword(2, 1) ?>"/>
					</td>
					<td>
						<input type="text" name="keyword[2][2]" placeholder="var2.2" class="sem_table"
						 value="<?php Sem_Keyword::get_keyword(2, 2) ?>"/>
					</td>
					<td>
						<input type="text" name="keyword[2][3]" placeholder="var2.3" class="sem_table"
						 value="<?php Sem_Keyword::get_keyword(2, 3) ?>"/>
					</td>
					<td>
						<input type="text" name="keyword[2][4]" placeholder="var2.4" class="sem_table"
						 value="<?php Sem_Keyword::get_keyword(2, 4) ?>"/>
					</td>
					<td>
						<input type="text" name="keyword[2][5]" placeholder="var2.5" class="sem_table"
						 value="<?php Sem_Keyword::get_keyword(2, 5) ?>"/>
					</td>
				</tr>
				<tr>
					<td>
						<input type="text" readonly name="url[]" placeholder="url1" class="sem_table url3"/>
					</td>
					<td>
						<input type="text" name="keyword[3][1]" placeholder="var3.1" class="sem_table" 
						value="<?php Sem_Keyword::get_keyword(3, 1) ?>"/>
					</td>
					<td>
						<input type="text" name="keyword[3][2]" placeholder="var3.2" class="sem_table"
						 value="<?php Sem_Keyword::get_keyword(3, 2) ?>"/>
					</td>
					<td>
						<input type="text" name="keyword[3][3]" placeholder="var3.3" class="sem_table"
						 value="<?php Sem_Keyword::get_keyword(3, 3) ?>"/>
					</td>
					<td>
						<input type="text" name="keyword[3][4]" placeholder="var3.4" class="sem_table"
						 value="<?php Sem_Keyword::get_keyword(3, 4) ?>"/>
					</td>
					<td>
						<input type="text" name="keyword[3][5]" placeholder="var3.5" class="sem_table"
						 value="<?php Sem_Keyword::get_keyword(3, 5) ?>"/>
					</td>
				</tr>
				<tr>
					<td>
						<input type="text" readonly name="url[]" placeholder="url1" class="sem_table url4"/>
					</td>
					<td>
						<input type="text" name="keyword[4][1]" placeholder="var4.1" class="sem_table"
						 value="<?php Sem_Keyword::get_keyword(4, 1) ?>"/>
					</td>
					<td>
						<input type="text" name="keyword[4][2]" placeholder="var4.2" class="sem_table"
						 value="<?php Sem_Keyword::get_keyword(4, 2) ?>"/>
					</td>
					<td>
						<input type="text" name="keyword[4][3]" placeholder="var4.3" class="sem_table"
						 value="<?php Sem_Keyword::get_keyword(4, 3) ?>"/>
					</td>
					<td>
						<input type="text" name="keyword[4][4]" placeholder="var4.4" class="sem_table"
						 value="<?php Sem_Keyword::get_keyword(4, 4) ?>"/>
					</td>
					<td>
						<input type="text" name="keyword[4][5]" placeholder="var4.5" class="sem_table"
						 value="<?php Sem_Keyword::get_keyword(4, 5) ?>"/>
					</td>
				</tr>
				<tr>
					<td>
						<input type="text" readonly name="url[]" placeholder="url1" class="sem_table url5"/>
					</td>
					<td>
						<input type="text" name="keyword[5][1]" placeholder="var5.1" class="sem_table"
						 value="<?php Sem_Keyword::get_keyword(5, 1) ?>"/>
					</td>
					<td>
						<input type="text" name="keyword[5][2]" placeholder="var5.2" class="sem_table"
						 value="<?php Sem_Keyword::get_keyword(5, 2) ?>" />
					</td>
					<td>
						<input type="text" name="keyword[5][3]" placeholder="var5.3" class="sem_table"
						 value="<?php Sem_Keyword::get_keyword(5, 3) ?>" />
					</td>
					<td>
						<input type="text" name="keyword[5][4]" placeholder="var5.4" class="sem_table"
						 value="<?php Sem_Keyword::get_keyword(5, 4) ?>" />
					</td>
					<td>
						<input type="text" name="keyword[5][5]" placeholder="var5.5" class="sem_table"
						 value="<?php Sem_Keyword::get_keyword(5, 5) ?>" />
					</td>
				</tr>
			</tbody>
		
			<tfoot>
				
			</tfoot>
		
		</table>
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
		}
	}
	
	
    public function save_variables()
    {
    	if(isset($_POST['keyword'])){
    			
    	}
    }
	
	public function add_meta_tags() 
	{
	  global $post;
      $page_id = get_option('page_dropdown');
      
      if($page_id == $post->ID){
            $title = get_post_meta($page_id, '_sem_tools_title');
	        echo '<meta name="description" content="'.get_post_meta($page_id, "_sem_tools_description").'">';
	        echo '<meta name="keywords" content="'.get_post_meta($page_id, '_sem_tools_keywords').'">';
      } 
	}
	
	public function change_the_title() {
		$sem_title = get_option('meta_title');
		$wp_title = get_option('blogname').' | '.get_option('blogdescription');
		if($page_id == $post->ID){
			//update_option('meta_title', $wp_title);
		//	update_option('blogname', "");
	     //  update_option('blogdescription', $sem_title, true);
		} else {
			//list($blogname, $blogdescription) = split(' | ', $wp_title);
			//update_option('blogname', $blogname, true);
	        //update_option('blogdescription', $blogdescription, true);
		}
        return "Je suis le titre du blogue";
    }
    
	public function remove_meta_generators($html) {
        $pattern = '/<meta name(.*)=(.*)"generator"(.*)>/i';
        $html = preg_replace($pattern, '', $html);
        return $html;
    }
    
    public function clean_meta_generator($html) {
        ob_start('remove_meta_generators');
    }
    
    public function get_sem_meta_keywords() {
    	
    }
}

