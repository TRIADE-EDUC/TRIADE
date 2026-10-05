<?php
// +-------------------------------------------------+
// © 2002-2005 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: requester.class.php,v 1.15 2023/04/26 14:10:27 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $include_path;
require_once ($include_path.'/templates/requests.tpl.php');

class requester {
	
	public $t_type = array();		//Table de definition des types de requetes			 
	public $t_cont = array();		//Table de definition des contenus de requetes
	public $t_univ = array();		//Table de definition des univers et relations
	public $t_schema = array();	//Table schema de base
	public $t_fct = array();		//Table de definition des fonctions
	public $t_fct_grp = array();	//Table de definition des groupes de fonctions

	public $t_join = array();		//Table de stockage des jointures

	//Constructeur.	 
	public function __construct() {
		
		$rp = new reqParser();

		$this->t_schema = $rp->run('req_schema');
		$this->t_type = $rp->run('req_types');
		$this->t_cont = $rp->run('req_contents');
		$this->t_univ = $rp->run('req_universes');
		$tmp = $rp->run('req_functions');
		$this->t_fct = $tmp['REQ_FUNCTION'];
		$this->t_fct_grp = $tmp['REQ_FUNCTION_GROUP'];
	}

	public function getForm() {
		global $req_add_content_form, $msg;
		
		$content_form = $req_add_content_form;
		
		$interface_form = new interface_admin_form('req_modif');
		$interface_form->set_label($msg['req_form_tit_add']);
		
		$num_classement=0;
		$combo_clas= gen_liste ("SELECT idproc_classement,libproc_classement FROM procs_classements ORDER BY libproc_classement ", "idproc_classement", "libproc_classement", "form_classement", "", $num_classement, 0, $msg['proc_clas_aucun'],0, $msg['proc_clas_aucun']) ;
		$content_form = str_replace('!!classement!!', $combo_clas, $content_form);
		
		$content_form = str_replace('!!req_name!!', '', $content_form);
		$content_form = str_replace('!!req_type!!',$this->getTypeSelector('1','req_typeChg();'), $content_form);
		$content_form = str_replace('!!req_univ!!',$this->getUnivSelector('1','req_univChg();'), $content_form);
		$content_form = str_replace('!!req_comm!!','', $content_form);
		$content_form = str_replace('!!req_code!!','', $content_form);
		$content_form = str_replace('!!req_auth!!', request::getAutorisationsForm(), $content_form);
		
		//Seulement de l'ajout
		$interface_form->set_object_id(0)
		->set_content_form($content_form)
		->set_table_name('procs')
		->set_field_focus('req_name');
		return $interface_form->get_display();
	}
	
	//Retourne un selecteur pour choix des univers
	public function getUnivSelector($selected=0, $change='') {
		global $charset;
		
		$form = "<select id='req_univ' name='req_univ' onChange=\"".$change."\">";
		foreach($this->t_univ as $id=>$value) {
			$form.= "<option value='$id'";
			if($id==$selected) {
				$form.= " selected='selected'";
			}
			$form.= " >".htmlentities($value['name'], ENT_QUOTES, $charset)."</option>";
		}
		$form.= "</select>";
		return $form;
	}

	//Retourne un selecteur pour choix du type de requete
	public function getTypeSelector($selected=0, $change='') {
		global $charset;
		
		$form = "<select id='req_type' name='req_type' onChange=\"".$change."\" >";
		foreach($this->t_type as $id=>$value) {
			$form.="<option value='$id'";
			if($id==$selected) {
				$form.= " selected='selected'";
			}
			$form.=" >".htmlentities($value['name'], ENT_QUOTES, $charset)."</option>";
		}
		$form.= "</select>";
		return $form;
	}
	
	//Retourne le nom de la table de reference d'un univers
	public function getReferenceTableName($univ_id=0){
		$univ_id = intval($univ_id);
		if (!$univ_id) {
			return '';
		}
		$itr=$this->t_univ[$univ_id]['ref'];
		return $itr;
	}
	
	//Retourne l'id de la table de reference d'un univers
	public function getReferenceTableID($univ_id=0){
		$univ_id = intval($univ_id);
		if (!$univ_id) {
			return 0;
		}
		$tr=$this->getReferenceTableName($univ_id);
		$itr=$this->t_schema[$tr];
		return $itr;
	}

	//Retourne l'id d'une table a partir de son nom 
	public function getTableID($table_name=''){
		if (!$table_name) {
			return 0;
		}
		$table_id=$this->t_schema[$table_name];
		return $table_id;
	}

	//Retourne les informations sur une table a partir de son id
	public function getTableInfo($table_id=0){
		$table_id = intval($table_id);
		if (!$table_id) {
			return false;
		}
		return $this->t_schema[$table_id];
	}

	//Retourne la cle primaire d'une table
	public function getPrimaryKeyID($table_id=0){
		$table_id = intval($table_id);
		if (!$table_id) {
			return false;
		}
		$pkid=$this->t_schema[$table_id]['pkid'];
		return $pkid;
	}
	
	//Retourne les cles etrangeres d'une table
	public function getForeignKeys($table_id=0){
		$table_id = intval($table_id);
		if (!$table_id) {
			return false;
		}
		$t=array();
		foreach($this->t_schema[$table_id]['fields'] as $k=>$v){
			if ($this->isForeignKey($k)) {
				$t[]=$k;
			}
		}
		return $t; 
	}

	//Retourne l'Id de la table accessible depuis une cle etrangere
	public function getForeignTableID($foreign_key=0) {
		if(!$foreign_key) {
			return false;
		}
		$pk=$this->t_schema['cp_links'][$foreign_key];
		$tmp=explode('-',$pk);
		return $tmp[0];
	}

	//Retourne true si le champ fait partie d'une relation enfant-parent
	public function isForeignKey($field_id=0){
		$field_id = intval($field_id);
		if(!$field_id) {
			return false;
		}
		if(is_array($this->t_schema['cp_links']) && array_key_exists($field_id,$this->t_schema['cp_links'])) {
			return true;
		} else {
			return false;
		}
	}

