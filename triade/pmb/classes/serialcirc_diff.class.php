<?php
// +-------------------------------------------------+
// | 2002-2007 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: serialcirc_diff.class.php,v 1.44.4.2 2025/03/04 10:45:25 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path, $include_path;
require_once($include_path."/serialcirc.inc.php"); // constant déclaration
require_once($include_path."/templates/serialcirc_diff.tpl.php");
require_once($class_path."/serial_display.class.php");
require_once($class_path."/empr_caddie.class.php");
require_once($class_path."/serialcirc_ask.class.php");
require_once($class_path."/serialcirc_tpl.class.php");
require_once($class_path."/serialcirc_print_fields.class.php");
require_once($class_path."/parametres_perso.class.php");

class serialcirc_diff {

	public $id;
	public $diffusion=array();
	public $abt_info=array();
	public $serial_info=array();
	public $num_abt=0;
	public $circ_type=0; // rotative, étoile
	public $virtual_circ=0; // virtuelle
	public $simple_circ=0; // simplifiée
	public $no_ret_circ=0; // pas de retour sur site

	public $num_periodicite=0;
	public $retard_mode=0;
	public $checked=0;
	public $allow_send_ask=0;
	public $allow_resa=0;
	public $allow_copy=0;
	public $allow_subscription=0;
	public $duration_before_send=0;
	public $expl_statut_circ=0;
	public $expl_statut_circ_after=0;
	public $sort_diff='';
	public $diffusion_nb_recipients=0;
	public $id_perio;
	public $duration;
	public $piedpage;
	public $tpl;
	public $empr_info;
	public $abt_name;

	protected static $instances = array();

	public function __construct($id_serialcirc=0,$num_abt=0 ) {
		$id_serialcirc = intval($id_serialcirc);
		$num_abt = intval($num_abt);
		if($num_abt && !$id_serialcirc){
			$requete="select id_serialcirc from serialcirc where num_serialcirc_abt=".$num_abt;
			$resultat=pmb_mysql_query($requete);
			if (pmb_mysql_num_rows($resultat)) {
				$r=pmb_mysql_fetch_object($resultat);
				$id_serialcirc=$r->id_serialcirc;
			}
		} elseif(!$num_abt && $id_serialcirc){
			$requete="select num_serialcirc_abt from serialcirc where id_serialcirc=".$id_serialcirc;
			$resultat=pmb_mysql_query($requete);
			if (pmb_mysql_num_rows($resultat)) {
				$r=pmb_mysql_fetch_object($resultat);
				$num_abt=$r->num_serialcirc_abt;
			}

		}
		$this->num_abt=$num_abt;
		$this->id=$id_serialcirc;
		$this->fetch_data();
	}

	public function fetch_data() {
		$this->diffusion=array();
		$this->abt_info=array();
		$this->serial_info=array();
		//on récupère les infos liées au périos
		if($this->num_abt){
			$query = "select notice_id,tit1,abt_name,location_id from abts_abts join notices on notice_id = num_notice and abt_id = ".$this->num_abt;
			$result = pmb_mysql_query($query);
			if(pmb_mysql_num_rows($result)){
				$row = pmb_mysql_fetch_object($result);
				$this->abt_name=$row->abt_name;
				$this->id_perio=$row->notice_id;
				$this->serial_info['serial_link']=	"./catalog.php?categ=serials&sub=view&serial_id=".$row->notice_id;
				$this->serial_info['serial_name']=$row->tit1;
				$this->serial_info['abt_link']="catalog.php?categ=serials&sub=view&view=abon&serial_id=".$row->notice_id;
				$this->serial_info['serialcirc_link']="catalog.php?categ=serialcirc_diff&sub=view&num_abt=".$this->num_abt;
				$this->serial_info['abt_name']=$row->abt_name;
				$this->serial_info['abt_location']=$row->location_id;
				$this->serial_info['bulletinage_link']="./catalog.php?categ=serials&sub=pointage&serial_id=".$row->notice_id;
			}
		}
		//on récupère les in infos sur la circulation
		if($this->id){
			$requete="select * from serialcirc where id_serialcirc=".$this->id ;
			$resultat=pmb_mysql_query($requete);
			if (pmb_mysql_num_rows($resultat)) {
				$r=pmb_mysql_fetch_object($resultat);
				$this->circ_type=$r->serialcirc_type; // rotative ou étoile
				$this->virtual_circ=$r->serialcirc_virtual; // virtuelle
				$this->simple_circ=$r->serialcirc_simple; // virtuelle
				$this->no_ret_circ=$r->serialcirc_no_ret;
				$this->duration=$r->serialcirc_duration;
				$this->checked=$r->serialcirc_checked;
				$this->retard_mode=$r->serialcirc_retard_mode;
				$this->allow_send_ask=$r->serialcirc_allow_send_ask;
				$this->allow_resa=$r->serialcirc_allow_resa;
				$this->allow_copy=$r->serialcirc_allow_copy;
				$this->allow_subscription=$r->serialcirc_allow_subscription;
				$this->duration_before_send=$r->serialcirc_duration_before_send;
				$this->expl_statut_circ=$r->serialcirc_expl_statut_circ;
				$this->piedpage=$r->serialcirc_piedpage;
				$this->tpl=$r->serialcirc_tpl;
				$this->expl_statut_circ_after=$r->serialcirc_expl_statut_circ_after;
				$this->sort_diff=$r->serialcirc_sort_diff;
				$this->diffusion_nb_recipients=0;

				// liste des lecteurs et groupes de lecteur
				$requete="select * from serialcirc_diff where num_serialcirc_diff_serialcirc=".$this->id." order by serialcirc_diff_order";
				$resultat=pmb_mysql_query($requete);

				while($r_empr=pmb_mysql_fetch_object($resultat)){
					$this->diffusion[$r_empr->id_serialcirc_diff]['id']=$r_empr->id_serialcirc_diff;
					$this->diffusion[$r_empr->id_serialcirc_diff]['empr_type']=$r_empr->serialcirc_diff_empr_type;
					$this->diffusion[$r_empr->id_serialcirc_diff]['type_diff']=$r_empr->serialcirc_diff_type_diff;
					$this->diffusion[$r_empr->id_serialcirc_diff]['num_empr']=$r_empr->num_serialcirc_diff_empr;
					$this->diffusion[$r_empr->id_serialcirc_diff]['empr_name']=$r_empr->serialcirc_diff_group_name;
					$this->diffusion[$r_empr->id_serialcirc_diff]['order']=$r_empr->serialcirc_diff_order;
					$this->diffusion[$r_empr->id_serialcirc_diff]['duration']=$r_empr->serialcirc_diff_duration;
					$this->diffusion[$r_empr->id_serialcirc_diff]['group']= array();// tableau des lecteurs du group
					if($this->diffusion[$r_empr->id_serialcirc_diff]['empr_type']==SERIALCIRC_EMPR_TYPE_group){
						// C'est un groupe; on va chercher les lecteurs de ce groupe
						$requete="select * from serialcirc_group where num_serialcirc_group_diff=".$this->diffusion[$r_empr->id_serialcirc_diff]['id']." order by serialcirc_group_order";
						$resultat_group_empr=pmb_mysql_query($requete);
						$cpt_empr_group=0;
						while($r_group_empr=pmb_mysql_fetch_object($resultat_group_empr)){
							$this->diffusion[$r_empr->id_serialcirc_diff]['group'][$cpt_empr_group]['id']=$r_group_empr->id_serialcirc_group;
							$this->diffusion[$r_empr->id_serialcirc_diff]['group'][$cpt_empr_group]['num_empr']=$r_group_empr->num_serialcirc_group_empr;
							$this->diffusion[$r_empr->id_serialcirc_diff]['group'][$cpt_empr_group]['order']=$r_group_empr->serialcirc_group_order;
							$this->diffusion[$r_empr->id_serialcirc_diff]['group'][$cpt_empr_group]['responsable']=$r_group_empr->serialcirc_group_responsable;
							$this->diffusion[$r_empr->id_serialcirc_diff]['group'][$cpt_empr_group]['empr']=$this->empr_info($r_group_empr->num_serialcirc_group_empr);
							$cpt_empr_group++;
							$this->diffusion_nb_recipients++;
						}
					} else{
						// c'est un emprunteur physique
						$this->diffusion[$r_empr->id_serialcirc_diff]['empr']=$this->empr_info($this->diffusion[$r_empr->id_serialcirc_diff]['num_empr']);
						$this->diffusion_nb_recipients++;
					}
				}
			}
		}else{
			$this->duration= $this->get_consultation_duration();
		}
		//print"<pre>";print_r($this->diffusion);print"</pre>";exit;
	}

