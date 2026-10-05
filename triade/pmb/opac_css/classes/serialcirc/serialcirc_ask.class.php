<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc_ask.class.php,v 1.1 2023/12/21 10:34:44 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class serialcirc_ask {	

    public $id=0;
    
    public $num_serial;
    
    public $num_serialcirc;
    
    public $type;
    
    public $status;
    
    public $date;
    
    public $comment;
    
    public $num_empr;
    
    public $serialcirc_diff;
	
	public function __construct($id) {
		$this->id=intval($id);		
		$this->fetch_data(); 
	}
	
	public function fetch_data() {
		$query = "select * from serialcirc_ask where id_serialcirc_ask=".$this->id;
		$result = pmb_mysql_query($query);	
		if (pmb_mysql_num_rows($result)) {
		    $row = pmb_mysql_fetch_object($result);
		    $this->num_serial = $row->num_serialcirc_ask_perio;
		    $this->num_serialcirc = $row->num_serialcirc_ask_serialcirc;
		    $this->type = $row->serialcirc_ask_type;
		    $this->status = $row->serialcirc_ask_statut;
		    $this->date = $row->serialcirc_ask_date;
		    $this->comment = $row->serialcirc_ask_comment;
		    $this->num_empr = $row->num_serialcirc_ask_empr;
				
		    if(!$this->num_serial){					
		        $this->serialcirc_diff = new serialcirc_diff($this->num_serialcirc);
		        $this->num_serial = $this->serialcirc_diff->id_perio;
				
			}
		}	
	}
}