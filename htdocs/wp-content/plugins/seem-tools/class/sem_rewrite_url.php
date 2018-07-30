<?php
class Sem_Rewrite_Url {
    public function __construct()
    {
        include_once plugin_dir_path( __FILE__ ).'sem_keywords.php';
        add_action('init', array($this,'custom_rewrite_rule'));
        //test 
        add_filter( 'generate_rewrite_rules', function ( $wp_rewrite ) {
		    $wp_rewrite->rules = array_merge(
		        ['my-custom-url' => 'index.php?custom=1'],
		        $wp_rewrite->rules
		    );
		} );
		add_filter( 'redirect_canonical','custom_disable_redirect_canonical' );
    }
    
    public function custom_rewrite_rule($request) {
        if(get_option('sem_active') == 'on')
        {
	        $pattern = '/variable[0-9]/';
			$text = get_option('modal_url');
			$matches = preg_match_all($pattern, $text, $array);
	
			$p = get_page(get_option("page_dropdown"));

			$number_row = Sem_Keyword::get_all_sem_keyword();
			
			add_rewrite_tag('%row%','([^&]+)');

			for($j=1;$j<=$number_row;$j++)
			{
				$remplacements = array();
				foreach($array[0] as $key=>$value)
				{
					$remplacements[$key] = Sem_Keyword::get_keyword($j, $value[strlen($value)-1]);
				}
				$url_primary = get_option('primary_url').'/'.str_replace($array[0], $remplacements, $text);
				
				add_rewrite_rule($url_primary,'index.php?page_id='.get_option("page_dropdown").'&page_name='.$p->post_name.'&row='.$j,'top');
			}
        }	
        flush_rewrite_rules();
    }
    
    function custom_disable_redirect_canonical( $redirect_url ){
    	$url = get_permalink(get_option("page_dropdown"));
	    if ( home_url($url) ) $redirect_url = false;
	    return $redirect_url;
	}
    
}