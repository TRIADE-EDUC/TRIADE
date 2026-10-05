<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: readers_relances_controller.class.php,v 1.7 2022/12/06 15:29:39 dbellamy Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $class_path;
require_once($class_path."/readers/readers_controller.class.php");
require_once($class_path."/relance.class.php");

class readers_relances_controller extends readers_controller {
	
	protected static $model_class_name = 'relance';
	
	protected static $list_ui_class_name = 'list_readers_relances_ui';

	protected static $empr;
		
	public static function get_query() {
		$requete = "select id_empr from empr, pret, exemplaires where 1 ";
		$requete.=" and id_empr in (".implode(",",static::$empr).") ";
		//$requete.= $loc_filter;
		$requete.= "and pret_retour<CURDATE() and pret_idempr=id_empr and pret_idexpl=expl_id group by id_empr";
		return $requete;
	}
	
	public static function get_relances_printed() {
	    $relances_printed = array();
	    $relances = static::get_relances();
	    foreach ($relances as $relance) {
	        if($relance['printed']) {
	            $relances_printed[] = $relance;
	        }
	    }
	    return $relances_printed;
	}
	
	public static function get_relances_not_printed() {
	    $relances_not_printed = array();
		$relances = static::get_relances();
		foreach ($relances as $relance) {
		    if(!$relance['printed']) {
		        $relances_not_printed[] = $relance;
		    }
		}
		return $relances_not_printed;
	}
	
	public static function get_relances() {
		$relances = array();
		$requete = static::get_query();
		$resultat=pmb_mysql_query($requete);
		while ($r=pmb_mysql_fetch_object($resultat)) {
			$amende=new amende($r->id_empr);
			$level=$amende->get_max_level();
			$niveau_min=$level["level_min"];
			$printed=$level["printed"];
			if ($niveau_min) {
				$relances[] = array(
						'id_empr' => $r->id_empr,
						'niveau_min' => $niveau_min,
				        'printed' => $printed
				);
			}
		}
		return $relances;
	}
	
	public static function get_print_form($empr_ids=array()) {
		$form = "
			<form name='print_empr_ids' action='pdf.php?pdfdoc=lettre_retard' target='lettre' method='post'>
			";
		for ($i=0; $i<count($empr_ids); $i++) {
			$form .= "<input type='hidden' name='empr_print[]' value='".$empr_ids[$i]."'/>";
		}
		$form .= "	<script>openPopUp('','lettre');
				document.print_empr_ids.submit();
				</script>
			</form>
			";
		return $form;
	}
	
	public static function proceed($id=0) {
		global $act;
		
		switch ($act) {
			case 'solo':     //Valider  une ligne 
				relance::do_action(static::$id_empr);
				break;
			case 'solo_print':   //Imprimer une ligne
				print_relance(static::$id_empr,false);
				break;
			case 'solo_mail':    //Envoyer un mail pour une ligne
				print_relance(static::$id_empr);
				break;
			case 'valid':    //Valider toutes les actions
				foreach (static::$empr as $id_empr) {
					relance::do_action($id_empr);
				}
				break;
			case 'print':    //Impression mails non envoyés
				static::proceed_print();
				break;
			case 'reprint':  //Réimprimer les relances
			    static::proceed_reprint();
			    break;
			case 'print_letters': // ??
				static::proceed_print_letters();
				break;
			case 'print_letters_mails': // ??
				static::proceed_print_letters_mails();
				break;
			case 'export': // ??
				static::proceed_export();
				break;
			case 'print_not_sended':
				//static::proceed_print_not_sended();
				break;
			case 'send_not_sended':
				//static::proceed_send_not_sended();
				break;
			case 'export_csv':   //Export CSV
				static::proceed_export_csv();
				break;
			case 'raz_printed':  // RAZ des impressions du
				static::proceed_raz_printed();
				break;
			default:
				parent::proceed($id);
				$list_readers_relances_ui = static::get_list_ui_instance();
				$nb_relances = count($list_readers_relances_ui->get_objects());
				print "<script type='text/javascript'>document.getElementById('nb_relance_to_do').innerHTML='(".$nb_relances.")';</script>";
				break;
		}
	}
	
