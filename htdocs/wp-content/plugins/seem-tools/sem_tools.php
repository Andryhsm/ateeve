<?php
/*
Plugin Name: SEM Tools
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
        new Landing_Page_Info();
    	// Active la création de la table newsletter dans la base de données
        register_activation_hook(__FILE__, array('Sem_Tools', 'install'));
        // Supprime la table en cas de désactivation 
		register_uninstall_hook(__FILE__, array('Sem_Tools', 'uninstall'));
    }
    
     // Enregistre la table sem_tools dans la base de données
	public static function install()
	{
	    global $wpdb;
	    $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}sem_varname
		      (varname_id SMALLINT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
		      varname VARCHAR(50));");
		$wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}sem_keywords	
			 	(keyword_id SMALLINT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
				varname_id SMALLINT UNSIGNED,
				row_number SMALLINT UNSIGNED,
				keywords VARCHAR(100),
				FOREIGN KEY(varname_id) 
				REFERENCES {$wpdb->prefix}sem_varname(varname_id));");
	    //$wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}sem_tools_url (sem_tools_url_id INT AUTO_INCREMENT PRIMARY KEY,post_id INT NOT NULL, temporary_url VARCHAR(255) NOT NULL);");
	    //$wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}sem_tools_url_keywords (sem_tools_url_keywords_id INT AUTO_INCREMENT PRIMARY KEY, temporary_url_id INT NOT NULL, keyword VARCHAR(255) NOT NULL);");
	    // $wpdb->query("INSERT INTO {$wpdb->prefix}landingpage_keys (`id`, `temporary_url`, `keys`) VALUES ('', '', '')")
	}

	public static function uninstall()
	{
	    global $wpdb;
	   // $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}sem_tools_url;");
	    //$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}sem_tools_url_keywords;");
	    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}sem_keywords;");
	    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}sem_varname;");
	    
	}
  
}
new Sem_Tools();
