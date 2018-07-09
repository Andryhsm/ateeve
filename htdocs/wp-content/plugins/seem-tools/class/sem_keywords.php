<?php

class Sem_Keyword
{
    private $keyword_id;
    private $varname_id;
    private $row_number;
    private $keyword;
  
    public static function get_keyword($varname_id, $row_number)
    {
        global $wpdb;
        $keyword = $wpdb->query("SELECT keyword FROM {$wpdb->prefix}sem_keyword WHERE varname_id=".$varname_id." AND row_number=".$row_number.";");
        echo $keyword;
    }

    public function save_keywords()
    {  
        if(isset($_POST['keyword'])){
            global $wpdb;
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
            var_dump($q);
        }else{
            $q = "INSERT INTO {$wpdb->prefix}sem_keyword (varname_id, row_number, keyword) VALUES (".$varname_id.", ".$row_number.", '".$keyword."');";
            $wpdb->query($q);
        }
    }
}