	//Retourne la liste des id des champs d'une table
	public function getTableFieldIDList($table_id=0, $with_FK='true'){
		$table_id = intval($table_id);
		if(!$table_id) {
			return false;
		}
		$t = array();
		foreach($this->t_schema[$table_id]['fields'] as $k=>$v) {
			if ($with_FK){
				$t[]=$k;
			} else {
				if (!$this->isForeignKey($k)) $t[]=$k;
			}
		}
		return $t;
	}

	//Retourne la liste des informations pour les champs d'une table
	public function getTableFieldInfo($table_id=0, $with_FK='true'){
		$table_id = intval($table_id);
		if(!$table_id) {
			return false;
		}
		$t = array();
		foreach($this->t_schema[$table_id]['fields'] as $k=>$v) {
			if ($with_FK){
				$t[$k]=$v;
			} else {
				if (!$this->isForeignKey($k)) $t[$k]=$v;
			}
		}
		return $t;
	}
	
 	//Construit un selecteur avec la liste des tables accessibles a partir d'un Univers
	public function getTableSelector($univ_id=0 ,$selected=0){
		global $charset;

		$univ_id = intval($univ_id);
		if (!$univ_id) {
			return false;
		}

		$sel = "<select>\n";
		
		//Creation racine liens depuis les champs de la table de reference
		//$dtree.="_dt_fiel_.add('U".$univ_id."',-1,'&nbsp;&nbsp;".addslashes($msg['req_fiel_lbl'])."');\n";

		//Creation noeud table de base 
		$bt_id=$this->getReferenceTableID($univ_id);		
		$t_info=$this->getTableInfo($bt_id);
		$t_desc=$t_info['desc'];
		$node_id='T'.$bt_id;
		$sel.="<option value='".$node_id."' ";
		if(!$selected || $bt_id==$selected) $sel.="selected='selected'" ;
		$sel.=">".htmlentities($t_desc,ENT_QUOTES,$charset)."</option>";
		//$link=$node_id;
		
		//Creation noeuds de l'arbre
		//$dtree.=$this->getNodesTree($univ_id, $bt_id, $node_id);
				
		//$dtree.= "_dt_fiel_.icon.root='../../images/req_fiel.gif';";
		//$dtree.= "document.getElementById('req_fiel_tree').innerHTML = _dt_fiel_;\n";
		$sel.= "</select>\n";
		return $sel;
	}
	
	//Construit l'arbre des champs accessibles a partir d'un Univers
	public function getFielTree($univ_id=0 ){
		global $msg;

		$univ_id = intval($univ_id);
		if (!$univ_id) {
			return false;
		}

		$dtree = "<script type='text/javascript'>\n";
		$dtree.= "_dt_fiel_ = new dTree('_dt_fiel_');\n";
		
		//Creation racine liens depuis les champs de la table de reference
		$dtree.="_dt_fiel_.add('U".$univ_id."',-1,'&nbsp;&nbsp;".addslashes($msg['req_fiel_lbl'])."');\n";

		//Creation noeud table de base 
		$bt_id=$this->getReferenceTableID($univ_id);		
		$t_info=$this->getTableInfo($bt_id);
		$t_desc=$t_info['desc'];
		$node_id='T'.$bt_id;
		$dtree.="_dt_fiel_.add('".$node_id."','U".$univ_id."','".addslashes($t_desc)."');\n";
		//$link=$node_id;
		
		//Creation noeuds de l'arbre
		$dtree.=$this->getNodesTree($univ_id, $bt_id, $node_id);
				
		$dtree.= "_dt_fiel_.icon.root='../../images/req_fiel.gif';";
		$dtree.= "document.getElementById('req_fiel_tree').innerHTML = _dt_fiel_;\n";
		$dtree.= "</script>\n";
		return $dtree;
	}

	//Construit les noeuds de l'arbre des champs accessibles a partir d'un univers
	public function getNodesTree($univ_id, $table_id, $prev_node_id=0, $prev_rel_id=0, $prev_desc=''){
		global $charset;
		
		//Info univers
		$u_info=$this->t_univ[$univ_id];
		
		//Info table
		$t_info=$this->getTableInfo($table_id);
		$t_name=$t_info['name'];
		
		//Info champs
		$f_info=$t_info['fields'];
		
		foreach($f_info as $f_id=>$f_tab) {

			$f_name=$f_tab['name'];
			$exc_fields=explode(',',$u_info[$prev_rel_id]['except']);
			
			if (!in_array($t_name.'.'.$f_name,$exc_fields)) {
	
				//Affichage noeud champ
				$drag_text='';
				if(!$prev_rel_id) {
					$drag_text.=$f_tab['desc'];
					$node_id='F'.$f_id;
				} else {
					$drag_text=$prev_desc.'.'.$f_tab['desc'];
					$node_id=$prev_node_id.'_F'.$f_id;
				}
				$dtree.="_dt_fiel_.add('".$node_id."','".$prev_node_id."','".addslashes($f_tab['desc'])."', \"draggable='yes' dragtype='cell' dragtext='".htmlentities($drag_text, ENT_QUOTES, $charset)."' dragged_type='FI' dragged_id='".$node_id."' \", '#');\n";
				
				//Est-ce un champ lie?
				if (is_array($u_info['relations']['from'][$t_name.'.'.$f_name])) {
					foreach ($u_info['relations']['from'][$t_name.'.'.$f_name] as $rel_id) {
						$rel_to=explode('.',$u_info[$rel_id]['to']);
						$rel_type=$u_info[$rel_id]['type'];
						$rel_desc=$u_info[$rel_id]['desc'];
						$rel_prev=$u_info[$rel_id]['prev'];
						$rel_through=$u_info[$rel_id]['through'];
						if ($prev_rel_id==$rel_prev) {
							switch($rel_type) {
								case 'S1' :
									break;
								default :
									//Calcul noeud relation
									$r_node_id=$node_id.'_R'.$rel_id;
									
									//Affichage noeud relation						
									$dtree.="_dt_fiel_.add('".$r_node_id."','".$prev_node_id."','".addslashes($rel_desc)."');\n";
									$r_link=$r_node_id;
									
									//On descend dans l'arbre
									$s_t_id=$this->t_schema[$rel_to[0]];
									$dtree.=$this->getNodesTree($univ_id, $s_t_id, $r_link, $rel_id, $rel_desc);
									
									//Mise a jour table relations
									$this->addJoin($univ_id, $rel_id);
																
									//Est-ce une relation multivaluee?
									if ($rel_through) {
										$tmp=explode('.',$rel_through);
										$tmp2=explode(',',$tmp[1]);
										$lt_id=$this->getTableID($tmp[0]);
										$lt_info=$this->getTableInfo($lt_id);
										foreach($lt_info['fields'] as $lf_id=>$lf_tab){
											if (!in_array($lf_tab['name'], $tmp2)) {
												$drag_text=$rel_desc.'.'.$lf_tab['desc'];
												$lr_node_id=$r_node_id.'_F'.$lf_id;
												$dtree.="_dt_fiel_.add('".$lr_node_id."','".$r_node_id."','".addslashes($lf_tab['desc'])."', \"draggable='yes' dragtype='cell' dragtext='".htmlentities($drag_text, ENT_QUOTES, $charset)."' dragged_type='FI' dragged_id='".$lr_node_id."' \", '#');\n";
											}
										}
									}
									break;
							}
						}
					}
				}
			}
		}
		return $dtree;
	}	
	
