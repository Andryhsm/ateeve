<?php

class Landing_Page_Info
{
    public function __construct()
    {
        //Ajout menu
        add_action('admin_menu', array($this, 'add_admin_menu'));

        //enregistrement form
        add_action('admin_init', array($this, 'register_settings'));
        
        //Modification du meta description 
        add_action('wp_head', array($this, 'add_meta_tags'));
        
        add_action('get_header', array($this, 'clean_meta_generator'), 100);
        add_filter('pre_get_document_title', array($this, 'change_the_title'));
        
		/*add_action( 'admin_enqueue_scripts', 'sem_tools_admin_head' );*/ //Ajout css 
		
    	// Active la création de la table newsletter dans la base de données
        register_activation_hook(__FILE__, array('Landing_Page_Info', 'install'));
        // Supprime la table en cas de désactivation 
		register_uninstall_hook(__FILE__, array('Landing_Page_Info', 'uninstall'));
    }

    //Menu du gauche
    public function add_admin_menu()
	{
	    $hook = add_menu_page('SEM Tools setting', 'SEM Tools', 'manage_options', 'sem_tools', array($this, 'menu_html'));
	    add_action('load-'.$hook, array($this, 'process_action'));
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

	    add_settings_section('sem_tools_section', 'Paramètres d\'enregistrement', array($this, 'section_html'), 'sem_tools_settings');
	    add_settings_field('page_dropdown_url', 'Page de référence:', array($this, 'page_dropdown_html'), 'sem_tools_settings', 'sem_tools_section');
	    add_settings_field('primary_url', 'Chemin URL primaire:', array($this, 'primary_url_html'), 'sem_tools_settings', 'sem_tools_section');
	    add_settings_field('modal_url', 'Modèle URL:', array($this, 'modal_url_html'), 'sem_tools_settings', 'sem_tools_section');
	    add_settings_field('meta_title', 'Modèle Meta Title:', array($this, 'meta_title_html'), 'sem_tools_settings', 'sem_tools_section');
	    add_settings_field('meta_description', 'Modèle Meta Description:', array($this, 'meta_description_html'), 'sem_tools_settings', 'sem_tools_section');
	    add_settings_field('meta_keywords', 'Modèle Meta Keywords:', array($this, 'meta_keywords_html'), 'sem_tools_settings', 'sem_tools_section');
	    add_settings_section('table_section', 'Tableau Url/Variable', array($this, 'table_html'),'sem_tools_settings', 'sem_tools_section');
	}

	//section
	public function section_html()
	{
			
	}

