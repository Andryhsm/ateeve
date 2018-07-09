<?php 

class Sem_Varname
{
    private $varname_id;
    private $varname;
    
    public function __construct()
    {
        
    }
    
    public function init_varnames(){
        global $wpdb;
        if(!$wpdb->query("SELECT * FROM {$wpdb->prefix}sem_varname")){
            $wpdb->query("INSERT INTO {$wpdb->prefix}sem_varname (varname) VALUES ('Variable 1')");
            $wpdb->query("INSERT INTO {$wpdb->prefix}sem_varname (varname) VALUES ('Variable 2')");
            $wpdb->query("INSERT INTO {$wpdb->prefix}sem_varname (varname) VALUES ('Variable 3')");
            $wpdb->query("INSERT INTO {$wpdb->prefix}sem_varname (varname) VALUES ('Variable 4')");
            $wpdb->query("INSERT INTO {$wpdb->prefix}sem_varname (varname) VALUES ('Variable 5')");
        }
    }
}