	public function get_consultation_duration(){
		$requete="select consultation_duration from abts_periodicites , abts_abts,abts_modeles, abts_abts_modeles where
			abts_abts.abt_id=abts_abts_modeles.abt_id and abts_modeles.modele_id=abts_abts_modeles.modele_id and num_periodicite=periodicite_id
			and abts_abts.abt_id=".$this->num_abt ;
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
			$r=pmb_mysql_fetch_object($resultat);
			return 	$r->consultation_duration;
		}
		return 0;
	}

	public function add_circ_expl($expl_id){
		if(!$this->id || !$expl_id) return 0;
		$req="INSERT INTO serialcirc_expl SET
			num_serialcirc_expl_id=".$expl_id.",
			num_serialcirc_expl_serialcirc=".$this->id.",
			serialcirc_expl_bulletine_date=now();
		";
		pmb_mysql_query($req);
	}

	public function empr_info($id){
		$info=array();
		$req="select empr_cb, empr_nom ,  empr_prenom, empr_mail, empr_adr1, empr_ville, empr_location from empr where id_empr=".$id;
		$res_empr=pmb_mysql_query($req);
		if ($empr=pmb_mysql_fetch_object($res_empr)) {
			$info['cb'] = $empr->empr_cb;
			$info['nom'] = $empr->empr_nom;
			$info['prenom'] = $empr->empr_prenom;
			$info['mail'] = $empr->empr_mail;
			$info['id_empr'] = $id;
			$info['view_link'] = './circ.php?categ=pret&form_cb='.$empr->empr_cb;
			$info['empr_libelle'] = $info['nom']." ".$info['prenom']." ( ".$info['cb'] ." ) ";
			$info['adr1'] = $empr->empr_adr1;
			$info['ville'] = $empr->empr_ville;
			$info['location'] = $empr->empr_location;
		}
		$this->empr_info[$id]=$info;
		return $info;
	}

	public function update_serialcirc($data = array()){
		if(empty($data)){
			$data['circ_type']=0;
			$data['virtual_circ']=0;
			$data['simple_circ']=1;
			$data['no_ret_circ']=0;
			$data['duration']=0;
			$data['checked']=0;
			$data['retard_mode']=0;
			$data['allow_resa']=0;
			$data['allow_copy']=0;
			$data['allow_send_ask']=0;
			$data['allow_subscription']=0;
			$data['duration_before_send']=0;
			$data['expl_statut_circ']=0;
			$data['expl_statut_circ_after']=0;
		}else{
			$data['circ_type'] = intval($data['circ_type']);
			$data['virtual_circ'] = intval($data['virtual_circ']);
			$data['simple_circ'] = intval($data['simple_circ']);
			$data['no_ret_circ'] = intval($data['no_ret_circ']);
			$data['duration'] = intval($data['duration']);
			$data['checked'] = intval($data['checked']);
			$data['retard_mode'] = intval($data['retard_mode']);
			$data['allow_resa'] = intval($data['allow_resa']);
			$data['allow_copy'] = intval($data['allow_copy']);
			$data['allow_send_ask'] = intval($data['allow_send_ask']);
			$data['allow_subscription'] = intval($data['allow_subscription']);
			$data['duration_before_send'] = intval($data['duration_before_send']);
			$data['expl_statut_circ'] = intval($data['expl_statut_circ']);
			$data['expl_statut_circ_after'] = intval($data['expl_statut_circ_after']);
		}

		$req = "num_serialcirc_abt=".$this->num_abt.",
				serialcirc_type=".$data['circ_type'].",
				serialcirc_virtual=".$data['virtual_circ'].",
				serialcirc_simple=".$data['simple_circ'].",
				serialcirc_no_ret=".$data['no_ret_circ'].",
				serialcirc_duration=".$data['duration'].",
				serialcirc_checked=".$data['checked'].",
				serialcirc_retard_mode=".$data['retard_mode'].",
				serialcirc_allow_resa=".$data['allow_resa'].",
				serialcirc_allow_copy=".$data['allow_copy'].",
				serialcirc_allow_send_ask=".$data['allow_send_ask'].",
				serialcirc_allow_subscription=".$data['allow_subscription'].",
				serialcirc_duration_before_send=".$data['duration_before_send'].",
				serialcirc_expl_statut_circ=".$data['expl_statut_circ'].",
				serialcirc_expl_statut_circ_after=".$data['expl_statut_circ_after'];

		if(!$this->id){
			$req="INSERT INTO serialcirc SET ".$req;
			pmb_mysql_query($req);
			$this->id = pmb_mysql_insert_id();
		}else{
			if ($this->expl_in_circ($this->num_abt)) {
				// Un exemplaire est en cours de circulation, on garde le paramétrage actuel, on crée une nouvelle liste
				$query = 'UPDATE serialcirc SET num_serialcirc_abt = 0 WHERE id_serialcirc = '.$this->id;
				pmb_mysql_query($query);
				$query = "INSERT INTO serialcirc SET ".$req.",
				serialcirc_piedpage='".$this->piedpage."',
				serialcirc_tpl='".$this->tpl."'";
				pmb_mysql_query($query);
				$new_id = pmb_mysql_insert_id();
				// On rattache les diffusions
				$query = "update serialcirc_diff set num_serialcirc_diff_serialcirc = ".$new_id." where num_serialcirc_diff_serialcirc = ".$this->id;
				pmb_mysql_query($query);

				$this->id = $new_id;
			} else {
				$req = "UPDATE serialcirc SET ".$req." WHERE id_serialcirc = ".$this->id;
				pmb_mysql_query($req);
			}
		}
		$this->fetch_data();
	}

	public function empr_list_form	(){
		global $serialcirc_diff_form_empr_list;
		global $serialcirc_diff_form_empr_list_empr;
		global $serialcirc_diff_form_empr_list_group;
		global $serialcirc_diff_form_empr_list_group_elt;
		global $serialcirc_diff_form_empr_list_group_empty;
		global $msg;

		$tpl_empr_list = '';
		foreach($this->diffusion as $diff){
			if($diff['empr_type']==SERIALCIRC_EMPR_TYPE_empr){
				$tpl_empr=$serialcirc_diff_form_empr_list_empr;
				$name_elt=$this->empr_info[ $diff['empr']['id_empr']]['empr_libelle'];
			}else{
				$name_elt=$diff['empr_name'];
				$group_list_list="";
				if(count($diff['group'])){
					$tpl_empr=$serialcirc_diff_form_empr_list_group;
					$cpt=0;
					foreach($diff['group'] as $empr){
						$group_list=$serialcirc_diff_form_empr_list_group_elt;
						$group_list=str_replace('!!id_empr!!', $empr['num_empr'], $group_list);
						$group_list=str_replace('!!order!!', $cpt, $group_list);
						$resp="";
						if($empr['responsable']){
							$resp=$msg["serialcirc_group_responsable"];
						}
						$group_list=str_replace('!!empr_libelle!!',$empr['empr']['empr_libelle'].$resp, $group_list);



						$group_list=str_replace('!!empr_cpt!!', $cpt, $group_list);
						$group_list_list.=$group_list;

						$cpt++;
					}
					$tpl_empr=str_replace('!!empr_list!!', $group_list_list, $tpl_empr);
				}else {
					$tpl_empr=$serialcirc_diff_form_empr_list_group_empty;
				}
			}
			$tpl_empr=str_replace('!!id_diff!!', $diff['id'], $tpl_empr);
			$tpl_empr=str_replace('!!empr_name!!', $name_elt, $tpl_empr);
			$tpl_empr_list.=$tpl_empr;
		}
		$form=$serialcirc_diff_form_empr_list;
		if (count($this->diffusion)) {
			$fields =new serialcirc_print_fields();
			$select_field=$fields->get_sort_form("sort_field",$this->sort_diff);
			$form=str_replace('!!sort_list!!', $select_field, $form);
		} else {
			$form=str_replace('!!sort_list!!', "", $form);
		}
		$form=str_replace('!!empr_list!!', $tpl_empr_list, $form);
		return $form;
	}

	public function empr_save($id_diff,$data){
		$data['id_empr'] = intval($data['id_empr']);
		if(!$data['id_empr'])	return;
		if(!$this->id){
			$this->update_serialcirc();
		}
		$data['duration'] = intval($data['duration']);
		if(!$id_diff){
			$req="INSERT INTO serialcirc_diff SET
			num_serialcirc_diff_serialcirc=".$this->id.",
			serialcirc_diff_empr_type=".SERIALCIRC_EMPR_TYPE_empr.",
			num_serialcirc_diff_empr=".$data['id_empr'].",
			serialcirc_diff_duration=".$data['duration'].",
			serialcirc_diff_order=".count($this->diffusion)."
			";
			pmb_mysql_query($req);
		}else{
			$req="UPDATE serialcirc_diff SET
			num_serialcirc_diff_serialcirc=".$this->id.",
			serialcirc_diff_empr_type=".SERIALCIRC_EMPR_TYPE_empr.",
			num_serialcirc_diff_empr=".$data['id_empr'].",
			serialcirc_diff_duration='".$data['duration']."'
			where id_serialcirc_diff=".$id_diff."
			";
			pmb_mysql_query($req);
		}
		//print $req;
		$this->fetch_data();
		serialcirc_ask::set_inscription($this->id_perio, $data['id_empr'],$this->id);
	}

	public function group_save($id_diff,$data){
		$id_diff = intval($id_diff);
		if(!$data['group_name'])	return;
		if(!$this->id){
			$this->update_serialcirc();
		}
		$data['duration'] = intval($data['duration']);
		if(!$this->diffusion[$id_diff]){
			$req="INSERT INTO serialcirc_diff SET
			num_serialcirc_diff_serialcirc=".$this->id.",
			serialcirc_diff_empr_type=".SERIALCIRC_EMPR_TYPE_group.",
			serialcirc_diff_type_diff='".$data['type_diff']."',
			serialcirc_diff_group_name='".$data['group_name']."',
			serialcirc_diff_duration=".$data['duration'].",
			serialcirc_diff_order=".count($this->diffusion)."
			";
			pmb_mysql_query($req);
			$id_diff = pmb_mysql_insert_id();
		}else{
			$req="UPDATE serialcirc_diff SET
			num_serialcirc_diff_serialcirc=".$this->id.",
			serialcirc_diff_empr_type=".SERIALCIRC_EMPR_TYPE_group.",
			serialcirc_diff_type_diff='".$data['type_diff']."',
			serialcirc_diff_group_name='".$data['group_name']."',
			serialcirc_diff_duration='".$data['duration']."'
			where id_serialcirc_diff=".$id_diff."
			";
			pmb_mysql_query($req);
		}

		$req=" DELETE from serialcirc_group WHERE num_serialcirc_group_diff=$id_diff ";
		pmb_mysql_query($req);
		$order=0;
		if(count($data['empr_list']))
		foreach($data['empr_list'] as $id_empr){
			$req=" INSERT INTO serialcirc_group SET num_serialcirc_group_diff=$id_diff,num_serialcirc_group_empr=$id_empr,serialcirc_group_order=$order ";
			if($id_empr==$data['empr_resp'] )	$req.=", serialcirc_group_responsable=1";
			pmb_mysql_query($req);
			$order++;
		}/*
		if($data['add_type']==1 && $data['caddie_select']){// vient d'un panier
			$requete = "SELECT object_id, flag FROM empr_caddie_content where empr_caddie_id='".$data['caddie_select']."' ";
			$res = pmb_mysql_query($requete);
			while($r=pmb_mysql_fetch_object($res)){
				$req=" INSERT INTO serialcirc_group SET num_serialcirc_group_diff=$id_diff,num_serialcirc_group_empr=".$r->object_id." ,serialcirc_group_order=$order ";
				pmb_mysql_query($req);
				$order++;
			}
		}else
		*/if($data['add_type']==2 && $data['group_circ_select']){// vient d'un group
			$requete = "SELECT empr_id  FROM empr_groupe where groupe_id='".$data['group_circ_select']."' ";
			$res = pmb_mysql_query($requete);
			while($r=pmb_mysql_fetch_object($res)){
			    if(!in_array($r->empr_id, $data['empr_list'])){
    				$req=" INSERT INTO serialcirc_group SET num_serialcirc_group_diff=$id_diff,num_serialcirc_group_empr=".$r->empr_id." ,serialcirc_group_order=$order ";
    				pmb_mysql_query($req);
    				$order++;
			    }
			}
		}
		$this->fetch_data();
	}

	public function del_diff($id_diff){
		$req=" DELETE from serialcirc_group WHERE num_serialcirc_group_diff=$id_diff ";
		pmb_mysql_query($req);
		$req=" DELETE from serialcirc_diff WHERE id_serialcirc_diff=$id_diff ";
		pmb_mysql_query($req);
		$this->fetch_data();
	}


	public function delete($num_abt=0){
		if(!$num_abt)return;
		$requete="select id_serialcirc from serialcirc where num_serialcirc_abt=".$num_abt;
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
			$r = pmb_mysql_fetch_object($resultat);
			$id_serialcirc=$r->id_serialcirc;

			$serialcirl_diff = new serialcirc_diff($id_serialcirc);
			$expl_in_circ = $serialcirl_diff->expl_in_circ($num_abt);

			$requete="select id_serialcirc_diff from serialcirc_diff where num_serialcirc_diff_serialcirc=".$id_serialcirc;
			$res_diff=pmb_mysql_query($requete);
			while($r = pmb_mysql_fetch_object($res_diff)){
				$id_diff=$r->id_serialcirc_diff;

				$requete="delete from serialcirc_group where num_serialcirc_group_diff=".$id_diff;
				pmb_mysql_query($requete);
				if(!$expl_in_circ){
					$requete="delete from serialcirc_expl where num_serialcirc_expl_serialcirc_diff=".$id_diff;
					pmb_mysql_query($requete);
				}
			}

			if(!$expl_in_circ){
				$requete="delete from serialcirc_circ where num_serialcirc_circ_serialcirc=".$id_serialcirc;
				pmb_mysql_query($requete);
			}
			$requete="delete from serialcirc_diff where num_serialcirc_diff_serialcirc=".$id_serialcirc;
			pmb_mysql_query($requete);
			$requete="delete from serialcirc_ask where num_serialcirc_ask_serialcirc=".$id_serialcirc;
			pmb_mysql_query($requete);
			if(!$expl_in_circ){
				$requete="delete from serialcirc where id_serialcirc=".$id_serialcirc;
				pmb_mysql_query($requete);
			} else {
				$requete="update serialcirc set num_serialcirc_abt = 0 where id_serialcirc=".$id_serialcirc;
				pmb_mysql_query($requete);
			}
		}
	}

	// l'abonnement a encore au moins un expl en circulation
	public function expl_in_circ($num_abt){
		$requete="select num_serialcirc_expl_id from serialcirc, serialcirc_expl where num_serialcirc_expl_serialcirc=id_serialcirc and num_serialcirc_abt=".$num_abt;
		$resultat=pmb_mysql_query($requete);
		if ($nb=pmb_mysql_num_rows($resultat)) {
			return $nb;
		}
		return 0;
	}

    public static function get_caddie($id_caddie)
    {
        global $serialcirc_diff_form_group_empr_0;
        global $serialcirc_diff_form_group_empr;

        $id_caddie = intval($id_caddie);
        $empr_form_list = "";
        $empr_form = $serialcirc_diff_form_group_empr_0;
        $requete = "SELECT * FROM empr, empr_caddie_content where empr_caddie_id='" . $id_caddie . "' and object_id=id_empr";
        $res = pmb_mysql_query($requete);
        if (! $empr_count = pmb_mysql_num_rows($res)) {
            $empr_form = str_replace('!!empr_libelle!!', "", $empr_form);
            $empr_form = str_replace('!!empr_cpt!!', "0", $empr_form);
            $empr_form = str_replace('!!id_empr!!', "0", $empr_form);
            $empr_form = str_replace('!!checked!!', "", $empr_form);
            $empr_form_list = $empr_form;
        }
        $cpt = 0;
        while ($r = pmb_mysql_fetch_object($res)) {
            $empr_form = str_replace('!!id_empr!!', $r->id_empr, $empr_form);
            $empr_form = str_replace('!!empr_libelle!!', $r->empr_nom . " " . $r->empr_prenom . " (" . $r->empr_cb . " )", $empr_form);
            $empr_form = str_replace('!!checked!!', '', $empr_form);
            $empr_form = str_replace('!!empr_cpt!!', $cpt, $empr_form);
            $empr_form_list .= $empr_form;
            $empr_form = $serialcirc_diff_form_group_empr;
            $cpt ++;
        }
        $empr_form_list = str_replace('!!empr_count!!', $empr_count, $empr_form_list);
        return $empr_form_list;
    }

	public function show_form($form_ask=0,$id=0){
		global $charset;
		global $serialcirc_diff_form,$msg;

		$form=$serialcirc_diff_form;
		$script='';
		$form=str_replace('!!serialcirc_diff_form_empr_list!!', $this->empr_list_form(), $form);
		switch($form_ask){
			case '1': // empr form
				$form_type=$this->empr_form($id);
			break;
			case '2': // group form
				$form_type=$this->group_form($id);
			break;
			case '3': // group form
				$form_type=$this->option_form();
			break;
			case '4': // add new empr from ask
				$form_type=$this->empr_add_form($id);
			break;
			case '5': // Fiche de circulation
				$form_type=$this->ficheformat_form();
			break;
			default:
				$form_type=$this->option_form($id);
				$script="
				<script>
					if(document.getElementById('record_button_other'))document.getElementById('record_button_other').style.display='none';
				</script>";
		}
		$form=str_replace('!!serialcirc_diff_form_type!!', $form_type, $form);
		$form=str_replace('!!serialcirc_diff_id!!', $this->id, $form);
		$form=str_replace('!!num_abt!!', $this->num_abt, $form);
		$form=str_replace('!!perio!!',   "<a href='".$this->serial_info['serial_link']."'>".htmlentities($this->serial_info['serial_name'],ENT_QUOTES,$charset)."</a>" , $form);
		$form=str_replace('!!abt!!',   "<a href='".$this->serial_info['abt_link']."'>".htmlentities($this->serial_info['abt_name'],ENT_QUOTES,$charset)."</a>" , $form);
		$form=str_replace('!!bulletinage_see!!',   "<a href='".$this->serial_info['bulletinage_link']."'>".htmlentities($msg['link_notice_to_bulletinage'],ENT_QUOTES,$charset)."</a>" , $form);
		$form=str_replace('!!num_abt!!', $this->num_abt, $form);
		$form=str_replace('!!form_ask!!', $form_ask, $form);
		return $form.$script;
	}

	public function empr_form($id_diff=0){
		global $serialcirc_diff_form_empr,$msg;
		$form=$serialcirc_diff_form_empr;

		if($id_diff){
			$form_title=$msg["serialcirc_diff_edit_title"];
			$form=str_replace('!!empr_libelle!!', $this->diffusion[$id_diff]['empr']['empr_libelle'], $form);
			$form=str_replace('!!duration!!', $this->diffusion[$id_diff]['duration'], $form);
			$id_empr=$this->diffusion[$id_diff]['empr']['id_empr'];
		}else{
			$form_title=$msg["serialcirc_diff_add_title"];
			$form=str_replace('!!empr_libelle!!','', $form);
			$form=str_replace('!!duration!!', '', $form);
			$id_empr=0;
		}
		$form=str_replace('!!id_empr!!', $id_empr, $form);
		$form=str_replace('!!id_diff!!', $id_diff, $form);
		$form=str_replace('!!form_title!!', $form_title, $form);
		return $form;
	}

	public function empr_add_form($id_empr){
		global $serialcirc_diff_form_empr,$msg;
		$form=$serialcirc_diff_form_empr;
		$empr_info= $this->empr_info($id_empr);
		$form_title=$msg["serialcirc_diff_edit_title"];
		$form=str_replace('!!empr_libelle!!', $empr_info['empr_libelle'], $form);
		$form=str_replace('!!duration!!', '', $form);
		$id_empr=$empr_info['id_empr'];

		$form=str_replace('!!id_empr!!', $id_empr, $form);
		$form=str_replace('!!id_diff!!', $id_diff, $form);
		$form=str_replace('!!form_title!!', $form_title, $form);

		return $form;
	}
	public function group_form($id_diff=0){
		global $serialcirc_diff_form_group,$msg, $charset;
		global $serialcirc_diff_form_group_empr, $serialcirc_diff_form_group_empr_0;

		$form=$serialcirc_diff_form_group;
		if($id_diff && isset($this->diffusion[$id_diff])){
			$form_title=$msg["serialcirc_diff_edit_title"];
			$form=str_replace('!!group_name!!',$this->diffusion[$id_diff]['empr_name'], $form);
			$form=str_replace('!!duration!!', $this->diffusion[$id_diff]['duration'], $form);
			$empr_count = count($this->diffusion[$id_diff]['group']);
		}else{
			$form_title=$msg["serialcirc_diff_add_title"];
			$form=str_replace('!!group_name!!', '', $form);
			$form=str_replace('!!duration!!', '', $form);
			$empr_count = 0;
		}
		$checked="";
		if (isset($this->diffusion[$id_diff]) && $this->diffusion[$id_diff]['type_diff'])$checked=" checked='checked' ";
		$form=str_replace('!!type_diff_checked!!', $checked, $form);

		$caddie_list = empr_caddie::get_cart_list();
		$caddie_sel="";
		foreach($caddie_list as $caddie){
			$caddie_sel.="<option value=".$caddie['idemprcaddie']." onchange=''>".htmlentities($caddie['name'],ENT_QUOTES,$charset)."</option>";
		}
		$group_empr_sel="";
		$requete="select id_groupe, libelle_groupe from groupe";
		$result=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($result)) {
			while ($grp_temp=pmb_mysql_fetch_object($result)) {
				$group_empr_sel.="<option value=".$grp_temp->id_groupe." onchange=''>".htmlentities($grp_temp->libelle_groupe,ENT_QUOTES,$charset)."</option>";
			}
		}
		$empr_form_list="";
		$empr_form=$serialcirc_diff_form_group_empr_0;

		if(!$empr_count){ // Pas de lecteur associés
			$empr_form=str_replace('!!empr_libelle!!', "", $empr_form);
			$empr_form=str_replace('!!empr_cpt!!', "0", $empr_form);
			$empr_form=str_replace('!!id_empr!!',"0", $empr_form);
			$empr_form=str_replace('!!checked!!',"", $empr_form);

			$empr_form_list=$empr_form;
		}
		if($empr_count){
			$cpt=0;
			foreach($this->diffusion[$id_diff]['group'] as $empr){
				$empr_form=str_replace('!!id_empr!!', $empr['num_empr'], $empr_form);
				$empr_form=str_replace('!!empr_libelle!!',$empr['empr']['empr_libelle'], $empr_form);
				$checked="";
				if($empr['responsable']){
					$checked=" checked='checked' ";
				}
				$empr_form=str_replace('!!checked!!',$checked, $empr_form);
				$empr_form=str_replace('!!empr_cpt!!', $cpt, $empr_form);
				$empr_form_list.=$empr_form;
				$empr_form=$serialcirc_diff_form_group_empr;
				$cpt++;
			}
		}

		$form=str_replace('!!group_empr_list!!', $empr_form_list, $form);
		$form=str_replace('!!empr_count!!', $empr_count, $form);
		$form=str_replace('!!caddie_select!!', $caddie_sel, $form);
		$form=str_replace('!!group_circ_select!!', $group_empr_sel, $form);
		$form=str_replace('!!id_diff!!', $id_diff, $form);
		$form=str_replace('!!form_title!!', $form_title, $form);
		return $form;
	}

	public function ficheformat_save($data){
		$fields =new serialcirc_print_fields($this->id);
		$fields->save_form();
		$this->fetch_data();
	}

	public function ficheformat_add_field($data){
		$fields =new serialcirc_print_fields($this->id);
		$fields->add_field();
	}

	public function ficheformat_del_field($data){
		$fields =new serialcirc_print_fields($this->id);
		$fields->del_field();
	}

	public function ficheformat_change_fields($data){
		global $form_serialcirc_tpl;

		$fields =new serialcirc_print_fields($this->id);
		$fields->change_fields();
		$this->tpl = ($form_serialcirc_tpl ? $form_serialcirc_tpl : "");
	}

	public function ficheformat_form(){
		global $serialcirc_diff_form_ficheformat;
		global $msg;

		$form=$serialcirc_diff_form_ficheformat;

		if (intval($this->tpl) > 0) $selected = $this->tpl;
		else $selected = 0;
		$select_tpl=serialcirc_tpl::gen_tpl_select("form_serialcirc_tpl", $selected, "serialcirc_print_change_fields();");
		$form=str_replace('!!fiche_tpl_field_sel!!', $select_tpl, $form);
		$form=str_replace('!!fiche_tpl_id_sel!!', $selected, $form);
		if (!$selected) {
			$fields =new serialcirc_print_fields($this->id);
			$select_field=$fields->get_select_form();
			$form=str_replace('!!fiche_add_field_sel!!', $select_field, $form);

			$piedpage_tpl="
			<div class='row'>
				<label class='etiquette' for='piedpage'>".$msg['serialcirc_diff_option_form_fiche_pied_page']."</label>!!fields_options!!
				<input class='bouton' type='button' onclick=\"insert_vars(document.getElementById('fields_options'), document.getElementById('piedpage')); return false; \" value=' ".$msg['admin_authperso_insert_field']." ' >
			</div>
			<div class='row'>
				<textarea type='text' name='piedpage' id='piedpage' class='saisie-50em' rows='4' cols='50' >!!pied_page!!</textarea>
			</div>";

			$fields_options="<select id='fields_options' name='fields_options'>";
			$fields_options.="<option value='{{last_empr.nom}}'>".$msg['serialcirc_fiche_circu_last_empr_first_name']."</option>";
			$fields_options.="<option value='{{last_empr.prenom}}'>".$msg['serialcirc_fiche_circu_last_empr_last_name']."</option>";
			$fields_options.="<option value='{{last_empr.empr_libelle}}'>".$msg['serialcirc_fiche_circu_last_empr_lib']."</option>";
			$fields_options.="<option value='{{last_empr.mail}}'>".$msg['serialcirc_fiche_circu_last_empr_mail']."</option>";
			$fields_options.="<option value='{{last_empr.cb}}'>".$msg['serialcirc_fiche_circu_last_empr_cb']."</option>";
			$fields_options.="<option value='{{expl.cb}}'>".$msg['serialcirc_fiche_circu_bull_cb']."</option>";
			$fields_options.="<option value='{{expl.numero}}'>".$msg['serialcirc_fiche_circu_bull_num']."</option>";
			$fields_options.="<option value='{{expl.bulletine_date}}'>".$msg['serialcirc_fiche_circu_bull_date']."</option>";
			$fields_options.="<option value='{{expl.serial_title}}'>".$msg['serialcirc_fiche_circu_bull_serialname']."</option>";
			$fields_options.="<option value='{{expl.expl_location_name}}'>".$msg['serialcirc_fiche_circu_bull_location']."</option>";
			$fields_options.="<option value='{{expl.expl_cote}}'>".$msg['serialcirc_fiche_circu_bull_cote']."</option>";
			$fields_options.="<option value='{{expl.expl_owner}}'>".$msg['serialcirc_fiche_circu_bull_owner']."</option>";
			$fields_options.="</select>";
			$piedpage_tpl=str_replace('!!fields_options!!', $fields_options, $piedpage_tpl);
			$piedpage_tpl=str_replace('!!pied_page!!', $this->piedpage, $piedpage_tpl);
			$form=str_replace('!!piedpageform!!', $piedpage_tpl, $form);

		} else {
			$form=str_replace('!!fiche_add_field_sel!!', '', $form);
			$form=str_replace('!!piedpageform!!', '', $form);
		}
		return $form;
	}

	public function option_save($data){
		$this->update_serialcirc($data);

	}

	public function option_form(){
		global $serialcirc_diff_form_option;

		$form=$serialcirc_diff_form_option;

		if($this->circ_type == 0){
			$form=str_replace("!!circ_type_checked_0!!"," checked='checked' ",$form);
			$form=str_replace("!!circ_type_checked_1!!","",$form);
		}else{
			$form=str_replace("!!circ_type_checked_0!!","",$form);
			$form=str_replace("!!circ_type_checked_1!!"," checked='checked' ",$form);
		}
		$form=str_replace("!!circ_type_checked_".$this->circ_type."!!"," checked='checked' ",$form);
		//circ vituelle
		if($this->virtual_circ)$checked=" checked='checked' "; else $checked="";
		$form=str_replace("!!virtual_checked!!", $checked,$form);
		// circ simplifiée
		if($this->simple_circ || !$this->id) $checked=" checked='checked' "; else $checked="";
		$form=str_replace("!!simple_circ_checked!!", $checked,$form);

		if($this->no_ret_circ)$checked=" checked='checked' "; else $checked="";
		$form=str_replace("!!no_ret_circ_checked!!", $checked,$form);

		if($this->virtual_circ) $display='block'; else $display='none';
		$form=str_replace("!!display_virtual_circ_part!!",$display,$form);

		$form=str_replace("!!duration!!",$this->duration,$form);

		//$form=str_replace("!!retard_mode_checked_".$this->retard_mode."!!"," checked='checked' ",$form);
		if($this->retard_mode == 0){
			$form=str_replace("!!retard_mode_checked_0!!"," checked='checked' ",$form);
			$form=str_replace("!!retard_mode_checked_1!!","",$form);
		}else{
			$form=str_replace("!!retard_mode_checked_0!!","",$form);
			$form=str_replace("!!retard_mode_checked_1!!"," checked='checked' ",$form);
		}

		if($this->checked)$checked=" checked='checked' "; else $checked="";
		$form=str_replace("!!checked_checked!!",$checked,$form);

		if($this->allow_resa)$checked=" checked='checked' "; else $checked="";
		$form=str_replace("!!allow_resa_checked!!",$checked,$form);

		if($this->allow_copy)$checked=" checked='checked' "; else $checked="";
		$form=str_replace("!!allow_copy_checked!!",$checked,$form);

		if($this->allow_send_ask)$checked=" checked='checked' "; else $checked="";
		$form=str_replace("!!allow_send_ask_checked!!",$checked,$form);

		if($this->allow_subscription)$checked=" checked='checked' "; else $checked="";
		$form=str_replace("!!allow_subscription_checked!!",$checked,$form);

		$form=str_replace("!!duration_before_send!!",$this->duration_before_send,$form);

		$form=str_replace('!!expl_statut_circ!!',	do_selector('docs_statut', 'expl_statut_circ', $this->expl_statut_circ),$form);
		$form=str_replace('!!expl_statut_circ_after!!',	do_selector('docs_statut', 'expl_statut_circ_after', $this->expl_statut_circ_after),$form);

		return $form;
	}

    public static function up_order_circdiff($tablo)
    {
        $liste = explode(",", $tablo);
        if ($liste[0]) {
            // réinitialisation du tri
            $rqt = "select num_serialcirc_diff_serialcirc from serialcirc_diff where id_serialcirc_diff='" . $liste[0] . "'";
            $res = pmb_mysql_query($rqt);
            if ($res) {
                $id_serialcirc = pmb_mysql_result($res, 0, "num_serialcirc_diff_serialcirc");
                if ($id_serialcirc) {
                    $req = " UPDATE serialcirc SET serialcirc_sort_diff='' WHERE id_serialcirc=" . $id_serialcirc;
                    pmb_mysql_query($req);
                }
            }
        }
        for ($i = 0; $i < count($liste); $i ++) {
            $rqt = "update serialcirc_diff set serialcirc_diff_order='" . $i . "' where id_serialcirc_diff='" . $liste[$i] . "' ";
            pmb_mysql_query($rqt);
        }
    }

    public static function up_order_circdiffprint($id_serialcirc, $tablo)
    {
        $fields = new serialcirc_print_fields($id_serialcirc);
        $fields->up_order($tablo);
    }

	public function duplicate($abt_to_id){
		$abt_to_id = intval($abt_to_id);
		$requete="select id_serialcirc from serialcirc where num_serialcirc_abt=".$abt_to_id;
		$resultat=pmb_mysql_query($requete);
		if (pmb_mysql_num_rows($resultat)) {
		    $status = $this->delete($abt_to_id);

			if($status)return $status;// error
		}

		$req="INSERT INTO serialcirc SET
			num_serialcirc_abt=".$abt_to_id.",
			serialcirc_type=".$this->circ_type.",
			serialcirc_virtual=".$this->virtual_circ.",
			serialcirc_simple=".$this->simple_circ.",
			serialcirc_no_ret=".$this->no_ret_circ.",
			serialcirc_duration=".$this->duration.",
			serialcirc_checked=".$this->checked.",
			serialcirc_retard_mode=".$this->retard_mode.",
			serialcirc_allow_resa=".$this->allow_resa.",
			serialcirc_allow_copy=".$this->allow_copy.",
			serialcirc_allow_send_ask=".$this->allow_send_ask.",
			serialcirc_allow_subscription=".$this->allow_subscription.",
			serialcirc_duration_before_send=".$this->duration_before_send.",
			serialcirc_expl_statut_circ=".$this->expl_statut_circ.",
			serialcirc_piedpage='".$this->piedpage."',
			serialcirc_tpl='".$this->tpl."',
			serialcirc_expl_statut_circ_after=".$this->expl_statut_circ_after."
			";
		pmb_mysql_query($req);
		$new_serialcirc_id = pmb_mysql_insert_id();

		$requete="select * from serialcirc_diff where num_serialcirc_diff_serialcirc=".$this->id." order by serialcirc_diff_order";
		$res_empr=pmb_mysql_query($requete);
		while($r_empr=pmb_mysql_fetch_object($res_empr)){
			$req="INSERT INTO serialcirc_diff SET
				num_serialcirc_diff_serialcirc=".$new_serialcirc_id.",
				serialcirc_diff_empr_type='".$r_empr->serialcirc_diff_empr_type."',
				serialcirc_diff_type_diff='".$r_empr->serialcirc_diff_type_diff."',
				num_serialcirc_diff_empr='".$r_empr->num_serialcirc_diff_empr."',
				serialcirc_diff_group_name='".$r_empr->serialcirc_diff_group_name."',
				serialcirc_diff_duration='".$r_empr->serialcirc_diff_duration."',
				serialcirc_diff_order=".$r_empr->serialcirc_diff_order."
			";
			pmb_mysql_query($req);
			$new_id_serialcirc_diff = pmb_mysql_insert_id();

			$requete="select * from serialcirc_group where num_serialcirc_group_diff=".$r_empr->id_serialcirc_diff ;
			$res_group=pmb_mysql_query($requete);
			while($r_group=pmb_mysql_fetch_object($res_group)){
				$req="INSERT INTO serialcirc_group SET
					num_serialcirc_group_diff=".$new_id_serialcirc_diff.",
					num_serialcirc_group_empr='".$r_group->num_serialcirc_group_empr."',
					serialcirc_group_responsable='".$r_group->serialcirc_group_responsable."',
					serialcirc_group_order='".$r_group->serialcirc_group_order."'
				";
				pmb_mysql_query($req);

			}
		}
	}

	public static function up_order_circdiffgroupdrop($tablo)
    {
        $liste = explode(",", $tablo);
        for ($i = 0; $i < count($liste); $i ++) {
            $ids = explode("_", $liste[$i]);
            $goup_id = $ids[0];
            $empr_id = $ids[1];
            $rqt = "update serialcirc_group set serialcirc_group_order='" . $i . "' where num_serialcirc_group_diff='" . $goup_id . "' and num_serialcirc_group_empr=$empr_id";
            pmb_mysql_query($rqt);
        }
    }

	public function sort_diff(){
		global $sort_field;

		if (!$this->id) return;

		$flag_get_value_from_database = false;
		if (!$sort_field) {
			$rqt = "select serialcirc_sort_diff from serialcirc where id_serialcirc=".$this->id;
			$res = pmb_mysql_query($rqt);
			$sort_field = pmb_mysql_result($res, 0, "serialcirc_sort_diff");
			$flag_get_value_from_database = true;
		}
		$select_order = "serialcirc_diff_order";
		$join = "";
		if ($sort_field) {
			if (substr($sort_field, 0, 2) != "pp") {
				switch($sort_field) {
					case "emprlibelle" :
						$select_order = "concat (empr_nom,' ',empr_prenom) as empr_nom_prenom";
						break;
					case "name" :
						$select_order = "empr_nom";
						break;
					case "cb" :
						$select_order = "empr_cb";
						break;
					case "adr1" :
						$select_order = "empr_adr1";
						break;
					case "adr2" :
						$select_order = "empr_adr2";
						break;
					case "tel1" :
						$select_order = "empr_tel1";
						break;
					case "tel2" :
						$select_order = "empr_tel2";
						break;
					case "ville" :
						$select_order = "empr_ville";
						break;
				}
			} else {
				$p_perso = new parametres_perso("empr");
				$select_order = $p_perso->prefix."_custom_".$p_perso->t_fields[substr($sort_field, 3, 1)]["DATATYPE"];
				$join = "left join ".$p_perso->prefix."_custom_values on ".$p_perso->prefix."_custom_origine=id_empr and ".$p_perso->prefix."_custom_champ=".substr($sort_field, 3, 1);

			}
		}
		$tab_sort = array();
		$requete = "select id_serialcirc_diff, $select_order from serialcirc_diff join empr on num_serialcirc_diff_empr=id_empr $join where num_serialcirc_diff_serialcirc=".$this->id;
		$result = pmb_mysql_query($requete);
		while ($row = pmb_mysql_fetch_array($result)) {
			$tab_sort[$row["id_serialcirc_diff"]] = strtolower($row[1]);
		}
		$requete = "select id_serialcirc_diff, $select_order from serialcirc_diff join serialcirc_group on id_serialcirc_diff=num_serialcirc_group_diff join empr on num_serialcirc_group_empr=id_empr $join where num_serialcirc_diff_serialcirc=".$this->id;
		$result = pmb_mysql_query($requete);
		while ($row = pmb_mysql_fetch_array($result)) {
			$tab_sort[$row["id_serialcirc_diff"]] = strtolower($row[1]);
		}
		asort($tab_sort);

		$no_ordre = 0;
		foreach ($tab_sort as $key=>$value) {
			$req="UPDATE serialcirc_diff SET
					serialcirc_diff_order = ".$no_ordre."
				WHERE id_serialcirc_diff=".$key."
				AND num_serialcirc_diff_serialcirc=".$this->id;
			pmb_mysql_query($req);
			$no_ordre++;
		}

		if(!$flag_get_value_from_database) {
			//on met à jour le tri choisi dans la table serialcirc
			$query = "update serialcirc set serialcirc_sort_diff='".$sort_field."' where id_serialcirc=".$this->id;
			pmb_mysql_query($query);
		}

		$this->fetch_data();
	}

	public function get_start_diff_id() {
	    if(!empty($this->diffusion)) {
	        foreach($this->diffusion as $diff) {
	            if($diff['num_empr']) {
	                return $diff['num_empr'];
	            }
	        }
	    }
	    return 0;
	}

	public static function get_instance($id_serialcirc=0,$num_abt=0) {
		if(!isset(static::$instances[$id_serialcirc][$num_abt])) {
			static::$instances[$id_serialcirc][$num_abt] = new serialcirc_diff($id_serialcirc, $num_abt);
		}
		return static::$instances[$id_serialcirc][$num_abt];
	}
} //serialcirc class end