	//Ajout relations a la table des jointures
	public function addJoin($univ_id,$rel_id,$n=0) {

		if (!($univ_id && $rel_id)) return;

		$t_rel = $this->t_univ[$univ_id][$rel_id];
		$tg_desc='';
		if (!$t_rel['prev']) {
			$tg_desc=$this->t_univ[$univ_id]['name'];
		} else {
			$tg_desc=$this->t_univ[$univ_id][$t_rel['prev']]['desc'];
		}
		$this->t_join[$rel_id][$n]['tg_desc']=$tg_desc;
		$this->t_join[$rel_id][$n]['td_desc']=$t_rel['desc'];

		switch($t_rel['type']) {
			case '11':
				$this->t_join[$rel_id][$n]['join']='S';
				break;
			case 'N0':
			case 'N1':
			case 'NN':
				$this->t_join[$rel_id][$n]['join']='R';
				break;
			default:
			case '0N':
			case '1N':
				$this->t_join[$rel_id][$n]['join']='L';
				break;
		}
		return;
	}
	
	//Retourne un formulaire pour la table des jointures
	public function getJoinTab() {
		global $charset,$joi_tab_line_select;
		
		$tpl='';
		foreach($this->t_join as $rel_id=>$rels_tab) {
			foreach($rels_tab as $rel_tab){
				$tpl.=$joi_tab_line_select;
				$tpl = str_replace('!!tg_desc!!', htmlentities($rel_tab['tg_desc'],ENT_QUOTES,$charset), $tpl);
				$tpl = str_replace('!!td_desc!!', htmlentities($rel_tab['td_desc'],ENT_QUOTES,$charset), $tpl);
 				$tpl = str_replace('!!R_rel!!','R'.$rel_id,$tpl);
 				$tpl = str_replace('!!N_rel!!','R'.$rel_id,$tpl);
/* 				
 * Ancienne version, avec definition des jointures en fonction de la relation			
				switch($rel_tab['join']) {
					case 'S':	//jointure stricte
						$tpl=str_replace('!!S_sel!!',"checked='checked'",$tpl);
						break;
					case 'R':	//jointure a droite
						$tpl=str_replace('!!R_sel!!',"checked='checked'",$tpl);
						break;
					case 'L':	//jointure a gauche
						$tpl=str_replace('!!L_sel!!',"checked='checked'",$tpl);
						break;
					case 'N':	//pas de jointure 
						$tpl=str_replace('!!N_sel!!',"checked='checked'",$tpl);
						break;
					default:
						break;
				}
				$tpl=str_replace('!!S_sel!!','',$tpl);
*/
 				$tpl=str_replace('!!S_sel!!',"checked='checked'",$tpl);
 				
				$tpl=str_replace('!!R_sel!!','',$tpl);
				$tpl=str_replace('!!L_sel!!','',$tpl);
				$tpl=str_replace('!!N_sel!!','',$tpl);
			}
		}
		return $tpl;
	}
	
	//Construit l'arbre des fonctions SQL
	public function getFuncTree(){
		global $msg, $charset;

		$dtree = "<script type='text/javascript'>\n";
		$dtree.= "_dt_func_ = new dTree('_dt_func_');\n";
		$dtree.="_dt_func_.add(0,-1,'&nbsp;&nbsp;".addslashes($msg['req_func_lbl'])."');\n";

		foreach($this->t_fct_grp as $k=>$v) {
			$g_name=$v['name'];
			$dtree.="_dt_func_.add(".($k).",0,'".addslashes($g_name)."');\n";
		}
		
		foreach($this->t_fct as $k=>$v) {
				$f_name=$v['name'];
				//TODO Traduction des messages
				//$f_desc=$msg['req_fct_'.$f_name];
				$f_desc=$f_name;
				$grp_id=1*$v['group'];
			$dtree.="_dt_func_.add(".$k.",".$grp_id.",'".addslashes($f_desc)."', \" draggable='yes' dragtype='cell' dragtext='".htmlentities($f_desc, ENT_QUOTES, $charset)."' dragged_type='FU' dragged_id='".$k."'\",'#' );\n";
		}
		
		$dtree.= "_dt_func_.icon.root='../../images/req_func.gif';";
		$dtree.= "document.getElementById('req_func_tree').innerHTML = _dt_func_;\n";
		$dtree.= "</script>\n";
		return $dtree;
	}