	public static function proceed_print() {
		global $mailretard_priorite_email;
		global $mail_sended;
		
		$not_all_mail = array();
		if (is_array(static::$empr) && count(static::$empr)) {
			$not_mail = 0;
			$mail_sended = 0;
			$relances = static::get_relances_not_printed();
			foreach ($relances as $relance) {
				$not_mail = print_relance($relance['id_empr']);
				if (($not_mail == 1) || (!$mail_sended) ||($mailretard_priorite_email==2 && $relance['niveau_min'] < 3)) { //mail_sended <=> globale
					$not_all_mail[] = $relance['id_empr'];
				}
			}
		}
		
		if (count($not_all_mail) > 0) {
			print static::get_print_form($not_all_mail);
		}
		//Fermeture de la fenêtre d'impression si tout est parti par mail
	}
	
    public static function proceed_reprint() {
        $to_reprint = array();
        if (is_array(static::$empr) && count(static::$empr)) {
            $relances = static::get_relances_printed();
            foreach ($relances as $relance) {
                if($relance['printed'] == 2) {
                    $to_reprint[] = $relance['id_empr'];
                }
            }
        }
        if (count($to_reprint) > 0) {
            print static::get_print_form($to_reprint);
        }
    }
	
	public static function proceed_print_letters() {
		$empr_ids = array();
		if (is_array(static::$empr) && count(static::$empr)) {
			$relances = static::get_relances();
			foreach ($relances as $relance) {
				$empr_ids[] = $relance['id_empr'];
			}
		}
		if (count($empr_ids) > 0) {
			print static::get_print_form($empr_ids);
		}
	}
	
	public static function proceed_print_letters_mails() {
		$empr_ids = array();
		if (is_array(static::$empr) && count(static::$empr)) {
			//TODO - récupérer les relances en suivant le paramétrage
// 			$relances = static::get_relances();
// 			foreach ($relances as $relance) {
// 				$empr_ids[] = $relance['id_empr'];
// 			}
		}
		if (count($empr_ids) > 0) {
			print static::get_print_form($empr_ids);
		}
	}
	
	public static function proceed_export() {
		$not_all_mail = array();
		if (is_array(static::$empr) && count(static::$empr)) {
			$req="TRUNCATE TABLE cache_amendes";
			pmb_mysql_query($req);
			$requete = static::get_query();
			$resultat=pmb_mysql_query($requete);
			while ($r=pmb_mysql_fetch_object($resultat)) {
				$amende=new amende($r->id_empr);
				$level=$amende->get_max_level();
				$niveau_min=$level["level_min"];
				$printed=$level["printed"];
				if ((!$printed)&&($niveau_min)) {
					$not_mail = print_relance($r->id_empr);
					if ($not_mail == 1) {
						$not_all_mail[] = $r->id_empr;
					}
				}
			}
		}
		
		if (count($not_all_mail) > 0) {
			static::set_empr($not_all_mail);
			static::proceed_export_csv();
		}
		//Fermeture de la fenêtre d'impression si tout est parti par mail
	}
	
	public static function proceed_export_csv() {
		if (is_array(static::$empr) && count(static::$empr)) {
			print "<form name='print_empr_ids' action='./circ/relance/relance_export.php';' target='lettre' method='post'>";
			for ($i=0; $i<count(static::$empr); $i++) {
				print "<input type='hidden' name='empr_export[]' value='".static::$empr[$i]."'/>";
			}
			print "<script>openPopUp('','lettre');
        			document.print_empr_ids.submit();
        			</script>
        		</form>";
		}
	}
	
	public static function proceed_print_not_sended() {
		if (is_array(static::$empr) && count(static::$empr)) {
			$empr_ids = array();
			$relances = static::get_relances_not_printed();
			foreach ($relances as $relance) {
				print_relance($relance['id_empr'], false);
				$empr_ids[] = $relance['id_empr'];
			}
			if(count($empr_ids)) {
				print static::get_print_form($empr_ids);
			}
		}
	}
	
	public static function proceed_send_not_sended() {
		static::proceed_print();
	}
	
	public static function proceed_raz_printed() {
		global $printed_cd;
		
		$req="TRUNCATE TABLE cache_amendes";
		pmb_mysql_query($req);
		$requete="update pret set printed=0 where printed!=0";
		if ($printed_cd) {
			$requete.=" and date_relance='".stripslashes($printed_cd)."'";
		}
		pmb_mysql_query($requete);
	}
	
	public static function set_empr($empr) {
		static::$empr = $empr;
	}
}