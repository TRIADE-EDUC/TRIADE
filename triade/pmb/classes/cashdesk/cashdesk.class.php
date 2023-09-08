<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: cashdesk.class.php,v 1.18 2019-06-13 15:26:51 btafforeau Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once($include_path."/templates/cashdesk/cashdesk.tpl.php");
require_once("$base_path/classes/comptes.class.php");
require_once($class_path."/transaction/transaction.class.php");
require_once($class_path."/users.class.php");

class cashdesk  {
	public $id = 0;				// identifiant de la caisse
	public $name = "";				// Libellé de la caisse
	public $affectation = array();	// affectation de la caisse  key=>id location, val => id section
	public $autorisations = "";	// utilisateurs autorisés à utiliser la caisse
	public $transactypes = "";		// Types de transaction  autorisés à la caisse
	public $cashbox = 0;
	
	public function __construct($id=0){		
	    $this->id = intval($id);
	    $this->fetch_data();		
	}
	
	protected function fetch_data(){		
		$this->affectation=array();
		$this->name="";
		if(!$this->id)	return false;
		
		// les infos générales...	
		$rqt = "select * from cashdesk where cashdesk_id ='".$this->id."'";
		$res = pmb_mysql_query($rqt);
		if(pmb_mysql_num_rows($res)){
			$row = pmb_mysql_fetch_object($res);
			$this->id = $row->cashdesk_id;
			$this->name = $row->cashdesk_name;
			$this->autorisations=$row->cashdesk_autorisations;
			$this->transactypes=$row->cashdesk_transactypes;
			$this->cashbox=$row->cashdesk_cashbox;
			
			$rqt = "select * from cashdesk_locations where cashdesk_loc_cashdesk_num ='".$this->id."'";
			$res_loc = pmb_mysql_query($rqt);
			if(pmb_mysql_num_rows($res_loc)){
				while($row_loc=pmb_mysql_fetch_object($res_loc)){
					// les localisations de la caisse				
					$this->affectation[$row_loc->cashdesk_loc_num]=array();				
					$rqt = "select * from cashdesk_sections where cashdesk_section_cashdesk_num ='".$this->id."'";
					
					$res_section = pmb_mysql_query($rqt);
					if(pmb_mysql_num_rows($res_section)){						
						while($row_section= pmb_mysql_fetch_object($res_section)){
							// les sections spécifique de la caisse
							$this->affectation[$row_loc->cashdesk_loc_num][$row_section->cashdesk_section_num]=1;			
						}			
					}else{
						// toutes les sections la localisation
					}			
				}					
			}					
		}else{
			// toutes les localisations
		}
	}
	
	public function get_form(){
		global $msg, $charset;
		global $pmb_droits_explr_localises;
		global $cashdesk_form,$deflt_docs_location;
		
		$form=$cashdesk_form;
		
		if($this->id){
			$titre=$msg["cashdesk_form_titre_edit"];
			$form = str_replace('!!supprimer!!', "<input type='button' class='bouton' value=' ".$msg["cashdesk_form_delete"]." ' onClick=\"if(confirm('".$msg["cashdesk_form_delete_question"]."'))
					document.location = './admin.php?categ=finance&sub=cashdesk&action=delete&id=!!id!!'\" />", $form);
		}else{
			$titre=$msg["cashdesk_form_titre_add"];
			$form = str_replace('!!supprimer!!', "", $form);
		}
		
		$form = str_replace('!!titre!!', $titre, $form);
		$form = str_replace('!!name!!', htmlentities($this->name,ENT_QUOTES, $charset), $form);
		
		$form = str_replace('!!autorisations_users!!', users::get_form_autorisations($this->autorisations,1), $form);
		$form = str_replace('!!transactypes!!', $this->get_form_transactypes($this->transactypes), $form);
		
		$form = str_replace('!!action!!', "./admin.php?categ=finance&sub=cashdesk&action=save&id=!!id!!", $form);
		$form = str_replace('!!location_section!!', $this->get_form_location(), $form);		
		
		if($this->cashbox)$cashbox_check=" checked "; else $cashbox_check="";
		$form = str_replace('!!cashbox_checked!!', $cashbox_check, $form);
		
		$form = str_replace('!!id!!', $this->id, $form);
		return $form; 
	}
	
