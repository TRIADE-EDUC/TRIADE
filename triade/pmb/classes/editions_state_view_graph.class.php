<?php
// +-------------------------------------------------+
// © 2002-2012 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: editions_state_view_graph.class.php,v 1.2 2023/03/07 07:36:19 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/editions_state_view.class.php");
require_once($class_path."/editions_state_view_tcd.class.php");


class editions_state_view_graph extends editions_state_view {
	public $datas_graph;
	public $valid_datas = false;
	public $datas_tcd =array(
		'values' => array(),
		'cols' => array()
	);
	
	public function get_datas(){
		global $tcd_cols;
		global $graph;
		if($tcd_cols){		
			$value = explode("_",$tcd_cols);
			for($i=1 ; $i<count($this->datas) ; $i++){
				if(!in_array($this->datas[$i][$value[1]],$this->datas_tcd['cols'])){
					$this->datas['cols'][] = $this->datas[$i][$value[1]];
				}
				$this->datas['values'][$this->datas[$i][$value[0]]][$this->datas[$i][$value[1]]]++;
			}
			if($graph){
				$this->datas['graph'] = $graph;
				$this->valid_datas = true;
			}
		}
		return $this->datas_tcd;
	}
	
	public function get_form(){
		global $charset,$msg;
		global $tcd_cols;
		// quel tablau on prend?
				
		$form = "
		<form action='' method='post' name='editions_state_graph'>
			<h3>données à mettre en forme</h3>
			<div class='row'>
				<div class='colonne3'>
					<label for='graph'>".htmlentities($msg['editions_state_graph_form_label'],ENT_QUOTES,$charset)."</label>
				</div>
				<div class='colonne_suite'>
					<select name='graph'>
						<option value='0'>".htmlentities($msg['editions_state_graph_form_choice'],ENT_QUOTES,$charset)."</option>
						<option value='histo'>".htmlentities($msg['editions_state_graph_form_histo'],ENT_QUOTES,$charset)."</option>
					</select>
				</div>
			</div>
			<div class='row'>
				<div class='colonne3'>
					<label for='tcd'>".htmlentities($msg["editions_state_view_grah_tcd"],ENT_QUOTES,$charset)."</label>
				</div>
				<div class='colonne_suite'>
					<select name='tcd_cols' onchange='document.forms.editions_state_graph.submit()'>
						<option value='0'>".htmlentities($msg["editions_state_tcd_choice"],ENT_QUOTES,$charset)."</option>";
		for($i=0 ; $i<count($this->datas[0]) ; $i++){
			for($j=0 ; $j<count($this->datas[0]) ; $j++){
				if($i!=$j){
				$form.="
						<option value='".$i."_".$j."'".($tcd_cols == $i."_".$j ? " selected='selected'" : "").">".htmlentities($this->datas[0][$i]." / ".$this->datas[0][$j],ENT_QUOTES,$charset)."</option>";
				}
			}
		}
		$form.= "
					</select>
				</div>
			</div>
			<div class='row'></div>
		</form>";
		return $form;
	}
	
	
	
	//un simple tableau pour la classe générique...
	public function show(){
		$html = $this->get_form();
		$this->get_datas();
		if($this->valid_datas){
			$html.="
			<script type='text/javascript'>
				require(['dojox/charting/Chart','dojox/charting/themes/Claro'],function(Chart,theme){
				
				});
			</script>";
		}
		return $html;
	}
}