	//Construit l'arbre des sous-requetes SQL
	public function getSubrTree(){
		global $msg,$charset;

		$dtree='';
		$q = "select idproc, name, comment, libproc_classement, num_classement from procs left join procs_classements on idproc_classement=num_classement ";
		$q.= "where trim(requete) like('select %') ";
		$q.= "order by libproc_classement,name ";
		
		$r = pmb_mysql_query($q);
		if(pmb_mysql_num_rows($r)) {

			$dtree = "<script type='text/javascript'>\n";
			$dtree.= "_dt_subr_ = new dTree('_dt_subr_');\n";
			$dtree.= "_dt_subr_.add(0,-1,'&nbsp;&nbsp;".addslashes($msg['req_subr_lbl'])."');\n";			
			$t_num_classement=array();

 			while(($row=pmb_mysql_fetch_object($r))) {
				if(in_array($row->num_classement,$t_num_classement)===false) {
					if(!$row->num_classement) {
						$libproc_classement=$msg['proc_clas_aucun'];
					} else {
						$libproc_classement=$row->libproc_classement;
					}
					$t_num_classement[]=$row->num_classement;
					$dtree.= "_dt_subr_.add('c_".$row->num_classement."',0,'".addslashes($libproc_classement)."');\n";
				}
				$dtree.="_dt_subr_.add(".$row->idproc.",'c_".$row->num_classement."','".addslashes($row->name)."', \" draggable='yes' dragtype='cell' dragtext='".htmlentities($row->name, ENT_QUOTES, $charset)."' dragged_type='SU' dragged_id='".$row->idproc."'\",'#' );\n";
			}
			$dtree.= "_dt_subr_.icon.root='../../images/req_subr.gif';";
			$dtree.= "document.getElementById('req_subr_tree').innerHTML = _dt_subr_;\n";
			$dtree.= "</script>\n";
		}
		return $dtree;
	}
	