	public function get_form_transactypes ($param_autorisations="1") {
		global $dbh;
		global $msg;
	
		$requete_users = "SELECT transactype_id, transactype_name FROM transactype order by transactype_name ";
		$res = pmb_mysql_query($requete_users, $dbh);
		$all_tansactions=array();
		while (list($transactype_id,$transactype_name)=pmb_mysql_fetch_row($res)) {
			$all_tansactions[]=array($transactype_id,$transactype_name);
		}	
	
		$autorisations_donnees=explode(" ",$param_autorisations);
	
		for ($i=0 ; $i<count($all_tansactions) ; $i++) {
			if (array_search ($all_tansactions[$i][0], $autorisations_donnees)!==FALSE) $autorisation[$i][0]=1;
			else $autorisation[$i][0]=0;
			$autorisation[$i][1]= $all_tansactions[$i][0];
			$autorisation[$i][2]= $all_tansactions[$i][1];
		}
		$form="";
		$id_check_list='';
		foreach ($autorisation as $row_number => $row_data) {
			$id_check="auto_transac".$row_data[1];
			if($id_check_list)$id_check_list.='|';
			$id_check_list.=$id_check;
//			if ($row_data[1]==1) $form.="<span class='usercheckbox'><input type='checkbox' name='autorisation_transactypes[]' id='$id_check' value='".$row_data[1]."' checked class='checkbox' readonly /><label for='$id_check' class='normlabel'>&nbsp;".$row_data[2]."</label></span>&nbsp;";
			if ($row_data[0]) $form.="<span class='usercheckbox'><input type='checkbox' name='autorisation_transactypes[]' id='$id_check' value='".$row_data[1]."' checked class='checkbox' /><label for='$id_check' class='normlabel'>&nbsp;".$row_data[2]."</label></span>&nbsp;";
			else $form.="<span class='usercheckbox'><input type='checkbox' name='autorisation_transactypes[]' id='$id_check' value='".$row_data[1]."' class='checkbox' /><label for='$id_check' class='normlabel'>&nbsp;".$row_data[2]."</label></span>&nbsp;";
		}
		$form.="<input type='hidden' id='auto_id_list_transactypes' name='auto_id_list_transactypes' value='$id_check_list' >";
		return $form;
	}
	
	public function get_form_location(){
		global $dbh, $msg;
		global $charset;		
		global $deflt_docs_section;
		global $deflt_docs_location;
		
		if (!isset($this->section_id) || !$this->section_id) $this->section_id=$deflt_docs_section ;
		if (!isset($this->location_id) || !$this->location_id) $this->location_id=$deflt_docs_location;
		
		$rqtloc = "SELECT * FROM docs_location order by location_libelle";
		$resloc = pmb_mysql_query($rqtloc, $dbh);		
		$form = "<table>
		<tr>
			<th>".$msg["cashdesk_form_locations"]."</th>
			<th>".$msg["cashdesk_form_sections"]."</th>
		</tr>
		";
		$parity = 0;
		while (($loc=pmb_mysql_fetch_object($resloc))) {
			if ($parity++ % 2)	$pair_impair = "even"; else $pair_impair = "odd";
			if(isset($this->affectation[$loc->idlocation]) && is_array($this->affectation[$loc->idlocation]))$checked=" checked='checked' "; else $checked="";
			$form.="<tr class='$pair_impair' >";
			$form.="<td>";
			$form.="<input class='checkbox' type='checkbox' $checked value='".$loc->idlocation."' name='f_locations[]'>".htmlentities($loc->location_libelle,ENT_QUOTES, $charset)."<br/>";
			$form.="</td>";
			$requete = "SELECT idsection, section_libelle FROM docs_section, docsloc_section where idsection=num_section and num_location='$loc->idlocation' order by section_libelle";
			$result = pmb_mysql_query($requete, $dbh);
			$form.="<td>";
			if ( pmb_mysql_num_rows($result)) {
				while ($section = pmb_mysql_fetch_object($result)) {
					if(isset($this->affectation[$loc->idlocation][$section->idsection]) && $this->affectation[$loc->idlocation][$section->idsection]) {
						$checked=" checked='checked' ";
					} else {
						$checked="";
					}
					$form.="<input class='checkbox' type='checkbox' $checked value='".$section->idsection."' name='f_sections_".$loc->idlocation."[]'>".$section->section_libelle."<br/>  ";						
				}
			}
			$form.="</td>";
			$form.="</tr>";
		}		
		$form.="</table>";
		return $form;		
	}
	
