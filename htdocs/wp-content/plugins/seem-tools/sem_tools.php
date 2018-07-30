<?php
/*
Plugin Name: Dynamic SEM
Plugin URI: https://alternateeve.com/
Description: Plugin pour créer des landings pages dynamique
Author: Alternateeve Technology Inc.
Version: 1.0
Author URI: http://alternateeve.com/
*/
   
class Sem_Tools
{
    public function __construct()
    {
        include_once plugin_dir_path( __FILE__ ).'/class/landing_page_info.php';
        include_once plugin_dir_path( __FILE__ ).'/class/sem_rewrite_url.php';
        new Landing_Page_Info();
        new Sem_Rewrite_Url();
    	// Active la création de la table newsletter dans la base de données
        register_activation_hook(__FILE__, array('Sem_Tools', 'install'));
        // Supprime la table en cas de désactivation 
		register_uninstall_hook(__FILE__, array('Sem_Tools', 'uninstall'));
    }
    
     // Enregistre la table sem_tools dans la base de données
	public function install()
	{
	    global $wpdb;
	    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}sem_varname
		      (varname_id SMALLINT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
		      varname VARCHAR(50));");
		$wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}sem_keyword	
			 	(keyword_id SMALLINT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
				varname_id SMALLINT UNSIGNED,
				row_number SMALLINT UNSIGNED,
				keyword VARCHAR(100),
				FOREIGN KEY(varname_id) 
				REFERENCES {$wpdb->prefix}sem_varname(varname_id));");
	}

	public function uninstall()
	{
	    global $wpdb;
	    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}sem_keyword;");
	    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}sem_varname;");
	    
	}
  
}
new Sem_Tools();
