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
        
    }

  
}
new Sem_Tools();
