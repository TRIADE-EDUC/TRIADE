<?php
// +-------------------------------------------------+
// ï¿½ 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: encaissement.php,v 1.20.2.1.2.2 2025/04/03 09:49:37 dgoron Exp $

global $base_path, $current_alert, $base_path, $class_path, $id_compte, $pmb_printer_name, $raspberry_ip_to_call, $print_script;
global $msg, $print_script, $act, $f_payment_method, $transactype_total, $transactype_id, $quantity, $trans, $typ_special;
global $commentaire, $credit_perte, $PMBuserid, $PMBusername, $dec_perte, $val_transactions;

//Liste des transactions d'un compte
$base_path = "..";
$current_alert = "circ";

require_once("../includes/init.inc.php");
require_once("$base_path/classes/comptes.class.php");
require_once($class_path."/transaction/transaction.class.php");
require_once($class_path . '/transaction/transaction_payment_method_list.class.php');

$cpte=new comptes($id_compte);
if ($cpte->error) {
	print pmb_bidi($cpte->error_message);
	exit;
} 

function back_to_list() {
	global $show_transactions, $date_debut,$id_compte;
	
	print "<script type='text/javascript' >document.location=\"list_transactions.php?show_transactions=$show_transactions&date_debut=".rawurlencode(stripslashes($date_debut))."&id_compte=$id_compte\";</script>";
}

function back_to_main() {
		global $show_transactions, $date_debut,$id_compte,$cpte;
	
		print "<script type='text/javascript' >parent.document.location=\"../circ.php?categ=pret&sub=compte&id=".$cpte->get_empr_from_compte_id()."&typ_compte=".$cpte->typ_compte."&show_transactions=$show_transactions&date_debut=".rawurlencode(stripslashes($date_debut))."\";</script>";
}

function encaisse_form($with_validated=false, $transacash_num=0) {
	global $id_compte,$solde,$date_debut,$val_transactions,$somme,$cpte,$charset;
	global $pmb_gestion_devise,$msg,$charset;
	global $show_transactions;
	
	$solde=$cpte->get_solde();
	if ($solde<0) {
		print "
        <table role='presentation'>
            <tr>
                <td style='text-align:right'>".$msg["finance_enc_montant_valide"]." : </td>
                <td style='text-align:right'>".comptes::format($somme*(-1))."</td>
            </tr>
        ";
		if ($solde<=0) {
		    print "
            <tr class='erreur'>
                <td style='text-align:right'>".$msg["finance_enc_montant_a_enc"]." : </td>";
		} else {
		    print "
            <tr>
                <td>".$msg["finance_enc_compte_cred"]." : </td>";
		}
		print "<td style='text-align:right'>";
		print comptes::format($solde*(-1));		
		print "</td>
            </tr>
        </table>
        <script type='text/javascript' >
            function check_somme(f) {
    			message='';
    			if (isNaN(f.somme.value)) {
    				message='".addslashes($msg["finance_enc_nan"])."';
    			} else {
    				if (f.somme.value<=0)
    					message='".addslashes($msg["finance_enc_mnt_neg"])."';
    			}
    			if (message) {
    				alert(message);
    				return false;
    			} else return true;
    		}
		</script>
        <form name='form_encaissement' action='encaissement.php?id_compte=$id_compte&show_transactions=$show_transactions&date_debut=".rawurlencode(stripslashes($date_debut))."' method='post'>
    		<input type='hidden' name='act' value='enc'/>
    		<input type='hidden' name='transacash_num' value='$transacash_num'/>
    		<input type='hidden' name='val_transactions' value=\"".htmlentities($val_transactions,ENT_QUOTES,$charset)."\"/>".
    		htmlentities($msg['finance_mnt_percu'], ENT_QUOTES, $charset)."&nbsp;<input type='text' value='".$solde*(-1)."' name='somme' class='saisie-5em' style='text-align:right'>&nbsp;".$pmb_gestion_devise."
            " . transaction_payment_method_list::get_selector() . "
    		<input type='submit' value='".$msg["finance_but_enc"]."' class='bouton' onClick=\"return check_somme(this.form)\"/>&nbsp;<input type='button' value='".$msg["76"]."' class='bouton' onClick=\"document.form_encaissement.act.value=''; document.form_encaissement.submit();\"/>
		</form>
		";
	} else {
		back_to_main();
	}
}