	//Construction requete a partir du formulaire poste
	public function buildRequest($req_type,$req_univ,$req_nb_lines,$req_datas) {
		global $msg;
		if (!$req_type || !$req_univ || !$req_nb_lines || !is_array($req_datas)) return;

		//TODO a mettre a jour au fur et a mesure de l'evolution du soft
		if ($req_type!='2') return ;

		//donnees de la requete
		$t_da=$req_datas['DA'];	//donnees
		$t_fi=$req_datas['FI']; //filtres
		//$t_va=$req_datas['VA']; //valeurs
		$t_al=$req_datas['AL']; //alias
		$t_vi=$req_datas['VI']; //visibilites
		$t_gr=$req_datas['GR']; //regroupements
		$t_so=$req_datas['SO']; //ordres
		$t_jo=$req_datas['JO']; //jointures
		$t_li=$req_datas['LI']; //limites
		
		$t_sql= array();		//Table de stockage des elements de la requete SQL

		//Type de la requete
		$t_sql['type']=$this->t_type[$req_type]['type'];

		$t_rel=array();

		//Pour chacune des lignes de la requete
		for($i=1;$i<$req_nb_lines+1;$i++) {
		
			//recuperation donnees
			if(is_array($t_da[$i]) && count($t_da[$i])) {
				$t=$this->buildDataContent($t_da[$i]);
				if(count($t['R'])) $t_rel+=$t['R'];
				if(count($t['D'])) $t_sql['data'][$i]=$t['D'];
				unset($t);
			}
			
			//recuperation valeurs
			//if (is_array($t_va[$lig])) {
			//}

			//recuperation filtres
			if(is_array($t_fi[$i]) && count($t_fi[$i])) {
				$t=$this->buildFilterContent($t_fi[$i]);
				if(count($t['R'])) $t_rel+=$t['R'];
				if(count($t['F'])) $t_sql['filter'][$i]=$t['F'];
				unset($t);
			}
		
			//recuperation alias
			if (trim($t_al[$i][0])) $t_sql['alias'][$i]=$t_al[$i][0];
			
			//recuperation visibilites
			if($t_vi[$i][0]) $t_sql['visibility'][$i]=$t_vi[$i][0];

			//recuperation regroupements
			if($t_gr[$i][0]) $t_sql['group'][$i]=$t_gr[$i][0];
			
			//recuperation tris
			if($t_so[$i][0]) {
				switch ($t_so[$i][0]){
					case '1':
						$t_sql['sort'][$i]='asc';
						break;
					case '2':
						$t_sql['sort'][$i]='desc';
						break;
					case '0':
					default:
						break;
				}
			}

		}


		//Creation jointures et tables
		//Suppression des jointures redondantes 
		$t_rel2=array_unique($t_rel);

		//Tri par nb de relations
		$t_rel3=array();
		foreach($t_rel2 as $v2) {
			$t_rel3[substr_count($v2,'_')][] =$v2;
		}
		ksort($t_rel3);

		//Liste des jointures a prendre en consideration
		$s_rel='';
		while(count($t_rel3)!==0) {
			$t_rel4=array_pop($t_rel3);
			foreach($t_rel4 as $v4) {
				if(strpos($s_rel,$v4)===false) $s_rel.=$v4;
			}
		}
		$t_rel5 = explode('_',$s_rel);


		//Liste des tables a prendre en compte dans la requete
		$t_sql['from']=array();
		//La table de reference
		$t_sql['from'][0][0]['prev']=0;
		$t_sql['from'][0][0]['t_from']=$this->t_schema[$this->getReferenceTableID($req_univ)]['name'];
		//Pour chaque relation
		foreach($t_rel5 as $v5) {
			if($v5) {
				$t_rel6 = $this->getFullJoin($req_univ, $v5, $t_jo);
				$t_sql['from']=array_merge($t_sql['from'],$t_rel6);
		 	}
		}

		
		//*********************************************************************
		//Creation de la requete !!!
		$request = '';
		//Le type
		$request.= $t_sql['type'].' ';

		//Les donnees
		if (is_array($t_sql['data'])) {
			$t_data=array();
			foreach ($t_sql['data'] as $k6=>$v6) {
				if ($t_sql['visibility'][$k6]) {
					$t_data[$k6]=$v6;
					if($t_sql['alias'][$k6]) $t_data[$k6].= " as \"".$t_sql['alias'][$k6]."\"";
				}
			}
			$s_data=implode(", ",$t_data);
			$request.=$s_data.' ';
		} else {
			$request.="* ";
		}
		
		//les tables et jointures
		$t_from=array();
		foreach($t_sql['from'] as $k7=>$v7) {
			if (!$k7) {
				$t_from[]=$v7[0]['t_from'].' ';
			} else {
				
				foreach($v7 as $k8=>$v8) {
					$prefix = '';
					if ($v8['prev']) {
						$prefix=$v8['prev'].'_';
					}
					switch($v8['join']) {
						case 'L' :	//jointure a gauche
							$t_from[]='left join ';
							$t_from[]=$v8['t_to'].' as '.$k7.'_'.$v8['t_to'];
							$t_from[]=' on ';
							$t_from[]=$k7.'_'.$v8['t_to'].'.'.$v8['f_to'].'=';
							$t_from[]=$prefix.$v8['t_from'].'.'.$v8['f_from'].' ';
							break;
						case 'R':	//jointure a droite
							$t_from[]='right join ';
							$t_from[]=$v8['t_to'].' as '.$k7.'_'.$v8['t_to'];
							$t_from[]=' on ';
							$t_from[]=$k7.'_'.$v8['t_to'].'.'.$v8['f_to'].'=';
							$t_from[]=$prefix.$v8['t_from'].'.'.$v8['f_from'].' ';
							break;
						case 'S':	//jointure stricte
							$t_from[]='join ';
							$t_from[]=$v8['t_to'].' as '.$k7.'_'.$v8['t_to'];
							$t_from[]=' on ';
							$t_from[]=$k7.'_'.$v8['t_to'].'.'.$v8['f_to'].'=';
							$t_from[]=$prefix.$v8['t_from'].'.'.$v8['f_from'].' ';
							break;
						case 'N':	//Pas de jointure
						default :
							$t_from[]=', ';
							$t_from[]=$v8['t_to'].' as '.$k7.'_'.$v8['t_to'];
							break;
					} 
				}
			}
		}
		$s_from= 'from '.implode('',$t_from).' ';
		$request.=$s_from;

		
		//Les clauses where
		$s_filter='';
		$last_data=$msg['req_error'];
		if (is_array($t_sql['filter'])) {
			$t_filter=array();
			foreach ($t_sql['filter'] as $k6=>$v6) {
				for($i=$k6;$i>=0;$i--){
					if($t_sql['data'][$i]) {
						$last_data=$t_sql['data'][$i];
						break;
					}
				}
				foreach($v6 as $v7) {
					if(count($t_filter[$k6])) {
						$t_filter[$k6].=' OR ';  
					}
					$t_filter[$k6].=$last_data.$v7;
				}
			}
			$s_filter.='('.implode(") and (",$t_filter).')';
		}
		if($s_filter) {
			$request.= " where ".$s_filter." ";
		}
				
		
		//Les regroupements
		if (is_array($t_sql['group'])) {
			$t_group=array();
			foreach ($t_sql['group'] as $k8=>$v8) {
				if ($v8==1 && $t_sql['data'][$k8]) {
					$t_group[]=$t_sql['data'][$k8];
				}
			}
			if (count($t_group!=0)) {
				$s_group=implode(", ",$t_group);
				$request.= "group by $s_group ";
			}
		}
		
		//Les tris
		if (is_array($t_sql['sort'])) {
			$t_sort=array();
			foreach ($t_sql['sort'] as $k9=>$v9) {
				$t_sort[]=$t_sql['data'][$k9].' '.$v9;
			}
			if (count($t_sort!=0)) {
				$s_sort=implode(", ",$t_sort);
				$request.='order by '.$s_sort.' ';
			}
		}

		
		//Les limites
		$s_limit='';
		if(is_array($t_li)) {
			if ($t_li['B']!=='') {
				$s_limit.= $t_li['B'].', ';
			}
			if ($t_li['Q']!=='') {
				$s_limit.= $t_li['Q']; 
			}
		}
		if ($s_limit!=='') {
			$request.='limit '.$s_limit;
		}

		return $request;
	}


	//Extraction des parametres d'une fonction
	//$f_id	= id de fonction
	public function getFunction($f_id) {
		$data=array();
		if($this->t_fct[$f_id]['name']) {
			$data['id']=$this->t_fct[$f_id]['id'];
			$data['name']=$this->t_fct[$f_id]['name'];
			$data['parenthesis']=$this->t_fct[$f_id]['parenthesis'];
			$data['param']=$this->t_fct[$f_id]['param'];
			$data['filter']=$this->t_fct[$f_id]['filter'];
			
		}
		return $data;
			
	}
	
