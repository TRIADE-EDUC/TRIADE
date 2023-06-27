<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: authorities_statuts.class.php,v 1.14 2017-11-21 13:38:21 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class authorities_statuts{
	protected static $statuts = array();
	private static $statuts_fetched = false;
	
	
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
		foreach(static::$statuts as $id => $statut){
			if ($i % 2) {
				$pair_impair = "even";
			} else {
				$pair_impair = "odd";
			}
			print "
			<tr  class='$pair_impair' style='cursor: pointer' onmouseover=\"this.className='surbrillance'\" onmouseout=\"this.className='$pair_impair'\">
				<td onclick='document.location=\"./admin.php?categ=authorities&sub=statuts&action=edit&id=".$id."\"'><span class='".$statut['class_html']."' style='margin-right:3px;'><img width='10' height='10' src='".get_url_icon('spacer.gif')."'/></span>".htmlentities($statut['label'], ENT_QUOTES, $charset)."</td>
			</tr>";
			$i++;
		}
		print "
		</table>
		<div class='row'>
			<input type='button' class='bouton' value='".$msg['115']."' onclick='document.location=\"./admin.php?categ=authorities&sub=statuts&action=add\"'/>		
		</div>";
	}
	
	public static function get_list(){
		global $dbh;
		
		if(!static::$statuts_fetched){
			static::$statuts = array();
			$query = "select id_authorities_statut, authorities_statut_label, authorities_statut_class_html, authorities_statut_available_for from authorities_statuts order by authorities_statut_label";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_object($result)){
					static::$statuts[$row->id_authorities_statut] = array(
						'label' => $row->authorities_statut_label,
						'class_html' => $row->authorities_statut_class_html,
						'available_for' => unserialize($row->authorities_statut_available_for)							
					);
					if(!is_array(static::$statuts[$row->id_authorities_statut]['available_for'])){
						static::$statuts[$row->id_authorities_statut]['available_for'] = array();
					}
				}
			}
			static::$statuts_fetched = true;
		}
	}
	
	public static function show_form($id){
		global $msg,$charset;	
		global $admin_authorities_statut_form;
		
		static::get_list();
		$id+=0;
		$form = $admin_authorities_statut_form;
		
		if(isset(static::$statuts[$id])){
			$form_title = $msg['118'];
			$statut = static::$statuts[$id];
		}else{
			$form_title = $msg['115'];
			$statut = array(
				'label' =>	"",
				'class_html' => "statutnot1",
				'available_for' => array()
			);
		}
		
		$form = str_replace("!!form_title!!", $form_title, $form);
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
		$form = str_replace("!!class_html!!", $couleurs, $form);

		$form = str_replace("!!gestion_libelle!!", htmlentities($statut['label'],ENT_QUOTES,$charset),$form);
		if($id == 1 || !isset(static::$statuts[$id])){
			$form = str_replace("!!bouton_supprimer!!","",$form);
		}else{
			$form = str_replace("!!bouton_supprimer!!","<input class='bouton' type='button' value=' $msg[supprimer] ' onClick=\"javascript:confirmation_delete(!!id!!,'!!libelle_suppr!!')\" />",$form); ;
		}
		
		$authorities = static::get_authorities_list();
		$i=0;
		$authorities_list = '';
		foreach($authorities as $value => $name){
		    if($i!= 0 && $i % 5 == 0){
				$authorities_list.= "<br>";
			}
			$authorities_list.= "<span style='margin-right:5px;'><input".($id==1 ? " disabled='disabled'" : "")." type='checkbox'".( (in_array($value,$statut['available_for']) || $id == 1) ? " checked='checked'" : "")." name='form_available_for[]' value='".$value."'/> $name</span>";
			$i++;
		}
		
		$form = str_replace("!!list_authorities!!", $authorities_list, $form);
		
		$form.=confirmation_delete("./admin.php?categ=authorities&sub=statuts&action=del&id=");
		$form = str_replace('!!libelle_suppr!!', addslashes($statut['label']), $form);
		$form = str_replace("!!id!!",$id,$form);
		print $form;
	}
	
	
	public static function get_from_from(){
		global $id,$form_gestion_libelle,$form_class_html, $form_available_for;
		
		if($id == 1) {
			$form_available_for = array_keys(self::get_authorities_list());
		}
		return array(
			'id' => stripslashes($id),
			'label' => stripslashes($form_gestion_libelle),
			'class_html' => stripslashes($form_class_html),
			'available_for' => $form_available_for
		);
	}
	
	public static function save($statut){
		global $dbh;
		$statut['id'] += 0; 
		if($statut['label'] != ""){ 
			if($statut['id'] != 0){
				$query = " update authorities_statuts set ";
				$where = "where id_authorities_statut = ".$statut['id'];
			}else{
				$query = " insert into authorities_statuts set ";
				$where = "";
			}
			$query.="
				authorities_statut_label = '".addslashes($statut['label'])."',
				authorities_statut_class_html = '".addslashes($statut['class_html'])."',
				authorities_statut_available_for = '".addslashes(serialize($statut['available_for']))."' ";
			$result = pmb_mysql_query($query.$where,$dbh);
			if($result){
				static::$statuts_fetched = false;
			}else{
				return false;
			}
		}
		return true;
	}
	
	public static function delete($id) {
		global $dbh;
		$id+=0;
		if($id==1) return true;
		
		if(!count($used = static::check_used($id))){
			$query = "delete from authorities_statuts where id_authorities_statut = ".$id;
			pmb_mysql_query($query,$dbh);
			return true;
		}
		return false;	
			
	}
	
	public static function check_used($id){
		global $dbh,$msg;
		global $base_path;
		
		$id+=0;
		$used = array();
		$query = "select type_object, count(*) as used FROM authorities where num_statut = ".$id." group by type_object order by used desc";
		$result = pmb_mysql_query($query,$dbh);
		if(pmb_mysql_num_rows($result)){
			while($row = pmb_mysql_fetch_object($result)){
				if($row->used != 0){
					switch ($row->type_object){
						case AUT_TABLE_AUTHORS: 
							$categ='auteurs';
							$name= $msg['133'];
						break;
						case AUT_TABLE_CATEG: 
							$categ='categories';
							$name= $msg['134'];
						break;
						case AUT_TABLE_PUBLISHERS: 
							$categ='editeurs';
							$name= $msg['135'];
						break;
						case AUT_TABLE_COLLECTIONS: 
							$categ='collections';
							$name= $msg['136'];
						break;
						case AUT_TABLE_SUB_COLLECTIONS: 
							$categ='souscollections';
							$name= $msg['137'];
						break;
						case AUT_TABLE_SERIES: 
							$categ='series';
							$name= $msg['333'];
						break;
						case AUT_TABLE_INDEXINT: 
							$categ='indexint';
							$name= $msg['indexint_menu'];
						break;
						case AUT_TABLE_TITRES_UNIFORMES: 
							$categ='titres_uniformes';
							$name= $msg['aut_menu_titre_uniforme'];
						break;
						case AUT_TABLE_CONCEPT: 
							$categ='concepts';
							$name= $msg['ontology_skos_menu'];
						break;
						default://Authperso
							$categ='authperso&id_authperso='.($row->type_object-1000);
							$name= $msg['authperso_multi_search_title'];
							
						break; 
					}
					$used[]=array(
						'type'=>$row->type_object,
						'used'=>$row->used,			
						'categ'=>$categ,		
						'msg'=>$name,			
						'link'=>'<a href="'.$base_path.'/autorites.php?categ='.$categ.'&authority_statut='.$id.'">'.$name.'( '.$row->used.' )</a>',						
					);
				}
			}
		}
		return $used;
	}
	
	private static function get_authorities_list(){
		global $msg,$thesaurus_concepts_active,$pmb_use_uniform_title;
		$authorities = array(
			AUT_TABLE_AUTHORS => $msg['133'], 
			AUT_TABLE_CATEG => $msg['134'], 
			AUT_TABLE_PUBLISHERS => $msg['135'], 
			AUT_TABLE_COLLECTIONS => $msg['136'],
			AUT_TABLE_SUB_COLLECTIONS => $msg['137'], 
			AUT_TABLE_SERIES => $msg['333'] , 
			AUT_TABLE_INDEXINT => $msg['indexint_menu'] 
		);
		if($pmb_use_uniform_title){
			$authorities[AUT_TABLE_TITRES_UNIFORMES] = $msg['aut_menu_titre_uniforme'];
		}
		if($thesaurus_concepts_active){
			$authorities[AUT_TABLE_CONCEPT] = $msg['ontology_skos_menu'];
		}
		

		$authpersos= authpersos::get_instance();
		$info_authpersos=$authpersos->get_data();
		foreach($info_authpersos as $authperso){
			$authorities[($authperso['id']+1000)] = $authperso['name'];
		}
		return $authorities;
	}
	
	/**
	 * Fonction permettant de générer le selecteur des statut définis pour un type d'autorité
	 * @param integer $auth_type Constante type d'autorité (ou 1000+id authperso)
	 * @param integer $auth_statut_id Identifiant du statut enregistré pour l'autorité courante 
	 * @param boolean $selector_search Sélécteur affiché dans la page de recherche
	 * @return string
	 */
	public static function get_form_for($auth_type, $auth_statut_id, $search=false){
	    global $msg;
	    $auth_statut_id+=0;
        $statuts_defined = static::get_statuts_for($auth_type);
        $on_change='';
        $selector = '<select name="authority_statut" '.$on_change.' >';
        if($search){
            $selector.='<option value="0">'.$msg['authorities_statut_selector_all'].'</option>';
        }
        foreach($statuts_defined as $id_statut => $statut){
            $selector.='<option '.(($id_statut == $auth_statut_id)?'selected="selected"':'').' value="'.$id_statut.'">'.$statut['label'].'</option>';
        }
        $selector.= '</select>';
        return $selector;
	}
	
	/**
	 * Fonction retournant un tableau des statut défini pour le type d'autorité passé en parametre
	 * @param integer $auth_type Type d'autorité
	 * @return array $statuts_found Tableau des statuts disponible pour le type d'autorité passé en parametre
	 */
	private static function get_statuts_for($auth_type){
	    /**
	     * TODO test sur auth_type pour les authorités perso
	     */
	    static::get_list();
	    $statuts_found = array();
	    foreach(static::$statuts as $id_statut => $statut){
	        if(in_array($auth_type,$statut['available_for']) || ($id_statut==1)){
	            $statuts_found[$id_statut] = $statut;
	        }
	        //TODO: array merge authority perso
	    }
	    return $statuts_found;
	} 
}