	//contenu section
	//-------------------------- start ----------------------//
	public function page_dropdown_html()
	{?>
	    <select name="page_dropdown" autocomplete="off"> 
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
	    <?php
	}
	public function primary_url_html()
	{?>
	    <input type="text" name="primary_url" value="<?php echo get_option('primary_url') ?>"/>
	    <?php
	}
	public function modal_url_html()
	{?>
	    <input type="text" name="modal_url" value="<?php echo get_option('modal_url') ?>"/>
	    <?php
	}
	public function meta_title_html()
	{
		$meta_title = get_post_meta( get_option("page_dropdown"), '_sem_tools_title', true );
		?>
	    <input type="text" name="meta_title" value="<?php echo $meta_title ?>"/>
	    <?php
	}
	public function meta_description_html()
	{
		$meta_description = get_post_meta( get_option("page_dropdown"), '_sem_tools_description', true );
		?>
	    <input type="text" name="meta_description" value="<?php echo $meta_description ?>"/>
	    <?php
	}
	public function meta_keywords_html()
	{
		$meta_keywords = get_post_meta( get_option("page_dropdown"), '_sem_tools_keywords', true );
		?>
	    <input type="text" name="meta_keywords_url" value="<?php echo $meta_keywords ?>"/>
	    <?php
	}
	public function table_html()
	{	?>
	    <table class="wp-list-table widefat plugins">
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
					<td>/url1</td>
					<td>
						<input type="text" name="keyword[1][]" placeholder="var1.1" style="border: none; box-shadow: none; width: 100%;"/>
					</td>
					<td>
						<input type="text" name="keyword[1][]" placeholder="var1.2" style="border: none; box-shadow: none; width: 100%;"/>
					</td>
					<td>
						<input type="text" name="keyword[1][]" placeholder="var1.3" style="border: none; box-shadow: none; width: 100%;"/>
					</td>
					<td>
						<input type="text" name="keyword[1][]" placeholder="var1.4" style="border: none; box-shadow: none; width: 100%;"/>
					</td>
					<td>
						<input type="text" name="keyword[1][]" placeholder="var1.5" style="border: none; box-shadow: none; width: 100%;"/>
					</td>
				</tr>
				<tr>
					<td>/url2</td>
					<td>
						<input type="text" name="keyword[2][]" placeholder="var2.1" style="border: none; box-shadow: none; width: 100%;"/>
					</td>
					<td>
						<input type="text" name="keyword[2][]" placeholder="var2.2" style="border: none; box-shadow: none; width: 100%;"/>
					</td>
					<td>
						<input type="text" name="keyword[2][]" placeholder="var2.3" style="border: none; box-shadow: none; width: 100%;"/>
					</td>
					<td>
						<input type="text" name="keyword[2][]" placeholder="var2.4" style="border: none; box-shadow: none; width: 100%;"/>
					</td>
					<td>
						<input type="text" name="keyword[2][]" placeholder="var2.5" style="border: none; box-shadow: none; width: 100%;"/>
					</td>
				</tr>
				<tr>
					<td>/url3</td>
					<td>
						<input type="text" name="keyword[3][]" placeholder="var3.1" style="border: none; box-shadow: none; width: 100%;"/>
					</td>
					<td>
						<input type="text" name="keyword[3][]" placeholder="var3.2" style="border: none; box-shadow: none; width: 100%;"/>
					</td>
					<td>
						<input type="text" name="keyword[3][]" placeholder="var3.3" style="border: none; box-shadow: none; width: 100%;"/>
					</td>
					<td>
						<input type="text" name="keyword[3][]" placeholder="var3.4" style="border: none; box-shadow: none; width: 100%;"/>
					</td>
					<td>
						<input type="text" name="keyword[3][]" placeholder="var3.5" style="border: none; box-shadow: none; width: 100%;"/>
					</td>
				</tr>
				<tr>
					<td>/url4</td>
					<td>
						<input type="text" name="keyword[4][]" placeholder="var4.1" style="border: none; box-shadow: none; width: 100%;"/>
					</td>
					<td>
						<input type="text" name="keyword[4][]" placeholder="var4.2" style="border: none; box-shadow: none; width: 100%;"/>
					</td>
					<td>
						<input type="text" name="keyword[4][]" placeholder="var4.3" style="border: none; box-shadow: none; width: 100%;"/>
					</td>
					<td>
						<input type="text" name="keyword[4][]" placeholder="var4.4" style="border: none; box-shadow: none; width: 100%;"/>
					</td>
					<td>
						<input type="text" name="keyword[4][]" placeholder="var4.5" style="border: none; box-shadow: none; width: 100%;"/>
					</td>
				</tr>
				<tr>
					<td>/url5</td>
					<td>
						<input type="text" name="keyword[5][]" placeholder="var5.1" style="border: none; box-shadow: none; width: 100%;"/>
					</td>
					<td>
						<input type="text" name="keyword[5][]" placeholder="var5.2" style="border: none; box-shadow: none; width: 100%;"/>
					</td>
					<td>
						<input type="text" name="keyword[5][]" placeholder="var5.3" style="border: none; box-shadow: none; width: 100%;"/>
					</td>
					<td>
						<input type="text" name="keyword[5][]" placeholder="var5.4" style="border: none; box-shadow: none; width: 100%;"/>
					</td>
					<td>
						<input type="text" name="keyword[5][]" placeholder="var5.5" style="border: none; box-shadow: none; width: 100%;"/>
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
		if(isset($_POST['page_dropdown']) && isset($_POST['meta_description'])){
			update_post_meta( $_POST['page_dropdown'], "_sem_tools_title", $_POST['meta_title']);
			update_post_meta( $_POST['page_dropdown'], "_sem_tools_description", $_POST['meta_description']);
			update_post_meta( $_POST['page_dropdown'], "_sem_tools_keywords", $_POST['meta_keywords_url']);
			update_option('page_dropdown', $_POST['page_dropdown'], true);
			update_option('primary_url', $_POST['primary_url'], true);
			update_option('modal_url', $_POST['modal_url'], true);
		}
	}
	

	/*function sem_tools_admin_head() {
		//wp_enqueue_style( 'sem_tools_stylesheet', plugins_url( 'public/css/style.css', __FILE__ ) );
		wp_register_style( 'sem_tools_stylesheet',  plugin_dir_url( __FILE__ ) . 'public/css/style.css' );
    	wp_enqueue_style( 'sem_tools_stylesheet' );
    }*/

	
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
        return 'The expected title';
    }
    
	public function remove_meta_generators($html) {
        $pattern = '/<meta name(.*)=(.*)"generator"(.*)>/i';
        $html = preg_replace($pattern, '', $html);
        return $html;
    }
    
    public function clean_meta_generator($html) {
        ob_start('remove_meta_generators');
    }
    
    // Enregistre la table sem_tools dans la base de données
	public static function install()
	{
	    global $wpdb;

	    //$wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}landingpage_keys (id INT AUTO_INCREMENT PRIMARY KEY, temporary_url VARCHAR(255) NOT NULL, meta_keys VARCHAR(255) NOT NULL);");
	    // $wpdb->query("INSERT INTO {$wpdb->prefix}landingpage_keys (`id`, `temporary_url`, `keys`) VALUES ('', '', '')")
	    
	}

	public static function uninstall()
	{
	    global $wpdb;

	    //$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}landingpage_keys;");
	}
	
}

// class Landing_Page {

//     public function __construct()
//     {
//     	// Active la création de la table newsletter dans la base de données
//         register_activation_hook(__FILE__, array('Landing_Page', 'install'));
//         // Supprime la table en cas de désactivation 
// 		register_uninstall_hook(__FILE__, array('Landing_Page', 'uninstall'));

//     }

//     // Enregistre la table newsletter dans la base de données
// 	public static function install()
// 	{
// 	    global $wpdb;

// 	    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}landingpage_keys (id INT AUTO_INCREMENT PRIMARY KEY, temporary_url VARCHAR(255) NOT NULL, meta_keys VARCHAR(255) NOT NULL);");
// 	    // $wpdb->query("INSERT INTO {$wpdb->prefix}landingpage_keys (`id`, `temporary_url`, `keys`) VALUES ('', '', '')")
	    
// 	}

// 	public static function uninstall()
// 	{
// 	    global $wpdb;

// 	    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}landingpage_keys;");
// 	}
// }