	//Extraction d'une sous-requete
	//$r_id	= id de requete
	public function getSubRequest($r_id) {
		$r_id = intval($r_id);
		$sub='';
		$q = "select requete from procs where idproc='".$r_id."'";
		$r = pmb_mysql_query($q);
		if(pmb_mysql_num_rows($r)) {
			$sub = '('.pmb_mysql_result($r,0,0).')';
		}
		return $sub;
	}

	
	//Extraction des infos de champ et de relation a partir de l'identifiant poste
	//$f_id	= id de champ
	public function getField($f_id) {
		$data=array();
		$data['D']='';		//Stockage donnees calculees
		$data['R']='';		//Stockage relations trouvees
		$tmp=explode('_',$f_id);
		$tmp2=explode('-',end($tmp));	//Id dernier champ
		$t_id=substr($tmp2[0],1);
		$f_id=substr(end($tmp),1);
		$t_name=$this->t_schema[$t_id]['name'];
		$f_name=$this->t_schema[$t_id]['fields'][$f_id]['name'];
		if (count($tmp)>1) {
			$rel_id=$tmp[count($tmp)-2];
			foreach($tmp as $v1) {
				if (substr($v1,0,1)=='R') {
					$data['R'].=$v1.'_';
				}
			}
			$data['D']=$rel_id.'_';
		} else {
			$rel_id=0;
			$data['R']=0;
		}
		$data['D'].=$t_name.'.'.$f_name;
		return $data;
	}

	
	//Extraction des jointures multiples 
	public function getFullJoin($univ_id, $rel, $t_jo) {
		$rel_id=substr($rel,1);
		$join=array();
		$tf_from=array();
		$tf_to=array();
 		$r_prev=$this->t_univ[$univ_id][$rel_id]['prev'];
	 	$tf_from=explode('.',$this->t_univ[$univ_id][$rel_id]['from']);
 		$t_from=$tf_from[0];
 		$f_from=$tf_from[1];
 		$tf_to=explode('.',$this->t_univ[$univ_id][$rel_id]['to']);
 		$t_to=$tf_to[0];
 		$f_to=$tf_to[1];
		$r_through=$this->t_univ[$univ_id][$rel_id]['through'];
 		if ($r_through) {

			$tmp=explode('.',$r_through);
			$t_int=$tmp[0];
			$tmp2=explode(',',$tmp[1]);
			$f_int_from=$tmp2[0];
			$f_int_to=$tmp2[1];
			

	 		if ($r_prev) {
	 			$join[$rel][0]['prev']='R'.$r_prev;
	 		} else {
	 			$join[$rel][0]['prev']=0;
	 		}
			$join[$rel][0]['t_from']=$t_from;
			$join[$rel][0]['f_from']=$f_from;
			$join[$rel][0]['t_to']=$t_int;
			$join[$rel][0]['f_to']=$f_int_from;
			$join[$rel][0]['join']=$t_jo[$rel][0];
			
			$join[$rel][1]['prev']=$rel;
			$join[$rel][1]['t_from']=$t_int;
			$join[$rel][1]['f_from']=$f_int_to;
			$join[$rel][1]['t_to']=$t_to;
			$join[$rel][1]['f_to']=$f_to;
			$join[$rel][1]['join']=$t_jo[$rel][0];
						
 		} else {

 	 		if ($r_prev) {
	 			$join[$rel][0]['prev']='R'.$r_prev;
	 		} else {
	 			$join[$rel][0]['prev']=0;
	 		}
 			$join[$rel][0]['t_from']=$t_from;
			$join[$rel][0]['f_from']=$f_from;
			$join[$rel][0]['t_to']=$t_to;
			$join[$rel][0]['f_to']=$f_to;
			$join[$rel][0]['join']=$t_jo[$rel][0];
 		}
 		return $join;
	}
	
	
	//Recuperation des attributs de fonction a partir de l'identifiant
	public function getAttributes($fct_id=0,$c_type='') {
		global $charset;
		if (!$fct_id) die('No function id');
		
		$tp_fct=$this->t_fct[$fct_id]['param'];

		$xml="<params parenthesis=\"".$this->t_fct[$fct_id]['parenthesis']."\" remove=\"".$this->t_fct[$fct_id]['remove']."\" >\n";
		if(count($tp_fct)) {
			foreach($tp_fct as $v) {
				if($c_type !="FI" || ($v['order'] >= $this->t_fct[$fct_id]['filter']) ) {
					$xml.= "<param order=\"".$v['order']."\" content=\"".$v['content']."\" optional=\"".$v['optional']."\" repeat_from=\"".$v['repeat_from']."\" value=\"".trim(htmlspecialchars($v['value'],ENT_QUOTES,$charset))."\" />\n";
				}
			}
			
		}
		$xml.="</params>\n";
		return $xml;
	}

	//construit la partie data d'une ligne de requete
	public function buildDataContent($t_da) {
		$t_ret=array();
		$t_fct=array();	//table de stockage de l'expression sql pour une fonction
		$tab_fu=array(); //table de stockage des fonctions en cours de traitement
		$n_fu=0;
		foreach($t_da as $e_da) {
			
			$da_type=key($e_da);
			$da_cont=$e_da[$da_type];
			
			switch ($da_type) {

				case 'FU' :	//Debut Fonction
					
					//si on est dans une fonction, c'est un argument
					if ($n_fu) {
						$tab_fu[$n_fu]['params'][]=array('sub',($n_fu+1));
					}
					
					//stockage id fonction courante 
					$n_fu++;
					$tab_fu[$n_fu]['id']=$da_cont;
					break;
					
				case 'order' : //Nouvel element fonction
					$tab_fu[$n_fu]['params'][]=array('order',$da_cont);
					break;
					
				case 'FU_E' : //Fin Fonction
					
					//Traitement de la fonction ici
					$f_format=$this->getFunction($tab_fu[$n_fu]['id']);
					
					//print '<pre>';print_r($tab_fu);print '</pre>';
		
					$f_content=array();
					$order=0;
					foreach($tab_fu[$n_fu]['params'] as $v) {
						switch($v[0]) {
							case 'order' :
								if ($f_format['param'][$v[1]]['optional']!='yes' && $f_format['param'][$v[1]]['content']=='keyword') {
									if($f_format['param'][$v[1]]['before_sep']) {
										$f_content[]=$f_format['param'][$v[1]]['before_sep'];
									}
									$f_content[]=$f_format['param'][$v[1]]['value'];
								}
								if($f_format['parenthesis']==$v[1]) $f_content[]='(';
								$order=$v[1];
								break;
							case 'arg' :
								if($f_format['param'][$order]['before_sep']) {
									$f_content[]=$f_format['param'][$order]['before_sep'];
								}
								$f_content[]=$v[1];
								break;
							case 'sub' :
								if($f_format['param'][$order]['before_sep']) {
									$f_content[]=$f_format['param'][$order]['before_sep'];
								}
								$f_content[]=array_pop($t_fct);
							default :
								break;
						}
					}
					//parenthese fermante
					if($f_format['parenthesis']) $f_content[]=')';
					
					//print '<pre>';print_r($f_content);print '</pre>';
					
					//ajout de la fonction a la table de stockage de l'expression sql pour une fonction 
					$t_fct[]=implode('',$f_content);
					array_pop($tab_fu);
					$n_fu--;
					if($n_fu==0) {
						$t_ret['D']=implode(' ',$t_fct);
					}
					break;
					
				case 'FI' :	//Champ		
					$fi_data= array();
					$fi_data=$this->getField($da_cont);
					if($fi_data['R']) {
						$t_ret['R'][]=$fi_data['R'];
					}
					//si on est dans une fonction, c'est un argument
					if($n_fu) {
						$tab_fu[$n_fu]['params'][]=array('arg',$fi_data['D']);
					} else {
						$t_ret['D']=$fi_data['D'];
					}
					break;
					
				case 'TE' : //Saisie libre
					//si on est dans une fonction, c'est un argument
					if($n_fu) {
						$tab_fu[$n_fu]['params'][]=array('arg',stripslashes($da_cont));
					} else {
						$t_ret['D']=stripslashes($da_cont);
					}
					break;
					
				case 'SU' : //Sous requete
					//si on est dans une fonction, c'est un argument
					if($n_fu) {
						$tab_fu[$n_fu]['params'][]=array('arg',$this->getSubRequest($da_cont));
					} else {
						$t_ret['D']=$this->getSubRequest($da_cont);
					}
					break;
				
				default:
					break;
			
			}
		}
		return $t_ret;
	}
	
