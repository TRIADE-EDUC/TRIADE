<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: editions_state_view.class.php,v 1.5 2023/05/04 14:15:47 rtigero Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($class_path."/spreadsheetPMB.class.php");

class editions_state_view {
	public $datas =array();		//tableau de données
	public $editions_state_id;
	public $my_param=array(); //Paramètre propre à la vue
	
	public function __construct($datas,$id,$param=array()){
		$this->datas = $datas;
		$this->editions_state_id = $id;
		$this->my_param = $param;
	}
	
	public function get_datas(){
		return $this->datas;
	}
	
	public function get_param(){
		return $this->my_param;
	}
	
	//un simple tableau pour la classe générique...
	public function show(){
		global $charset,$msg,$base_path;
		global $javascript_path;
		global $show_all;
		$html = "
		<script type='text/javascript' src='".$javascript_path."/sorttable.js'></script>
		<div class='row'>
				<label>".$msg['editions_state_nb_rows']."</label>
				<span>".(count($this->datas)-1)."</span>		
		</div>
		
		<div class='row'>";
		$html.="
		<table class='sortable'>";
		for($i=0 ; $i<count($this->datas) ; $i++){
			$html.="
				<tr>";
			for($j=0 ; $j<count($this->datas[$i]) ; $j++){
				$html.="
					<".($i==0 ? "th" : 'td').">
					".htmlentities($this->datas[$i][$j] ?? "",ENT_QUOTES,$charset)."
					</".($i==0 ? "th" : 'td').">";
			}
			$html.="
				</tr>";
			if(!$show_all && ($i == 50)){
				$html.="<tr  class='sortbottom' ><td colspan=\"".count($this->datas[$i])."\" ><a onclick='test_form(\"tab\",\"show_all\");'><b>".$msg["editions_state_view_tab_all"]."</b></a></td></tr>";
				break;
			}
		}
		$html.="
			</table>
		</div>
		<div class='row'>
			<input type='button' class='bouton' value='".htmlentities($msg["editions_state_view_export_excel"],ENT_QUOTES,$charset)."' onclick=\"test_form('tab','edit');\" />
		</div>";
		return $html;
	}
	
	public function render_xls_file($name="state"){
	    $worksheet = new spreadsheetPMB();
		for($i=0 ; $i<count($this->datas) ; $i++){
			for($j=0 ; $j<count($this->datas[$i]) ; $j++){
				$worksheet->write($i,$j,$this->datas[$i][$j]);
			}
		}
		$worksheet->download($name.'.xls');	
	}
}