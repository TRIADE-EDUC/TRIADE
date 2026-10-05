<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cms_module_common_selector_authority_random_in_cart.class.php,v 1.2 2019/09/03 15:33:13 arenou Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class cms_module_common_selector_authority_random_in_cart extends cms_module_common_selector{
	
	public function __construct($id=0){
		parent::__construct($id);
		$this->once_sub_selector=true;
	}
	
	public function get_sub_selectors(){
	    return [
	        "cms_module_common_selector_generic_cart_authorities"
	    ];
	}
	
	public function get_value(){
	    if(!$this->value){
	        $this->value = 0;
	        $cart_selector = new cms_module_common_selector_generic_cart_authorities($this->get_sub_selector_id("cms_module_common_selector_generic_cart_authorities"));
	        $idCart= $cart_selector->get_value();
	        if($idCart[0]!= 0){
	            $query = "select object_id from authorities_caddie_content where caddie_id=".$idCart[0];
	            $result = pmb_mysql_query($query);
	            if(pmb_mysql_num_rows($result)){
	                $authorities = [];
	                while($row = pmb_mysql_fetch_object($result)){
	                    $authorities[] = $row->object_id;
	                }
	                $this->value = $authorities[rand(0,count($authorities)-1)];
	            }
	        }
	    }
	    return $this->value;
	}
}