	public function summarize( $date_begin="",$date_end="", $transactype=0,$encaissement=0 ){
		global $pmb_gestion_abonnement, $pmb_gestion_amende, $pmb_gestion_tarif_prets;
		global $msg;
		
		$transactype = intval($transactype);
		$transactype_filter = $encaissement_filter = $date_begin_filter = $date_end_filter = '';
		if($transactype){
			$transactype_filter= " and transactype_num = $transactype ";
		}
		if($encaissement){
			$encaissement_filter= " and encaissement = $encaissement ";
		}
		if($date_begin){
			$date_begin_filter= " and date_effective >= '$date_begin' ";
		}
		if($date_end){
			$date_end_filter= " and date_effective <= '$date_end' ";
		}
		$all_filter = $transactype_filter.$encaissement_filter.$date_begin_filter.$date_end_filter;
		
		$requete="select *, SUM(montant)as cash from transactions, transactype where cashdesk_num=".$this->id." and transactype_num=transactype_id  $all_filter
			group by transactype_num";
		
		$i=0;
		$data=array();
		$res=pmb_mysql_query($requete);
		if(pmb_mysql_num_rows($res)){
			while ($row = pmb_mysql_fetch_object($res)){
		
				$data[$i]["id"]=$row->transactype_num;
				$data[$i]["name"]=$row->transactype_name;
				$data[$i]["montant"]=$row->cash;
				$data[$i]["unit_price"]=$row->transactype_unit_price;				
				
				$req="select SUM(montant)as cash from transactions where cashdesk_num=".$this->id." and transactype_num=".$row->transactype_num." and realisee=0 $all_filter";				
				$res_sum=pmb_mysql_query($req);
				if($row_sum= pmb_mysql_fetch_object($res_sum))	$data[$i]["realisee_no"]=$row_sum->cash;
				else $data[$i]["realisee_no"]="";
				
				$req="select SUM(montant)as cash from transactions where cashdesk_num=".$this->id." and transactype_num=".$row->transactype_num." and realisee=1 $all_filter";				
				$res_sum=pmb_mysql_query($req);
				if($row_sum= pmb_mysql_fetch_object($res_sum))	$data[$i]["realisee"]=$row_sum->cash;
				else $data[$i]["realisee"]="";
				
				$req="select SUM(montant)as cash from transactions where cashdesk_num=".$this->id." and transactype_num=".$row->transactype_num." and encaissement=0 
				and transacash_num=0 $all_filter";
				$res_sum=pmb_mysql_query($req);
				if($row_sum= pmb_mysql_fetch_object($res_sum))	$data[$i]["encaissement_no"]=$row_sum->cash;
				else $data[$i]["encaissement_no"]="";
				
				$req="select SUM(montant)as cash from transactions where cashdesk_num=".$this->id." and transactype_num=".$row->transactype_num." 
				and transacash_num>0 $all_filter";		
				$res_sum=pmb_mysql_query($req);
				if($row_sum= pmb_mysql_fetch_object($res_sum))	{
				    $data[$i]["encaissement"]=$row_sum->cash;
				    $data[$i]["encaissement_payment_method"] = $this->get_payment_method(0, $all_filter, $row->transactype_num);				    
				}
				else $data[$i]["encaissement"]="";
				
				$i++;
			}
						
		}	
		
		if ($pmb_gestion_abonnement) {
			$aff_flag=0;
						
			$compte["id"]="cpt_1";
			$compte["name"]=$msg["finance_solde_abt"];
			$compte["montant"]='';
			$compte["unit_price"]='';
			$compte["realisee_no"]='';

			// Non Validée
			$requete="select SUM(montant)as cash from transactions,  comptes where cashdesk_num=".$this->id."
			and encaissement=0 and realisee=0 and type_compte_id=1 and id_compte =compte_id $all_filter
			";
			$res_sum=pmb_mysql_query($requete);
			if($row_sum= pmb_mysql_fetch_object($res_sum)){
			if($row_sum->cash)$aff_flag=1;
			$compte["realisee_no"]=$row_sum->cash;
			}else $compte["realisee_no"]="";

			//Validée
			$requete="select SUM(montant)as cash from transactions,  comptes where cashdesk_num=".$this->id."
			and encaissement=0 and realisee=1 and type_compte_id=1 and id_compte =compte_id $all_filter
			";
			$res_sum=pmb_mysql_query($requete);
			if($row_sum= pmb_mysql_fetch_object($res_sum)){
			if($row_sum->cash)$aff_flag=1;
			$compte["realisee"]=$row_sum->cash;
			}else $compte["realisee"]="";
			
			//Ecaissé
			$requete="select SUM(montant)as cash from transactions,  comptes where cashdesk_num=".$this->id."
			and encaissement=1 and type_compte_id=1 and id_compte =compte_id $all_filter
			";
			$res_sum=pmb_mysql_query($requete);
			if($row_sum= pmb_mysql_fetch_object($res_sum)){
				if($row_sum->cash)$aff_flag=1;	
				$compte["encaissement"]=$row_sum->cash;
				$compte["encaissement_payment_method"] = $this->get_payment_method(1, $all_filter);
			}else $compte["encaissement"]="";
			
			$compte["encaissement_no"] = -($compte["encaissement"] - $compte["realisee"]);
			if(!$compte["encaissement_no"])	$compte["encaissement_no"]='';
			
			if($aff_flag){
				$data[$i]=$compte;
				$i++;
			}
		}
		
		if ($pmb_gestion_amende) {
			$aff_flag=0;
			
			$compte["id"]="cpt_2";
			$compte["name"]=$msg["finance_solde_amende"];
			$compte["montant"]='';
			$compte["unit_price"]='';
			
			// Non Validée
			$requete="select SUM(montant)as cash from transactions,  comptes where cashdesk_num=".$this->id."
			and encaissement=0 and realisee=0 and type_compte_id=2 and id_compte =compte_id $all_filter
			";
			$res_sum=pmb_mysql_query($requete);
			if($row_sum= pmb_mysql_fetch_object($res_sum)){
			if($row_sum->cash)$aff_flag=1;
			$compte["realisee_no"]=$row_sum->cash;
			}else $compte["realisee_no"]="";

			//Validée
			$requete="select SUM(montant)as cash from transactions,  comptes where cashdesk_num=".$this->id."
			and encaissement=0 and realisee=1 and type_compte_id=2 and id_compte =compte_id $all_filter
			";
			$res_sum=pmb_mysql_query($requete);
			if($row_sum= pmb_mysql_fetch_object($res_sum)){
			if($row_sum->cash)$aff_flag=1;
			$compte["realisee"]=$row_sum->cash;
			}else $compte["realisee"]="";
			
			//Ecaissé
			$requete="select SUM(montant)as cash from transactions,  comptes where cashdesk_num=".$this->id."
			and encaissement=1 and type_compte_id=2 and id_compte =compte_id $all_filter
			";
			$res_sum=pmb_mysql_query($requete);
			if($row_sum= pmb_mysql_fetch_object($res_sum)){
				if($row_sum->cash)$aff_flag=1;	
				$compte["encaissement"]=$row_sum->cash;
				$compte["encaissement_payment_method"] = $this->get_payment_method(2, $all_filter);
			}else $compte["encaissement"]="";
			
			$compte["encaissement_no"] = -($compte["encaissement"] - $compte["realisee"]);
			if(!$compte["encaissement_no"])	$compte["encaissement_no"]='';
			
			if($aff_flag){
				$data[$i]=$compte;
				$i++;
			}
		}
		
		if ($pmb_gestion_tarif_prets) {
			$aff_flag=0;
						
			$compte["id"]="cpt_3";
			$compte["name"]=$msg["finance_solde_pret"];
			$compte["montant"]='';
			$compte["unit_price"]='';

			// Non Validée
			$requete="select SUM(montant)as cash from transactions,  comptes where cashdesk_num=".$this->id."
			and encaissement=0 and realisee=0 and type_compte_id=3 and id_compte =compte_id $all_filter
			";
			$res_sum=pmb_mysql_query($requete);
			if($row_sum= pmb_mysql_fetch_object($res_sum)){
			if($row_sum->cash)$aff_flag=1;
			$compte["realisee_no"]=$row_sum->cash;
			}else $compte["realisee_no"]="";

			//Validée
			$requete="select SUM(montant)as cash from transactions,  comptes where cashdesk_num=".$this->id."
			and encaissement=0 and realisee=1 and type_compte_id=3 and id_compte =compte_id $all_filter
			";
			$res_sum=pmb_mysql_query($requete);
			if($row_sum= pmb_mysql_fetch_object($res_sum)){
			if($row_sum->cash)$aff_flag=1;
			$compte["realisee"]=$row_sum->cash;
			}else $compte["realisee"]="";			
			
			//Ecaissé
			$requete="select SUM(montant)as cash from transactions,  comptes where cashdesk_num=".$this->id."
			and encaissement=1 and type_compte_id=3 and id_compte =compte_id $all_filter
			";
			$res_sum=pmb_mysql_query($requete);
			if($row_sum= pmb_mysql_fetch_object($res_sum)){
				if($row_sum->cash)$aff_flag=1;	
				$compte["encaissement"]=$row_sum->cash;
				$compte["encaissement_payment_method"] = $this->get_payment_method(3, $all_filter);
			}else $compte["encaissement"]="";
		
			$compte["encaissement_no"] = -($compte["encaissement"] - $compte["realisee"]);
			if(!$compte["encaissement_no"])	$compte["encaissement_no"]='';
			
			if($aff_flag){
				$data[$i]=$compte;
				$i++;
			}		
			
		}
		return $data;
	}
	
