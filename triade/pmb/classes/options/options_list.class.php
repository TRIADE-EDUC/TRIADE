<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: options_list.class.php,v 1.7 2024/02/13 16:28:31 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once ($class_path."/options/options.class.php");

/**
 * TODO : à câbler sur la refonte
 * @author dgoron
 *
 */
class options_list extends options {
    
    protected $created_primary_key = [];
    
	public function init_default_parameters() {
		parent::init_default_parameters();
		$this->parameters['MULTIPLE'][0]['value'] = '';
		$this->parameters['AUTORITE'][0]['value'] = '';
		$this->parameters['CHECKBOX'][0]['value'] = '';
		$this->parameters['CHECKBOX_NB_ON_LINE'][0]['value'] = '';
		$this->parameters['NUM_AUTO'][0]['value'] = '';
		$this->parameters['UNSELECT_ITEM'][0]['VALUE'] = '';
		$this->parameters['UNSELECT_ITEM'][0]['value'] = '';
		$this->parameters['DEFAULT_VALUE'][0]['value'] = '';
	}
	
	public function init_additional_parameters() {
	    global $_custom_prefixe_;
	    
	    parent::init_additional_parameters();
    	//Récupération des valeurs de la liste
    	if ($this->idchamp) {
    	    //Par sécurité..on verifie l'existence de la clé primaire
    	    if($this->is_created_primary_key()) {
    	        $requete="select id_".$_custom_prefixe_."_custom_list, ".$_custom_prefixe_."_custom_list_value, ".$_custom_prefixe_."_custom_list_lib, ordre from ".$_custom_prefixe_."_custom_lists where ".$_custom_prefixe_."_custom_champ=".$this->idchamp." order by ordre";
    	    } else {
    	        $requete="select ".$_custom_prefixe_."_custom_list_value, ".$_custom_prefixe_."_custom_list_lib, ordre from ".$_custom_prefixe_."_custom_lists where ".$_custom_prefixe_."_custom_champ=".$this->idchamp." order by ordre";
    	    }
    	    $resultat=pmb_mysql_query($requete);
    	    if (pmb_mysql_num_rows($resultat)) {
    	        $i=0;
    	        while (($r=pmb_mysql_fetch_array($resultat))) {
    	            if($this->is_created_primary_key()) {
                        $this->additional_parameters['ITEM_ID'][$i] = $r["id_".$_custom_prefixe_."_custom_list"];
    	            }
    	            $this->additional_parameters['ITEM'][$i] = $r[$_custom_prefixe_."_custom_list_lib"];
    	            $this->additional_parameters['VALUE'][$i] = $r[$_custom_prefixe_."_custom_list_value"];
    	            $this->additional_parameters['ORDRE'][$i] = $r["ordre"];
    	            $i++;
    	        }
    	    }
    	}
	}
	
	protected function is_created_primary_key() {
	    global $_custom_prefixe_;
	    
	    if(!isset($this->created_primary_key[$_custom_prefixe_])) {
	        $query = "SHOW COLUMNS FROM ".addslashes($_custom_prefixe_)."_custom_lists LIKE 'id_".addslashes($_custom_prefixe_)."_custom_list'";
	        $result = pmb_mysql_query($query);
	        $this->created_primary_key[$_custom_prefixe_] = pmb_mysql_num_rows($result);
	    }
	    return $this->created_primary_key[$_custom_prefixe_];
	}
    	
	protected function get_hidden_fields_form() {
		global $_custom_prefixe_;
		return "<input type='hidden' name='first' value='0' />
				<input type='hidden' name='name' value='".$this->name."' />
				<input type='hidden' name='type' value='".$this->type."' />
				<input type='hidden' name='idchamp' value='".$this->idchamp."' />
				<input type='hidden' name='_custom_prefixe_' value='".$_custom_prefixe_."' />";
	}
	