function special_form() {
    global $id_compte, $date_debut, $val_transactions, $cpte, $msg, $pmb_gestion_devise, $show_transactions;
	
	print "<script type='text/javascript' >
		function check_somme(f) {
			message='';
			if (isNaN(f.somme.value)) {
				message='".addslashes($msg["finance_enc_nan"])."';
			} else {
				if (f.somme.value<=0)
					message='".addslashes($msg["finance_enc_mnt_neg"])."';
			}
			if (message) {
				alert(message);
				return false;
			} else return true;
		}
	</script>";
	print "<form name='form_special' action='encaissement.php?id_compte=$id_compte&show_transactions=$show_transactions&date_debut=".rawurlencode(stripslashes($date_debut))."' method='post'>
		<h3>".$msg["finance_but_cred"]."</h3>
        <input type='hidden' name='act' value='enc_special'/>
        <div class='row'>
            ".$msg["finance_montant"]." <input type='text' value='' name='somme' class='saisie-5em' style='text-align:right'>&nbsp;".$pmb_gestion_devise."
            " . transaction_payment_method_list::get_selector() . "
        </div>
        <div class='row'>
    		<input type='radio' value='1' name='typ_special' id='typ_special_1' checked>&nbsp;<label for='typ_special_1'>".$msg["finance_enc_spe_crediter"]."&nbsp;<input type='checkbox' name='credit_perte' value='1'>&nbsp;".$msg["finance_enc_spe_perte"]."</label><br /><input type='radio' value='2' name='typ_special' id='typ_special_2'>&nbsp;<label for='typ_special_2'>".$msg["finance_enc_debiter"]."</label><br />
    		<input type='radio' value='3' name='typ_special' id='typ_special_3'>&nbsp;<label for='typ_special_3'>".$msg["finance_enc_crediter_enc"]."</label><br /><input type='radio' value='4' name='typ_special' id='typ_special_4'>&nbsp;<label for='typ_special_4'>".$msg["finance_enc_debiter_enc"]." <input type='checkbox' name='dec_perte' value='1'>&nbsp;".$msg["finance_enc_spe_perte"]."</label><br />
        </div>
        <div class='row'>
            ".$msg["finance_enc_raison"]."<br />
            <textarea cols='80' rows='2' wrap='virtual' name='commentaire'></textarea>
        </div>
        <div class='row'>
            <input type='submit' value='".$msg["finance_enc_valider"]."' class='bouton' onClick=\"return check_somme(this.form)\" />&nbsp;
            <input type='button' value='".$msg["76"]."' class='bouton' onClick=\"document.form_special.act.value=''; document.form_special.submit();\" />
        </div>
		</form>
		";
}