	public function get_payment_method($type_compte_id, $all_filter='', $transactype_num=0) {
	    
	    $data = array();
	    $requete = "
                SELECT SUM(montant)as cash, transaction_payment_method_num, transaction_payment_method_name
                FROM transactions
                LEFT JOIN transaction_payment_methods on transaction_payment_method_id=transaction_payment_method_num ";
	    if ($transactype_num) {
	       $requete.= "
                WHERE cashdesk_num=" . $this->id . " and transactype_num=".$transactype_num." and transacash_num>0 ";	   
	    } else {
	       $requete.= "
                JOIN comptes ON id_compte=compte_id and type_compte_id=" . $type_compte_id . "
                WHERE cashdesk_num=" . $this->id . " and encaissement=1 ";
	    }
	    $requete.= $all_filter . " group by transaction_payment_method_num";	   
	    $res_sum = pmb_mysql_query($requete);
	    while ($row_sum = pmb_mysql_fetch_assoc($res_sum)) {
	        $data[] = $row_sum;
	    }   
	    return $data;
	}
	
	public function get_from_form(){		
		global $f_name;
		global $f_locations;
		global $id;
		global $autorisations;
		global $autorisation_transactypes;		
		global $f_cashbox;		
		
		$this->id = (int) $id;
		$this->name=stripslashes($f_name);		
		$this->cashbox=stripslashes($f_cashbox);		
		$this->affectation=array();
		if(is_array($f_locations))
		foreach ($f_locations as $id_location){
			$id_location+=0;
			$this->affectation[$id_location]=array();
			$section_name="f_sections_".$id_location;;
			global ${$section_name};
			$sections= ${$section_name};
			
			if(is_array($sections))
			foreach ($sections as $id_section){
				$id_section+=0;
				$this->affectation[$id_location][$id_section]=1;				
			}
		}
		if (is_array($autorisation_transactypes)) $this->transactypes=implode(" ",$autorisation_transactypes);
		else $this->transactypes="";
		if (is_array($autorisations)) $this->autorisations=implode(" ",$autorisations);
		else $this->autorisations="";
	}
	