	public function get_content_form() {
    	global $msg, $charset;
    	
		$content_form = $this->get_line_content_form($msg["procs_options_liste_multi"], 'MULTIPLE', 'checkbox', 'yes');
		$content_form .= $this->get_line_content_form($msg["pprocs_options_liste_authorities"], 'AUTORITE', 'checkbox', 'yes');
		$content_form .= "
		<tr>
			<td>".$msg['pprocs_options_liste_checkbox']."</td>
			<td>
				<input type='checkbox' value='yes' name='CHECKBOX' ".($this->parameters['CHECKBOX'][0]['value']=="yes" ? "checked='checked'" : "")." />
				&nbsp;".$msg['pprocs_options_liste_checkbox_nb_on_line']."<input class='saisie-2em' type='text' name='CHECKBOX_NB_ON_LINE' value='".htmlentities($this->parameters['CHECKBOX_NB_ON_LINE'][0]['value'],ENT_QUOTES,$charset)."' />
			</td>					
		</tr>";
		$content_form .= $this->get_line_content_form($msg["num_auto_list"], 'NUM_AUTO', 'checkbox', 'yes');
		$content_form .= "
		<tr>
			<td>".$msg['procs_options_choix_vide']."</td>
			<td>".$msg['procs_options_value']." : <input type='text' size='5' name='UNSELECT_ITEM_VALUE' value='".htmlentities($this->parameters['UNSELECT_ITEM'][0]['VALUE'],ENT_QUOTES,$charset)."' />&nbsp;".$msg['procs_options_label']." : <input type='text' name='UNSELECT_ITEM_LIB' value='".htmlentities($this->parameters['UNSELECT_ITEM'][0]['value'],ENT_QUOTES,$charset)."' /></td>
		</tr>
		<tr>
			<td>".$msg["proc_options_default_value"]."</td>
			<td>".$msg['procs_options_value']." : <input type='text' class='saisie-10em' name='DEFAULT_VALUE' value='".htmlentities($this->parameters['DEFAULT_VALUE'][0]['value'],ENT_QUOTES,$charset)."' /></td>
		</tr>
		";
        return $content_form;
    }
    