if($pmb_printer_name) {
	
	$raspberry_ip_to_call = '';
	$tmp_pmb_printer_name = explode('@', $pmb_printer_name);
	if (isset($tmp_pmb_printer_name[1])) {
		$raspberry_ip_to_call = $tmp_pmb_printer_name[1];
	}
	
	$print_script="
		<div id='printer_script'></div>
			<script type='text/javascript'>
				function printer_get_jzebra() {
					if(!document.jzebra) {
						var req = new http_request();
						req.request('$base_path/ajax.php?module=circ&categ=zebra_print_pret&sub=get_script');
						document.getElementById('printer_script').innerHTML=req.get_text();
						return false;
					}
				}
				
				function printer_jzebra_send_ticket(text,printer,encoding) {
					var applet = document.jzebra;
					var found=false;
					if(applet!=null) {
						applet.findPrinter(printer);
						while (!applet.isDoneFinding()) {}
						if(printer == applet.getPrinter()) {
							found = true; 
							if(encoding) {
								applet.setEncoding(encoding);
							}
							applet.append(text);
							applet.print();
						}
					}
					if(!found) {		     	     				
			       		alert('".$msg['printer_not_found']."');
			       	}
			    }
			       				
			    function printer_raspberry_send_ticket(url) {
         	
		         	var req = new http_request();
		         	var tpl;
		         	var printer = '';
					var printer_id = 0;
		         	var raspberry_ip = '';
					var printer_type = '';
		         	
		         	//Quelle est l'imprimante sélectionnée ?
		         	if (req.request('./ajax.php?module=circ&categ=zebra_print_pret&sub=get_selected_printer')) {
						alert ( req.get_text() );
					} else {
						printer = req.get_text();
					}
					if (printer == '') {
						alert('".$msg['user_printer_not_found']."');
						return;
					}
					
					var temp = printer.split('@');
					printer_id = temp[0];
					raspberry_ip = temp[1];
		
					//On interroge le raspberry pour connaitre le type d'imprimante (et savoir si elle est bien sur ce raspberry)
					if (req.request('https://' + raspberry_ip + '/getPrinter?idPrinter=' + printer_id)) {
						alert ( req.get_text() );
					} else {
						printer_type = req.get_text();
					}
					if (printer_type == '' || printer_type == 'unknown') {
						alert('".$msg['user_printer_type_not_found']."');
						return;
					}
		
					//On va générer le template en fonction de l'imprimante
					url = url + '&printer_type=' + printer_type;
					if(req.request(url)){
						alert ( req.get_text() );
					} else {
						tpl = req.get_text();
					}
		         	if (tpl.length == 0) {
		         		alert('".$msg['printer_tpl_error']."');
		         		return;
		         	}
		
					//On envoie l'impression
					var xhr = new XMLHttpRequest();
					xhr.open('POST', 'https://' + raspberry_ip + '/print?', true);
					xhr.setRequestHeader('Content-type', 'text/plain');
					xhr.send(JSON.stringify({idPrinter:printer_id,xml:tpl}));
								
		         	return;
		         				
		        }
		";
	
	if (substr($pmb_printer_name,0,9) == 'raspberry') {
		$print_script.= "
			function printer_jzebra_print_ticket(url) {
				printer_raspberry_send_ticket(url);
			}";
	} else {
		$print_script.="
			function printer_jzebra_print_ticket(url) {
				printer_get_jzebra();
				var req = new http_request();
				if(req.request(url)){
					// Il y a une erreur. 
					alert ( req.get_text() );			
				}else { 
					printer_jzebra_send_ticket(req.get_text(),'".$pmb_printer_name."','850');
					return 1;	
				}
			}";
	}
	$print_script.="
		printer_jzebra_print_ticket('$base_path/ajax.php?module=circ&categ=zebra_print_pret&sub=transacash_ticket&transacash_id=!!transacash_id!!');
		</script>	";
}

