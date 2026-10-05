<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area_status.class.php,v 1.6 2022/01/05 08:35:29 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once ($class_path."/contribution_area/contribution_area.class.php");
require_once("$include_path/templates/contribution_area/contribution_area_status.tpl.php");

class contribution_area_status{
	protected static $status = array();
	private static $status_fetched = false;
	
	
	public static function show_list(){
		global $msg;
		global $charset;
		static::get_list();
		
		print "
		<table>
			<tr>
				<th>".$msg['noti_statut_libelle']."</th>
			</tr>";
		$i=0;
		foreach(static::$status as $id => $statut){
			if ($i % 2) {
				$pair_impair = "even";
			} else {
				$pair_impair = "odd";
			}
			print "
			<tr  class='$pair_impair' style='cursor: pointer' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\">
				<td onclick='document.location=\"./modelling.php?categ=contribution_area&sub=status&action=edit&id=".$id."\"'><span class='".$statut['class_html']."' style='margin-right:3px;'><img width='10' height='10' src='".get_url_icon('spacer.gif')."'/></span>".htmlentities($statut['label'], ENT_QUOTES, $charset)."</td>
			</tr>";
			$i++;
		}
		print "
		</table>
		<div class='row'>
			<input type='button' class='bouton' value='".$msg['115']."' onclick='document.location=\"./modelling.php?categ=contribution_area&sub=status&action=add\"'/>		
		</div>";
	}
	