    protected function get_additional_content_form() {
    	global $msg, $charset, $_custom_prefixe_;
    	global $DEL;
    	global $first, $options;
    	
    	if ($first == 4) {
    	    $this->set_additional_parameters_from_form();
    	}
    	$content_form = "
		<hr />".$msg['procs_options_liste_options']."<br />";
		
		if ($this->idchamp) {
			$content_form .= "<table border=1>
				<tr>
					<th role='column_header' scope='col'></th>
					<th role='column_header' scope='col'>".$msg["parperso_options_list_value"]."</th>
					<th role='column_header' scope='col'>".$msg["parperso_options_list_lib"]."</th>
					<th role='column_header' scope='col'>".$msg["parperso_options_list_order"]."</th>
				</tr>\n";
			$n=0;
			$requete="SELECT datatype FROM ".$_custom_prefixe_."_custom WHERE idchamp = $this->idchamp";
			$resultat = pmb_mysql_query($requete);
			$dtype = pmb_mysql_result($resultat,0,0);
		
			if (!empty($this->additional_parameters['ITEM'])) {
    			$ITEM_ID = $this->additional_parameters['ITEM_ID'];
    			$ITEM = $this->additional_parameters['ITEM'];
    			$VALUE = $this->additional_parameters['VALUE'];
    			$ORDRE = $this->additional_parameters['ORDRE'];
    			for ($i=0; $i<count($ITEM); $i++) {
    			    if (!isset($DEL[$i]) || (isset($DEL[$i]) && $DEL[$i] != 1)) {
    					//Recherche de la valeur dans les notices
    					$is_deletable=true;
    					if($VALUE[$i] !== "") {
    						$r_deletable="select count(".$_custom_prefixe_."_custom_origine) as C,".$_custom_prefixe_."_custom_".$dtype." as T from ".$_custom_prefixe_."_custom_values where ".$_custom_prefixe_."_custom_champ=".$this->idchamp." and ".$_custom_prefixe_."_custom_".$dtype."='".addslashes($VALUE[$i])."' GROUP BY T";
    						$r_del=pmb_mysql_query($r_deletable);
    						if (pmb_mysql_num_rows($r_del)) {
    							$objdel = pmb_mysql_fetch_object($r_del);
    							if ($objdel->T != $VALUE[$i]){
    								$is_deletable=true;
    							}elseif($objdel->C > 0){
    								$is_deletable=false;
    							}else{
    								$is_deletable=true;
    							}
    						}
    					}
    					$content_form .= "
    						<tr id='options_list_item_".$n."'>
    							<td ".(!$is_deletable?"title='".htmlentities($msg['perso_field_used'],ENT_QUOTES,$charset)."' ":"").">
    								<input type=\"hidden\" name=\"ITEM_ID[]\" value=\"".htmlentities($ITEM_ID[$i],ENT_QUOTES,$charset)."\" />
    								<input type=\"hidden\" name=\"EXVAL[]\" value=\"".htmlentities($VALUE[$i],ENT_QUOTES,$charset)."\" />
    								<input type=\"checkbox\" name=\"DEL[$n]\" value=\"1\" ".(!$is_deletable?"disabled='disabled' ":"")." />
    							</td>
    							<td ".(!$is_deletable?"title='".htmlentities($msg['perso_field_used'],ENT_QUOTES,$charset)."' ":"").">
    								<input class='saisie-10em' type=\"text\" value=\"".htmlentities($VALUE[$i],ENT_QUOTES,$charset)."\" name=\"VALUE[]\" ".(!$is_deletable?"readonly='readonly' ":"")." />
    							</td>
    							<td>
    								<input class='saisie-20em' type=\"text\" value=\"".htmlentities($ITEM[$i],ENT_QUOTES,$charset)."\" id=\"ITEM_".$n."\" name=\"ITEM[".$n."]\" data-translation-fieldname=\"".$_custom_prefixe_."_custom_list_lib\" />
    							</td>
    							<td>
    								<input class='saisie-10em' type=\"text\" value=\"".htmlentities($ORDRE[$i],ENT_QUOTES,$charset)."\" name=\"ORDRE[]\" />
    							</td>
    						</tr>";
    					
    					$translation = new translation($ITEM_ID[$i], $_custom_prefixe_.'_custom_lists');
    					$content_form .= $translation->connect('options_list_item_'.$n);
    					
    					$n++;
    				}
    			}
			}
			$content_form .= "</table>";
		} else {
			$content_form .= "<div class='row'><b>".$msg["parperso_options_list_before_rec"]."</b></div>";
		}
		if(!empty($options) && is_array($options)) {
		    $options=array_to_xml(stripslashes($options),"OPTIONS");
		}
		$content_form .= "<input type='hidden' name='options' value='".htmlentities($options, ENT_QUOTES, $charset)."' />";
		return $content_form;
    }
    
    protected function get_buttons_form() {
    	global $msg;
    	
    	$buttons = '';
    	if ($this->idchamp) {
    		$buttons .= "
				<input class='bouton' type='submit' value='".$msg['ajouter']."' onClick='this.form.first.value=2' />&nbsp;
				<input class='bouton' type='submit' value='".$msg['procs_options_suppr_options_coche']."' onClick='this.form.first.value=3' />&nbsp;
				<input class='bouton' type='submit' value='".$msg["proc_options_sort_list"]."' onClick='this.form.first.value=4' />&nbsp;
			";
		}
		$buttons .= "<input class='bouton' type='submit' value='".$msg[77]."' onClick='this.form.first.value=1'>";
    	return $buttons;
    }
    