switch ($act) {
	case "valenc":
		//Validation de ce qui n'est pas valide
		$t=$cpte->get_transactions("","",0,0);
		$somme=0;
		$val_transactions="";
		for ($i=0; $i<count($t); $i++) {
			if ($cpte->validate_transaction($t[$i]->id_transaction)) {
				$somme+=$t[$i]->montant*$t[$i]->sens;
				$val_transactions.=" #".$t[$i]->id_transaction."#";
			}
		}
		$transacash_num = 0;
		if (!empty($t) && is_array($t)) {
			$transacash_num = $cpte->cashdesk_memo_transactions($t);					
		}
		if ($val_transactions!="") {
		    $val_transactions=$msg["finance_enc_tr_lib_valider"]." : ".$val_transactions."\n";
		}
		$solde_avant=$cpte->get_solde();
		if ($solde_avant!=0) {
		    $val_transactions.=$msg["finance_enc_tr_lib_etat_compte"]." : ".$solde_avant;
		}
		$cpte->update_solde();
		encaisse_form(true,$transacash_num);
		break;
	case "enc":
		if ($somme*1>0) {
			//Generation de la transaction
		    if ($id_transaction=$cpte->record_transaction("",$somme,1,$val_transactions,1, 0, $f_payment_method)) {
				$cpte->validate_transaction($id_transaction);
				$cpte->update_solde();
				if(!$transacash_num){
					$req="select MAX(transacash_num) from transactions where compte_id=".$cpte->id_compte."";
					$resultat=pmb_mysql_query($req);
					if ($transacash_num=pmb_mysql_result($resultat,0,0)){
						$req="update transactions set transacash_num = $transacash_num where compte_id=".$cpte->id_compte." and transacash_num=0";
						pmb_mysql_query($req);
					}
				}
				$cpte->cashdesk_memo_encaissement($id_transaction,$transacash_num,$somme);		
			}
			//AUCUN TEMPLATE D'ENCAISSEMENT PAR IMPRIMANTE TICKET DE PRET POUR LE MOMENT...
			//print str_replace("!!transacash_id!!",$transacash_num,$print_script);			
			back_to_main();
		} else {
			back_to_list();
		}
		break;
	case "transac_add":			
		if ($transactype_total*1>0) {
			$transactype=new transactype($transactype_id);	
			if($quantity) $aff_quantity=" ($quantity)";
			//Generation de la transaction
			if ($id_transaction=$cpte->record_transaction("",$transactype_total,-1,$transactype->name.$aff_quantity,0,$transactype_id)) {
				//$cpte->validate_transaction($id_transaction);
				//$cpte->update_solde();
			}
			back_to_main();
		} else {
			back_to_list();
		}
		break;
	case "val":
		if (!isset($trans)) $trans=array();
		$i=0;
		foreach ($trans as $key=>$value){
			$cpte->validate_transaction($key);
			if (isset($t[$i])) {
				$t[$i]->id_transaction=$key;
			}
			$i++;
		}
		if (!empty($t) && is_array($t)) {
			$transacash_num = $cpte->cashdesk_memo_transactions($t);
		}
		$cpte->update_solde();
		back_to_main();
		break;
	case "supr":
		if (!isset($trans)) $trans=array();
		foreach ($trans as $key=>$value){
			$cpte->delete_transaction($key);
		}
		$cpte->update_solde();
		back_to_main();
		break;
	case "encnoval":
		$solde=$cpte->get_solde();
		$val_transactions.=$msg["finance_enc_tr_lib_etat_compte"]." : ".$solde;
		encaisse_form();
		
		break;
	case "special":
		special_form();
		break;
	case "enc_special":
		if ($somme*1>0) {
			switch ($typ_special) {
				case "1":
				    //Crediter
					$signe=1;
					$caisse=0;
					break;
				case "2":
					//Debiter
					$signe=-1;
					$caisse=0;
					break;
				case "3":
					//Crediter et encaisser 
					$signe=1;
					$caisse=1;
					break;
				case "4":
					//Debiter et decaisser
					$signe=-1;
					$caisse=1;
					break;
			}
			if ($id_transaction=$cpte->record_transaction("",$somme,$signe,stripslashes($commentaire),$caisse, 0, $f_payment_method)) {
				$cpte->validate_transaction($id_transaction);
				//Traitement du passage en perte
				//Credit
				if (($typ_special==1)&&($credit_perte)) {
					//Ajout d'un transaction debit pour le compte 0
					$requete="insert into transactions (compte_id,user_id,user_name,machine,date_enrgt,date_prevue,date_effective,montant,sens,realisee,commentaire,encaissement) values(0,$PMBuserid,'".$PMBusername."','".$_SERVER["REMOTE_ADDR"]."', now(), now(), now(), ".($somme*1).", -1, 1,'#".$id_transaction."# : ".$commentaire."',0)";
					pmb_mysql_query($requete);
				}
				//Decaissement
				if (($typ_special==4)&&($dec_perte)) {
					//Credit sur le compte du lecteur
					if ($id_transaction_1=$cpte->record_transaction("",$somme,1,sprintf($msg["finance_enc_tr_lib_lost"]," #".$id_transaction."#")." : ".stripslashes($commentaire),0)) {
						$cpte->validate_transaction($id_transaction_1);
						//Debit sur le compte 0
						$requete="insert into transactions (compte_id,user_id,user_name,machine,date_enrgt,date_prevue,date_effective,montant,sens,realisee,commentaire,encaissement) values(0,$PMBuserid,'".$PMBusername."','".$_SERVER["REMOTE_ADDR"]."', now(), now(), now(), ".($somme*1).", -1, 1,'#".$id_transaction_1."# : ".sprintf($msg["finance_enc_tr_lib_lost"],"#".$id_transaction."#")." : ".stripslashes($commentaire)."',0)";
						pmb_mysql_query($requete);
					}
				}
				$cpte->update_solde();
			}
			back_to_main();
		} else {
			back_to_list();
		}
	default:
		back_to_list();
		break;
}

print "<script type='text/javascript'> 
if(parent.document.getElementById('selector_transaction_list')) {
    parent.document.getElementById('selector_transaction_list').style.visibility='hidden';
}
if(parent.document.getElementById('buttons_transaction_list')) {
    parent.document.getElementById('buttons_transaction_list').style.visibility='hidden';
}
</script>";
?>