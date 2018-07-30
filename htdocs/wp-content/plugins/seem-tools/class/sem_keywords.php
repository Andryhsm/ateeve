<?php

class Sem_Keyword
{
    private $keyword_id;
    private $varname_id;
    private $row_number;
    private $keyword;
    
    
    public function __construct()
    {
        
    }
    
    public static function get_keyword($row_number, $varname_id)
    {
        global $wpdb;
        $sem_keyword = $wpdb->get_results("SELECT keyword FROM {$wpdb->prefix}sem_keyword WHERE varname_id=".$varname_id." AND row_number=".$row_number.";");
        return $sem_keyword[0]->keyword;
    }
    
    public static function get_all_sem_varname()
    {
        global $wpdb;
        $wpdb->get_results(" SELECT * FROM {$wpdb->prefix}sem_varname ");
        return $wpdb->num_rows;
    }
    
    public static function get_all_sem_keyword()
    {
        global $wpdb;
        return $wpdb->get_var(" SELECT COUNT(DISTINCT row_number) FROM {$wpdb->prefix}sem_keyword ");
        
    }

    public function save_keywords()
    {  
        global $wpdb;
        if(isset($_POST['keyword'])){
            foreach ($_POST['keyword'] as $row_number => $values_in_column) {
                foreach ($values_in_column as $varname_id => $keyword) {
                    self::add_keyword($wpdb, $row_number, $varname_id, $keyword); 
                }
            }
        }
    }

    public static function add_keyword($wpdb, $row_number, $varname_id, $keyword)
    {
        if($wpdb->query("SELECT * FROM {$wpdb->prefix}sem_keyword WHERE row_number=".$row_number." AND varname_id=".$varname_id.";")){
            $q = "UPDATE {$wpdb->prefix}sem_keyword SET keyword='".$keyword."' WHERE row_number=".$row_number." AND varname_id=".$varname_id.";";
            $wpdb->query($q);
        }else{
            $q = "INSERT INTO {$wpdb->prefix}sem_keyword (varname_id, row_number, keyword) VALUES (".$varname_id.", ".$row_number.", '".$keyword."');";
            $wpdb->query($q);
        }
    }
    
    public static function get_varname_by_id($id){
        global $wpdb;
        $wpdb->get_results("SELECT varname FROM {$wpdb->prefix}sem_varname where varname_id = " . $id);
    }
    
    public static function delete_keyword_by_row_number()
    {
        global $wpdb;
        if(isset($_POST['lp_item'])){
            $row_number = implode(',',$_POST['lp_item']);
            $affected_row = $wpdb->query(" DELETE FROM {$wpdb->prefix}sem_keyword where row_number IN (".$row_number.")");
        }
        // pour ajax
        if(wp_doing_ajax()) {
            echo $affected_row;
            wp_die();
        }
        else return $affected_row;
    }
}
