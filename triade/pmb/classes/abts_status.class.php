<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: abts_status.class.php,v 1.2 2019-06-07 13:48:35 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($include_path . '/templates/abts_abonnements.tpl.php');

class abts_status{
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
				<td onclick='document.location=\"./admin.php?categ=abonnements&sub=status&action=edit&id=".$id."\"'><span class='".$statut['class_html']."' style='margin-right:3px;'><img width='10' height='10' src='".get_url_icon('spacer.gif')."'/></span>".htmlentities($statut['label'], ENT_QUOTES, $charset)."</td>
			</tr>";
			$i++;
		}
		print "
		</table>
		<div class='row'>
			<input type='button' class='bouton' value='".$msg['115']."' onclick='document.location=\"./admin.php?categ=abonnements&sub=status&action=add\"'/>		
		</div>";
	}
	
	public static function get_list(){
		global $dbh;
		
		if(!static::$status_fetched){
			static::$status = array();
			$query = "select abts_status_id, abts_status_gestion_libelle,abts_status_class_html,abts_status_bulletinage_active					
					from abts_status order by abts_status_gestion_libelle";
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				while($row = pmb_mysql_fetch_object($result)){
					static::$status[$row->abts_status_id] = array(
						'label' => $row->abts_status_gestion_libelle,
						'class_html' => $row->abts_status_class_html,
						'bulletinage_active' => $row->abts_status_bulletinage_active,					
					);
				}
			}
			static::$status_fetched = true;
		}
	}
	
	public static function show_form($id){
		global $msg,$charset;	
		global $admin_abts_status_form;
		
		static::get_list();
		$id+=0;
		$form = $admin_abts_status_form;
		
		if(isset(static::$status[$id])){
			$form_title = $msg['118'];
			$statut = static::$status[$id];
		}else{
			$form_title = $msg['115'];
			$statut = array(
				'label' =>	"",
				'class_html' => "statutnot1"
			);
		}
		
		$couleur = array();
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
		
		if(empty($statut['bulletinage_active'])) $statut['bulletinage_active'] = '';
		$form = str_replace("!!bulletinage_active_checked!!", ($statut['bulletinage_active'] ? 'checked=checked' : ''), $form);
		
		$form = str_replace("!!gestion_libelle!!", htmlentities($statut['label'],ENT_QUOTES,$charset),$form);
		if($id == 1 || !isset(static::$status[$id])){
			$form = str_replace("!!bouton_supprimer!!","",$form);
		}else{
			$form = str_replace("!!bouton_supprimer!!","<input class='bouton' type='button' value=' $msg[supprimer] ' onClick=\"javascript:confirmation_delete(!!id!!,'!!libelle_suppr!!')\" />",$form); ;
		}
				
		$form.=confirmation_delete("./admin.php?categ=abonnements&sub=status&action=del&id=");
		$form = str_replace('!!libelle_suppr!!', addslashes($statut['label']), $form);
		$form = str_replace("!!id!!",$id,$form);
		print $form;
	}
	
	
	public static function get_from_from(){
		global $id, $form_gestion_libelle, $form_class_html, $form_bulletinage_active;
		
		return array(
			'id' => stripslashes($id),
			'label' => stripslashes($form_gestion_libelle),
			'class_html' => stripslashes($form_class_html),
			'bulletinage_active' => stripslashes($form_bulletinage_active),
		);
	}

	public static function get_ids_bulletinage_active(){
		static::get_list();
		
		$ids = array();
		foreach(static::$status as $id_statut => $statut){
			if($statut['bulletinage_active']) {
				$ids[] = $id_statut;
			}
		}
		return $ids;
	}
	
	public static function save($statut){
		global $dbh;
		
		$statut['id'] += 0; 
		if($statut['label'] != ""){ 
			if($statut['id'] != 0){
				$query = " update abts_status set ";
				$where = "where abts_status_id = ".$statut['id'];
			}else{
				$query = " insert into abts_status set ";
				$where = "";
			}
			$query.="
				abts_status_gestion_libelle = '".addslashes($statut['label'])."',
				abts_status_class_html = '".addslashes($statut['class_html'])."',
				abts_status_bulletinage_active = '".addslashes($statut['bulletinage_active'])."'
			";
			$result = pmb_mysql_query($query.$where,$dbh);
			if($result){
				static::$status_fetched = false;
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
			$query = "delete from abts_status where abts_status_id = ".$id;
			pmb_mysql_query($query,$dbh);
			return true;
		}
		return false;	
			
	}
	
	/**
	 * Fonction qui controle si le status est utilisé
	 * @param integer $id du statut
	 * @return array: ids des abonnemets
	 */
	public static function check_used($id){
		global $dbh,$msg;
		global $base_path;
		
		$id+=0;
		$used = array();
		$query="select abt_id from abts_abts where abt_status=".$id;
		$res = pmb_mysql_query($query);
		if (pmb_mysql_num_rows($res)) {
			while($r = pmb_mysql_fetch_object($res)) {
				$used[] = $r->abt_id;
			}			
		}
		return $used;
	}
	
	
	/**
	 * Fonction permettant de générer le selecteur des statuts
	 * @param integer $id du statut sélectionné  
	 * @param boolean $selector_search Sélécteur affiché dans la page de recherche
	 * @return string
	 */
	public static function get_form_for($id, $search=false){
	    global $msg;
	    
	    $id+=0;
	    static::get_list();
	    
        $on_change='';
        if($search){
        	$on_change='onchange="if(this.form) this.form.submit();"';        
        }
        $selector = '<select name="abts_status" '.$on_change.' >';
        if($search){
            $selector.='<option value="0">'.$msg['abts_status_selector_all'].'</option>';
        }
        foreach(static::$status as $id_statut => $statut){
            $selector.='<option '.(($id_statut == $id)?'selected="selected"':'').' value="'.$id_statut.'">'.$statut['label'].'</option>';
        }
        $selector.= '</select>';
        return $selector;
	}
	
	/**
	 * Fonction qui construit l'affichage du statut
	 * @param integer $id du statut
	 * @return string
	 */
	public static function get_display($id){
		global $msg, $charset;
		 
		$id+=0;
		static::get_list();
		$statut = static::$status[$id];
		$display = "<small><span class='".$statut['class_html']."' style='margin-right: 3px;'><a href=# onmouseover=\"z=document.getElementById('zoom_statut".$id."'); z.style.display=''; \" onmouseout=\"z=document.getElementById('zoom_statut".$id."'); z.style.display='none'; \"><img src='".get_url_icon('spacer.gif')."' width='10' height='10' /></a></span></small>";
		$display .= "<div id='zoom_statut".$id."' style='border: solid 2px #555555; background-color: #FFFFFF; position: absolute; display:none; z-index: 2000;'><b>".nl2br(htmlentities($statut['label'],ENT_QUOTES, $charset))."</b></div>";
		return $display;
	}
}