	//construit la partie filtre d'une ligne de requete (where)
	public function buildFilterContent($t_fi) {
		$t_ret=array();
		$t_fct=array();	//table de stockage de l'expression sql pour une fonction
		$tab_fu=array(); //table de stockage des fonctions en cours de traitement
		$n_fu=0;
		
		foreach($t_fi as $e_fi) {
			
			$fi_type=key($e_fi);
			$fi_cont=$e_fi[$fi_type];
			
			switch ($fi_type) {

				case 'FU' :	//Debut Fonction
					
					//si on est dans une fonction, c'est un argument
					if ($n_fu) {
						$tab_fu[$n_fu]['params'][]=array('sub',($n_fu+1));
					}
					
					//stockage id fonction courante 
					$n_fu++;
					$tab_fu[$n_fu]['id']=$fi_cont;
					break;
					
				case 'order' : //Nouvel element fonction
					$tab_fu[$n_fu]['params'][]=array('order',$fi_cont);
					break;
					
				case 'FU_E' : //Fin Fonction
					
					//Traitement de la fonction ici
					$f_format=$this->getFunction($tab_fu[$n_fu]['id']);
					
					$f_content=array();
					$order=0;
					
					foreach($tab_fu[$n_fu]['params'] as $v) {
						switch($v[0]) {
							case 'order' :
								if ($f_format['param'][$v[1]]['optional']!='yes' && $f_format['param'][$v[1]]['content']=='keyword') {
									$f_content[]=$f_format['param'][$v[1]]['value'];
								}
								if($f_format['parenthesis']==$v[1]) $f_content[]='(';
								$order=$v[1];
								break;
							case 'arg' :
								if($f_format['param'][$order]['before_sep']) {
									$f_content[]=$f_format['param'][$order]['before_sep'];
								}
								$f_content[]=$v[1];
								break;
							case 'sub' :
								if($f_format['param'][$order]['before_sep']) {
									$f_content[]=$f_format['param'][$order]['before_sep'];
								}
								$f_content[]=array_pop($t_fct);
							default :
								break;
						}
					}
					//parenthese fermante
					if($f_format['parenthesis']) $f_content[]=')';

					//ajout de la fonction a la table de stockage de l'expression sql pour une fonction 
					$t_fct[]=implode('',$f_content);
					array_pop($tab_fu);
					$n_fu--;
					if($n_fu==0) {
						$eq="";
						if(!$f_format['filter']) $eq="=";
						$t_ret['F'][]=$eq.implode(' ',$t_fct);
						unset($t_fct);
					}

					break;
					
				case 'FI' :	//Champ		
					$fi_filter= array();
					$fi_filter=$this->getField($fi_cont);
					if($fi_filter['R']) {
						$t_ret['R'][]=$fi_filter['R'];
					}

					//si on est dans une fonction, c'est un argument
					if($n_fu) {
						$tab_fu[$n_fu]['params'][]=array('arg',$fi_filter['D']);
					} else {
						$t_ret['F'][]="=".$fi_filter['D'];
					}
					break;
					
				case 'TE' : //Saisie libre
					//si on est dans une fonction, c'est un argument
					if($n_fu) {
						$tab_fu[$n_fu]['params'][]=array('arg',stripslashes($fi_cont));
					} else {
						$t_ret['F'][]="=".stripslashes($fi_cont);
					}
					break;
					
				case 'SU' : //Sous requete
					//si on est dans une fonction, c'est un argument
					if($n_fu) {
						$tab_fu[$n_fu]['params'][]=array('arg',$this->getSubRequest($fi_cont));
					} else {
						$t_ret['F'][]="=".$this->getSubRequest($fi_cont);
					}
					break;
				
				default:
					break;
			
			}
		}
		return $t_ret;
	}	
}


class reqParser {

	public $parser;
	public $t=array();
	public $cur_id=0;
	public $cur_rel=0;
	public $cur_fct=0;
	
	public function __construct() {
	}
	