	public static function get_list(){
		if(!static::$status_fetched){
			static::$status = array();
			$query = "select contribution_area_status_id, contribution_area_status_gestion_libelle,contribution_area_status_class_html, contribution_area_status_available_for from contribution_area_status order by contribution_area_status_gestion_libelle";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_object($result)){
					static::$status[$row->contribution_area_status_id] = array(
						'label' => $row->contribution_area_status_gestion_libelle,
						'class_html' => $row->contribution_area_status_class_html,
						'available_for' => unserialize($row->contribution_area_status_available_for)							
					);
					if(!is_array(static::$status[$row->contribution_area_status_id]['available_for'])){
						static::$status[$row->contribution_area_status_id]['available_for'] = array();
					}
				}
			}
			static::$status_fetched = true;
		}
	}
	
	public static function show_form($id){
		global $msg,$charset;	
		global $admin_contribution_area_status_form;
		
		static::get_list();
		$id = intval($id);
		$form = $admin_contribution_area_status_form;
		
		if(isset(static::$status[$id])){
			$form_title = $msg['118'];
			$statut = static::$status[$id];
		}else{
			$form_title = $msg['115'];
			$statut = array(
				'label' =>	"",
				'class_html' => "statutnot1",
				'available_for' => array()
			);
		}
		
		$form = str_replace("!!form_title!!", $form_title, $form);
		$couleur = array();
		for ($i=1;$i<=20; $i++) {
			if ($statut['class_html'] == "statutnot".$i){
			    $checked = "checked";
			}
			else {
			    $checked = "";
			}
			$couleur[$i]="<span for='statutnot".$i."' class='statutnot".$i."' style='margin: 7px;'><img src='".get_url_icon('spacer.gif')."' width='10' height='10' />
					<input id='statutnot".$i."' type=radio name='form_class_html' value='statutnot".$i."' $checked class='checkbox' /></span>";
			if ($i==10) $couleur[10].="<br />";
			elseif ($i!=20) $couleur[$i].="<b>|</b>";
		}
		
		$couleurs=implode("",$couleur);
		
	    $button = "";
		if($id != 1 && isset(static::$status[$id])){
		    $button = "<input class='bouton' type='button' value='".$msg["supprimer"]."' onClick=\"javascript:confirmation_delete(!!id!!,'!!libelle_suppr!!')\" />";
		}
		
		
		$form = str_replace(
		    array(
		        "!!class_html!!",
		        "!!gestion_libelle!!",
		        '!!bouton_supprimer!!',
		    ),
		    array(
		        $couleurs,
		        htmlentities($statut['label'],ENT_QUOTES,$charset),
		        $button,
		    ), $form);
		
		$entities_list = static::get_pmb_entities();
		$pmb_entities = "";
		
		// Si c'est le "Statut par défaut" on désactive tout
		$default = "";
		if ($id == 1) {
		    $default = 'onclick="return false;" readonly="readonly"';
		}
		
		$i = 0;
		foreach($entities_list as $value => $name){
		    
		    if($i!= 0 && $i % 5 == 0){
				$pmb_entities.= "<br>";
			}
			
			$inputId = "entity_".$value;
			$isChecked = "";
			if (in_array($value,$statut['available_for']) || $id == 1 || $id == 0) {
			    $isChecked = 'checked="checked"';
			}
			
			$pmb_entities .= '<span id="entitie_item">
                                <input class="entitie_item_checkbox" id="'.$inputId.'" '.$default.' type="checkbox"'.$isChecked.' name="form_available_for[]" value="'.$value.'"/> 
                                <label for="'.$inputId.'">'.$name.'</label>
                            </span>';
			
			$i++;
		}
		
		$form = str_replace(
		    array(
		        "!!coche_button_type!!",
		        "!!list_entities!!",
		        '!!libelle_suppr!!',
		        "!!id!!"
		    ), 
		    array(
		        ($id==1 ? 'hidden' : "button"),
		        $pmb_entities,
		        addslashes($statut['label']),
		        $id
		    ), $form);
		
		$form .= confirmation_delete("./modelling.php?categ=contribution_area&sub=status&action=del&id=");
		
		print $form;
	}
	
	
	public static function get_from_from(){
		global $id,$form_gestion_libelle,$form_class_html, $form_available_for;
		
		if($id == 1) {
			$form_available_for = array_keys(self::get_pmb_entities());
		}
		return array(
			'id' => stripslashes($id),
			'label' => stripslashes($form_gestion_libelle),
			'class_html' => stripslashes($form_class_html),
			'available_for' => $form_available_for
		);
	}
	
	public static function save($statut){
		$statut['id'] = intval($statut['id']); 
		if($statut['label'] != ""){ 
			if($statut['id'] != 0){
				$query = " update contribution_area_status set ";
				$where = "where contribution_area_status_id = ".$statut['id'];
			}else{
				$query = " insert into contribution_area_status set ";
				$where = "";
			}
			$query.="
				contribution_area_status_gestion_libelle = '".addslashes($statut['label'])."',
				contribution_area_status_class_html = '".addslashes($statut['class_html'])."',
				contribution_area_status_available_for = '".addslashes(serialize($statut['available_for']))."' ";
			$result = pmb_mysql_query($query.$where);
			if($result){
				static::$status_fetched = false;
			}else{
				return false;
			}
		}
		return true;
	}
	
	public static function delete($id) {
		$id = intval($id);
		if($id==1) return true;
		
		if(!count(static::check_used($id))){
			$query = "delete from contribution_area_status where contribution_area_status_id = ".$id;
			pmb_mysql_query($query);
			return true;
		}
		return false;	
			
	}
	
	/**
	 * Fonction qui controle si le status de contribution est utilisé
	 * @param integer $id 
	 * @return array:
	 */
	public static function check_used($id){
		$id = intval($id);
		$used = array();
		return $used;
	}
	
	private static function get_pmb_entities(){		
		return contribution_area::get_pmb_entities();		
	}
	
	/**
	 * Fonction permettant de générer le selecteur des statut définis pour un type d'autorité
	 * @param integer $auth_type Constante type d'autorité (ou 1000+id authperso)
	 * @param integer $auth_statut_id Identifiant du statut enregistré pour l'autorité courante 
	 * @param boolean $selector_search Sélécteur affiché dans la page de recherche
	 * @return string
	 */
	public static function get_form_for($pmb_entity, $contribution_area_id, $search=false){
	    global $msg;
	    
        $status_defined = static::get_status_for($pmb_entity);
        $on_change='';
        if($search){
        	$on_change='onchange="if(this.form) this.form.submit();"';        
        }
        $selector = '<select name="contribution_area_status" '.$on_change.' >';
        if($search){
            $selector.='<option value="0">'.$msg['contribution_area_status_selector_all'].'</option>';
        }
        foreach($status_defined as $id_statut => $statut){
            $selector.='<option '.(($id_statut == $contribution_area_id)?'selected="selected"':'').' value="'.$id_statut.'">'.$statut['label'].'</option>';
        }
        $selector.= '</select>';
        return $selector;
	}
	
	/**
	 * Fonction retournant un tableau des statut défini pour le type d'autorité passé en parametre
	 * @param integer $auth_type Type d'autorité
	 * @return array $status_found Tableau des status disponible pour le type d'autorité passé en parametre
	 */
	private static function get_status_for($pmb_entity){
	    /**
	     * TODO test sur auth_type pour les authorités perso
	     */
	    static::get_list();
	    $status_found = array();
	    foreach(static::$status as $id_statut => $statut){
	        if(in_array($pmb_entity,$statut['available_for']) || ($id_statut==1)){
	            $status_found[$id_statut] = $statut;
	        }
	        //TODO: array merge authority perso
	    }
	    return $status_found;
	} 
}