    public function set_parameters_from_form() {
    	global $MULTIPLE, $AUTORITE, $CHECKBOX, $NUM_AUTO;
    	global $UNSELECT_ITEM_VALUE, $UNSELECT_ITEM_LIB, $DEFAULT_VALUE, $CHECKBOX_NB_ON_LINE;
    	
    	parent::set_parameters_from_form();
    	$this->parameters['MULTIPLE'][0]['value'] = "no";
    	if ($MULTIPLE == "yes") {
    		$this->parameters['MULTIPLE'][0]['value'] = "yes";
    	}
    	
    	$this->parameters['AUTORITE'][0]['value'] = "no";
    	if ($AUTORITE == "yes") {
    		$this->parameters['AUTORITE'][0]['value'] = "yes";
    	}
    	
    	$this->parameters['CHECKBOX'][0]['value'] = "no";
    	if ($CHECKBOX == "yes") {
    		$this->parameters['CHECKBOX'][0]['value'] = "yes";
    	}
    	
    	$this->parameters['NUM_AUTO'][0]['value'] = "no";
    	if ($NUM_AUTO == "yes") {
    		$this->parameters['NUM_AUTO'][0]['value'] = "yes";
    	}
    	
    	$this->set_additional_parameters_from_form();
		
		$this->parameters['UNSELECT_ITEM'][0]['VALUE']=stripslashes($UNSELECT_ITEM_VALUE);
		$this->parameters['UNSELECT_ITEM'][0]['value']=stripslashes($UNSELECT_ITEM_LIB);	
		$this->parameters['DEFAULT_VALUE'][0]['value']=stripslashes($DEFAULT_VALUE);
		$this->parameters['CHECKBOX_NB_ON_LINE'][0]['value']=stripslashes($CHECKBOX_NB_ON_LINE);
    }
    
    public function set_additional_parameters_from_form() {
        global $msg;
        global $NUM_AUTO;
        global $VALUE, $ITEM_ID, $ITEM, $ORDRE;
        global $first;
        
        /*
         * On regarde si il n'y a pas un doubon dans les valeurs
         */
        //On enlève les valeurs vide
        if (empty($VALUE)) {
            $VALUE = array();
            $ITEM_ID = array();
            $ITEM = array();
            $ORDRE = array();
        }
        foreach ( $VALUE as $key => $value ) {
            if($value === ""){
                unset($VALUE[$key]);
                unset($ITEM_ID[$key]);
                unset($ITEM[$key]);
                unset($ORDRE[$key]);
            }
        }
        //Pour que les clés se suivent
        if (!empty($VALUE)) {
            $VALUE=array_merge($VALUE);
        }
        if (!empty($ITEM_ID)) {
            $ITEM_ID=array_merge($ITEM_ID);
        }
        if (!empty($ITEM)) {
            $ITEM=array_merge($ITEM);
        }
        if (!empty($ORDRE)) {
            $ORDRE=array_merge($ORDRE);
        }
        //Pour tester si il y a des doublons
        $temp=array_flip($VALUE);
        if(is_array($VALUE) && (count($temp) != count($VALUE))){
            print "<script>
				alert('".$msg["parperso_valeur_existe_liste"]."');
				history.go(-1);
			</script>";
            exit();
        }
        
        //Tri des options
        if ($first==4) {
            if($ITEM){
                $ITEM_REVERSE=$ITEM;
                reset($ITEM_REVERSE);
                foreach ($ITEM_REVERSE as $key => $val) {
                    $ITEM_REVERSE[$key]=convert_diacrit($ITEM_REVERSE[$key]);
                }
                /*asort($ITEM_REVERSE);*/
                reset($ITEM_REVERSE);
                natcasesort($ITEM_REVERSE);
                reset($ITEM_REVERSE);
                $n_o=0;
                foreach ($ITEM_REVERSE as $key => $val) {
                    $ORDRE[$key]=$n_o;
                    $n_o++;
                }
            }
        }
        
        $this->additional_parameters = array();
        if (!empty($ITEM) && is_array($ITEM)) {
            for ($i=0; $i<count($ITEM); $i++) {
                $this->additional_parameters['ITEM_ID'][$i] = $ITEM_ID[$i];
                $this->additional_parameters['ITEM'][$i] = stripslashes($ITEM[$i]);
                $this->additional_parameters['VALUE'][$i] = stripslashes($VALUE[$i]);
                $this->additional_parameters['ORDRE'][$i] = $ORDRE[$i];
                    
                //Translations - A récup ici ?
            }
        }
        
        if ($first == 1) { //Si enregistrer
            $this->save_additional_parameters();
        } elseif ($first==2) {//Si ajouter
            $nb_items = count($ITEM);
            $this->additional_parameters['ITEM_ID'][$nb_items]=0;
            $this->additional_parameters['ITEM'][$nb_items]="";
            if($NUM_AUTO){
                if(!$VALUE && !$ITEM){
                    $this->additional_parameters['VALUE'][$nb_items]="1";
                } else {
                    $this->additional_parameters['VALUE'][$nb_items]=(max(array_map("tonum",$VALUE))*1)+1;
                }
            } else {
                $this->additional_parameters['VALUE'][$nb_items]="";
            }
            $this->additional_parameters['ORDRE'][$nb_items]="";
        }
    }
    