	public function run($file) {
		global $include_path;
		global $charset;
		
		//Recherche du fichier XML de description
		$file = $include_path.'/requests/'.$file.'.xml';
		$xml=file_get_contents($file,"r") or die("Can't find XML file $file");

		unset($this->t);
		$this->cur_id=0;
		
		$m = array();
		$rx = "/<?xml.*encoding=[\'\"](.*?)[\'\"].*?>/m";
		if (preg_match($rx, $xml, $m)) $encoding = strtoupper($m[1]);
			else $encoding = "ISO-8859-1";
		
		$this->parser = xml_parser_create($encoding);
		xml_set_object($this->parser, $this);
		xml_parser_set_option($this->parser, XML_OPTION_TARGET_ENCODING, $charset);		
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, FALSE);	
		xml_parser_set_option($this->parser, XML_OPTION_SKIP_WHITE, TRUE);
		xml_set_element_handler($this->parser, "tagStart", "tagEnd");
		if (!xml_parse($this->parser, $xml, TRUE)) {
			die( sprintf( "erreur XML %s à la ligne: %d", 
				xml_error_string(xml_get_error_code($this->parser ) ),
				xml_get_current_line_number($this->parser) ) );
		}
		xml_parser_free($this->parser);
		return ($this->t);
	}

	public function tagStart($parser, $tag, $att) {
		global $msg;
		
		switch ($tag) {
			case 'table':
				$this->t[$att['id']]['name']= $att['name'];
				$this->t[$att['id']]['desc']= $att['desc'];
				$this->t[$att['id']]['pkid']= $att['pkid'];
				$this->t[$att['name']]=$att['id'];
				$this->cur_id=$att['id'];
				break;
			case 'field':
				$this->t[$this->cur_id]['fields'][$att['id']]['name']= $att['name'];
				$this->t[$this->cur_id]['fields'][$att['id']]['desc']= $att['desc'];
				$this->t[$this->cur_id]['fields'][$att['id']]['type']= $att['type'];
				$this->t[$this->cur_id]['fields'][$att['id']]['name']= $att['name'];
				$this->t[$this->cur_id]['fields'][$att['id']]['length']= $att['length'];
				$this->t[$this->cur_id]['fields'][$att['id']]['precision']= $att['precision'];
				$this->t[$this->cur_id]['fields'][$att['id']]['autoincrement']= $att['autoincrement'];
				$this->t[$this->cur_id]['fields'][$att['id']]['enum']= $att['enum'];
				$this->t[$this->cur_id]['fields'][$att['id']]['defval']= $att['defval'];
				break;
			case 'lien':
				$this->t['cp_links'][$att['child']]=$att['parent'];
				$this->t['pc_links'][$att['parent']][]=$att['child'];
				break;
			case 'REQ_TYPE':
				$this->t[$att['id']]['type']= $att['type'];
				$this->t[$att['id']]['name']= $msg[$att['name']];
				break;
			case 'REQ_CONTENT':
				$this->t['REQ_CONTENT'][$att['id']]['type']= $att['type'];
				$this->t['REQ_CONTENT'][$att['id']]['name']= (isset($att['name']) && isset($msg[$att['name']]) ? $msg[$att['name']] : '');
				break;
			case 'REQ_CONTAINER':
				$this->t['REQ_CONTAINER'][$att['id']]['type']= $att['type'];
				$this->t['REQ_CONTAINER'][$att['id']]['name']= (isset($att['name']) && isset($msg[$att['name']]) ? $msg[$att['name']] : '');
				break;
			case 'REQ_UNIVERSE':
				$this->t[$att['id']]['name']= $msg[$att['name']];
				$this->t[$att['id']]['ref']= $att['ref'];
				$this->cur_id=$att['id'];
				break;
			case 'REQ_RELATION':
				$this->t[$this->cur_id]['relations']['from'][$att['from']][]=$att['id'];
				$this->t[$this->cur_id][$att['id']]['from']=$att['from'];
				$this->t[$this->cur_id][$att['id']]['prev']=(isset($att['prev']) ? $att['prev'] : '');
				$this->t[$this->cur_id][$att['id']]['type']=$att['type'];
				$this->t[$this->cur_id][$att['id']]['to']=$att['to'];
				$this->t[$this->cur_id][$att['id']]['desc']=$att['desc'];
				$this->t[$this->cur_id][$att['id']]['except']=(isset($att['except']) ? $att['except'] : '');		
				$this->cur_rel=$att['id'];
				break;
			case 'REQ_THROUGH':
					$this->t[$this->cur_id][$this->cur_rel]['through']= $att['through'];
				break;				
			case 'REQ_FUNCTION_GROUP' :
				$this->t['REQ_FUNCTION_GROUP'][$att['id']]['name']=$att['name'];
				break;
			case 'REQ_FUNCTION':
				$this->t['REQ_FUNCTION'][$att['id']]['name']=$att['name'];
				$this->t['REQ_FUNCTION'][$att['id']]['group']=$att['group'];
				$this->t['REQ_FUNCTION'][$att['id']]['parenthesis']= (isset($att['parenthesis']) ? $att['parenthesis'] : '');
				$this->t['REQ_FUNCTION'][$att['id']]['remove']= $att['remove'];
				$this->t['REQ_FUNCTION'][$att['id']]['filter']= (isset($att['filter']) ? $att['filter'] : '');
				$this->cur_fct=$att['id'];
				break;
			case 'FCT_PARAM':
				$this->t['REQ_FUNCTION'][$this->cur_fct]['param'][$att['order']]['order']= $att['order'];
				$this->t['REQ_FUNCTION'][$this->cur_fct]['param'][$att['order']]['content']= $att['content'];
				$this->t['REQ_FUNCTION'][$this->cur_fct]['param'][$att['order']]['optional']= (isset($att['optional']) ? $att['optional'] : '');
				$this->t['REQ_FUNCTION'][$this->cur_fct]['param'][$att['order']]['value']= (isset($att['value']) ? $att['value'] : '');
				$this->t['REQ_FUNCTION'][$this->cur_fct]['param'][$att['order']]['before_sep']= (isset($att['before_sep']) ? $att['before_sep'] : '');
				$this->t['REQ_FUNCTION'][$this->cur_fct]['param'][$att['order']]['repeat_from']= (isset($att['repeat_from']) ? $att['repeat_from'] : '');
				break;				
			default :
				break;
		}
		return;
	}
	
	public function tagEnd($parser, $tag) {
		return;
	}
	
}


?>