	public function check_delete(){
		// tbd
		return 1;
	}
	
	public function delete(){
		global $dbh;
		
		$rqt = "delete FROM cashdesk_locations WHERE cashdesk_loc_cashdesk_num ='".$this->id."'";
		pmb_mysql_query($rqt, $dbh);
		$rqt = "delete FROM cashdesk_sections WHERE cashdesk_section_cashdesk_num ='".$this->id."'";
		pmb_mysql_query($rqt, $dbh);		
		$rqt = "delete FROM cashdesk WHERE cashdesk_id ='".$this->id."'";
		pmb_mysql_query($rqt, $dbh);
		
		$this->id=0;
	}
	
	public function save(){
		global $dbh;
		
		if($this->id){
			$rqt = "delete FROM cashdesk_locations WHERE cashdesk_loc_cashdesk_num ='".$this->id."'";
			pmb_mysql_query($rqt, $dbh);
			$rqt = "delete FROM cashdesk_sections WHERE cashdesk_section_cashdesk_num ='".$this->id."'";
			pmb_mysql_query($rqt, $dbh);
			
			$save = "update ";
			$clause = "where cashdesk_id = '".$this->id."'";
		}else{
			$save = "insert into ";						
		}
		$save.=" cashdesk set cashdesk_name='". addslashes( $this->name). "', cashdesk_autorisations='".$this->autorisations."' , cashdesk_transactypes='".$this->transactypes."'  , cashdesk_cashbox='".$this->cashbox."' $clause";
		pmb_mysql_query($save,$dbh);		
		if(!$this->id){
			$this->id=pmb_mysql_insert_id();
		}
				
		foreach($this->affectation as $id_location => $sections){
			$rqt = "insert into cashdesk_locations set cashdesk_loc_cashdesk_num ='".$this->id."', cashdesk_loc_num ='".$id_location."' ";
			pmb_mysql_query($rqt,$dbh);
			foreach($sections as $id_section => $val){					
				$rqt = "insert into cashdesk_sections set cashdesk_section_cashdesk_num ='".$this->id."', cashdesk_section_num ='".$id_section."' ";			
				pmb_mysql_query($rqt,$dbh);
			}
		}				
		$this->fetch_data();
	}
	
	public function proceed(){
		global $action,$msg;
		
		switch($action) {
			case 'edit':
					print $this->get_form();
				break;
			case 'save':
					$this->get_from_form();
					$this->save();
					print "<script type='text/javascript'>window.location='./admin.php?categ=finance&sub=cashdesk'</script>";
				break;
			case 'delete':
					if($this->check_delete()){
						$this->delete();
						print "<script type='text/javascript'>window.location='./admin.php?categ=finance&sub=cashdesk'</script>";
					}else{
						print "<script type='text/javascript'>alert(".$msg["cashdesk_form_delete_no"].");window.location='./admin.php?categ=finance&sub=cashdesk'</script>";
					}
				break;	
			default:
				break;
		}
	}
}