    public function save_additional_parameters() {
        global $_custom_prefixe_;
        
        if ($this->idchamp) {
            $requete="delete from ".$_custom_prefixe_."_custom_lists where ".$_custom_prefixe_."_custom_champ=".$this->idchamp;
            pmb_mysql_query($requete);
            /*$requete="SELECT datatype FROM ".$_custom_prefixe_."_custom WHERE idchamp = $this->idchamp";
             $resultat = pmb_mysql_query($requete);
             $dtype = pmb_mysql_result($resultat,0,0);*/
        }
        if (!empty($this->additional_parameters['ITEM']) && is_array($this->additional_parameters['ITEM'])) {
            for ($i=0; $i<count($this->additional_parameters['ITEM']); $i++) {
                if($this->additional_parameters['VALUE'][$i] !== "") {
                    /* On ne met pas a jour car on ne peut modifier que les valeurs qui ne sont pas utilisées*/
                    /*
                     $requete="UPDATE ".$_custom_prefixe_."_custom_values SET ".$_custom_prefixe_."_custom_".$dtype." = '".$VALUE[$i]."' WHERE  ".$_custom_prefixe_."_custom_champ = $this->idchamp AND ".$_custom_prefixe_."_custom_$dtype = '".$EXVAL[$i]."'";
                     pmb_mysql_query($requete);*/
                    if(!empty($this->additional_parameters['ITEM_ID'][$i])) {
                        $requete="insert into ".$_custom_prefixe_."_custom_lists (id_".$_custom_prefixe_."_custom_list,".$_custom_prefixe_."_custom_champ, ".$_custom_prefixe_."_custom_list_value, ".$_custom_prefixe_."_custom_list_lib, ordre) 
                            values(".$this->additional_parameters['ITEM_ID'][$i].", ".$this->idchamp.", '".addslashes($this->additional_parameters['VALUE'][$i])."','".addslashes($this->additional_parameters['ITEM'][$i])."','".addslashes($this->additional_parameters['ORDRE'][$i])."')";
                        pmb_mysql_query($requete);
                    } else {
                        $requete="insert into ".$_custom_prefixe_."_custom_lists (".$_custom_prefixe_."_custom_champ, ".$_custom_prefixe_."_custom_list_value, ".$_custom_prefixe_."_custom_list_lib, ordre) 
                            values(".$this->idchamp.", '".addslashes($this->additional_parameters['VALUE'][$i])."','".addslashes($this->additional_parameters['ITEM'][$i])."','".addslashes($this->additional_parameters['ORDRE'][$i])."')";
                        pmb_mysql_query($requete);
                        $this->additional_parameters['ITEM_ID'][$i] = pmb_mysql_insert_id();
                    }
                    $translation = new translation($this->additional_parameters['ITEM_ID'][$i], $_custom_prefixe_.'_custom_lists');
                    $translation->update_array($_custom_prefixe_.'_custom_list_lib', 'ITEM['.$i.']');
                }
            }
        }
    }
}
?>