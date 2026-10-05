<?php
// +-------------------------------------------------+
// | 2002-2011 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: recouvr_reader_controller.class.php,v 1.1.4.2 2025/05/30 08:11:48 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

class recouvr_reader_controller extends lists_controller {
	
	protected static $list_ui_class_name = 'list_recouvr_reader_ui';
	
	protected static $id_empr;
	
	public static function get_empr_informations() {
	    global $charset;
	    
	    $empr=new emprunteur(static::$id_empr,'', FALSE, 0);
	    return "
    	<div class='row'>
    		<div class='colonne3'>
    			<div class='row'>".htmlentities($empr->adr1, ENT_QUOTES, $charset)."</div>
    			<div class='row'>".htmlentities($empr->adr2, ENT_QUOTES, $charset)."</div>
    			<div class='row'>".htmlentities($empr->cp." ".$empr->ville, ENT_QUOTES, $charset)."</div>
    			<div class='row'>".htmlentities($empr->mail, ENT_QUOTES, $charset)."</div>
    		</div>
    		<div class='colonne_suite'>
    			<div class='row'>".htmlentities($empr->tel1, ENT_QUOTES, $charset)."</div>
    			<div class='row'>".htmlentities($empr->tel2, ENT_QUOTES, $charset)."</div>
    		</div>
    	</div>";
	}
	
	public static function show_recouvr_form($recouvr_id) {
	    global $msg, $charset;
	    global $id_empr;
	    
	    $recouvr_id = intval($recouvr_id);
	    $libelle = '';
	    $montant = '';
	    if ($recouvr_id) {
	        $requete = "select libelle,montant from recouvrements where recouvr_id=$recouvr_id";
	        $resultat = pmb_mysql_query($requete);
	        if (pmb_mysql_num_rows($resultat)) {
	            $r = pmb_mysql_fetch_object($resultat);
	            $libelle = $r->libelle;
	            $montant = $r->montant;
	        }
	    }
	    print "
    	<form class='form-circ' name='recouvr_reader_form' method='post' action='./circ.php?categ=relance&sub=recouvr&act=recouvr_reader&id_empr=$id_empr'>
    		<h3><a href='./circ.php?categ=pret&id_empr=$id_empr'>".emprunteur::get_name($id_empr, 1)."</a></h3>
    		<div class='form-contenu'>
    			".static::get_empr_informations()."
    			<input type='hidden' name='act_line' value=''/>
    			<input type='hidden' name='recouvr_id' value=''/>
    	        <div class='row'></div>
    	    	<div class='row'>
    	    	    <div class='row'>
    	    	        <label for='libelle'>".$msg["relance_recouvrement_libelle"]."</label>
    	            </div>
    	            <div class='row'>
    	        		<textarea rows='5' cols='30' wrap='virtual' name='libelle' id='libelle'>".htmlentities($libelle, ENT_QUOTES, $charset)."</textarea>
    	        	</div>
    	    	    <div class='row'>
    	        		<label for='montant'>".$msg["relance_recouvrement_montant"]."</label>
    	        	</div>
    	        	<div class='row'>
    	        		<input name='montant' value='".$montant."' class='saisie-10em' id='montant'/>
    	        	</div>
    	        </div>
    	        <div class='row'></div>
    		</div>
    		<!--boutons -->
    		<div class='row'>
                <input type='button' value='".$msg["76"]."' class='bouton' onClick=\"this.form.submit();\"/>
    			<input type='submit' value='".$msg["77"]."' class='bouton' onClick=\"this.form.act_line.value='rec_update_line'; this.form.recouvr_id.value='".$recouvr_id."'\"/>
    		</div>
    	</form>";
	    
	}
	
    public static function proceed($id=0) {
        global $act_line;
        global $msg, $act_line;
        global $libelle, $montant, $recouvr_ligne;
        
        $id = intval($id);
        switch ($act_line) {
            case "update_line":
                static::show_recouvr_form($id);
                break;
            case "rec_update_line":
                if ($id) {
                    $query = "update recouvrements set libelle='".addslashes($libelle)."', montant='".addslashes($montant)."' where recouvr_id=$id";
                    pmb_mysql_query($query);
                } else {
                    $query = "insert into recouvrements (empr_id, date_rec, libelle, montant) values(".static::$id_empr.",now(),'".addslashes($libelle)."','".addslashes($montant)."')";
                    pmb_mysql_query($query);
                }
                static::redirect_display_list();
                break;
            case "del_line":
                if (!empty($recouvr_ligne) && is_countable($recouvr_ligne)) {
                    for ($i=0; $i<count($recouvr_ligne); $i++) {
                        $query = "delete from recouvrements where recouvr_id=".intval($recouvr_ligne[$i]);
                        pmb_mysql_query($query);
                    }
                }
                //Vérification qu'il reste des lignes
                $query = "select count(*) from recouvrements where empr_id='".static::$id_empr."'";
                $result = pmb_mysql_query($query);
                if (pmb_mysql_result($result,0,0)) {
                    static::redirect_display_list();
                } else {
                    print "<script>document.location='./circ.php?categ=relance&sub=recouvr&act=recouvr_liste';</script>";
                }
                break;
            case "solde":
                $query = "select sum(montant) from recouvrements where empr_id='".static::$id_empr."'";
                $result = pmb_mysql_query($query);
                $solde = pmb_mysql_result($result,0,0);
                if ($solde) {
                    //Crédit du compte lecteur
                    $compte_id=comptes::get_compte_id_from_empr(static::$id_empr,2);
                    if ($compte_id) {
                        $cpte=new comptes($compte_id);
                        $id_transaction=$cpte->record_transaction("",$solde,1,$msg["relance_recouvrement_solde_recouvr"],0);
                        if ($id_transaction) {
                            $cpte->validate_transaction($id_transaction);
                            
                            //Débit du compte bibliothèque
                            // ?? Jamais exécutée ?
//                             $requete="insert into transactions (compte_id,user_id,user_name,machine,date_enrgt,date_prevue,date_effective,montant,sens,realisee,commentaire,encaissement)
//         					values(
//         						0,$PMBuserid,'".$PMBusername."','".$_SERVER["REMOTE_ADDR"]."',now(),now(),now(),
//         						$solde,-1,1,'".sprintf($msg["relance_recouvrement_solde_recouvr_bibli"],$id_empr)."',0)";
                        }
                    }
                }
                pmb_mysql_query("delete from recouvrements where empr_id='".static::$id_empr."'");
                print "<script>document.location='./circ.php?categ=relance&sub=recouvr&act=recouvr_liste';</script>";
                break;
            default:
                print "<h3><a href='./circ.php?categ=pret&id_empr=".static::$id_empr."'>".emprunteur::get_name(static::$id_empr, 1)."</a></h3>";
                print static::get_empr_informations();
                
                //Liste des recouvrements
                $list_ui_instance = static::get_list_ui_instance(array('id_empr' => static::$id_empr));
                print $list_ui_instance->get_display_list();
                break;
        }
    }

    public static function set_id_empr($id_empr) {
        static::$id_empr = intval($id_empr);
    }
    
    public static function get_url_base() {
        return parent::get_url_base()."&act=recouvr_reader&id_empr=".static::$id_empr;
    }
}