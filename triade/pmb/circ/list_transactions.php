<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: list_transactions.php,v 1.11.8.1 2024/09/12 13:46:31 dgoron Exp $

global $base_path, $current_alert, $id_compte, $show_transactions, $date_debut, $charset, $msg;

//Liste des transactions d'un compte
$base_path = "..";
//$base_noheader=1;

$current_alert = "circ";

require_once("../includes/init.inc.php");
require_once("$base_path/classes/comptes.class.php");
require_once("$base_path/classes/transaction/transaction_payment_method.class.php");

$cpte=new comptes($id_compte);
if ($cpte->error) {
	print $cpte->error_message;
	exit;
}

switch ($show_transactions) {
	case "2":
		$t=$cpte->get_transactions("","",0,0);
		break;
	case "3":
		$date_debut_=extraitdate($date_debut);
		$t=$cpte->get_transactions($date_debut_,"",0,-1,0,"asc");
		break;
	case "1":
	default:
		$t=$cpte->get_transactions("","",0,-1, 10);
		break;
}

print "<form name='form_transactions' action='encaissement.php' method='post' onSubmit='return false'>
           <input type='hidden' name='act' value=''/>
           <input type='hidden' name='id_compte' value='".$id_compte."'/>
           <input type='hidden' name='show_transactions' value='".$show_transactions."'/>
           <input type='hidden' name='date_debut' value='".htmlentities($date_debut,ENT_QUOTES,$charset)."'/>";

if (empty($t) || !is_array($t)) {
    print $msg["finance_list_tr_no_tr"];
} else {
	print "<table style='width:100%'>
	           <tr>
	               <th>".$msg["finance_list_tr_date_enrgt"]."</th>
                   <th>&nbsp;</th>
                   <th>".$msg["finance_list_tr_comment"]."</th>
                   <th style='text-align:right'>".$msg["finance_montant"]."</th>
                   <th style='text-align:right'>".$msg["finance_list_tr_deb_cred"]."</th>
                   <th style='text-align:center'>".$msg["finance_list_tr_validee"]."</th>
                   <th>".$msg["finance_date_valid"]."</th>
                   <th><input title='".$msg['tout_cocher_checkbox']."' type='checkbox' value='0' onchange='checkbox_checked(this)'></th>
               </tr>\n";
	$nb_transactions = count($t);
	for ($i = 0; $i < $nb_transactions; $i++) {
		print "<tr>";
		print pmb_bidi("<td>".formatdate($t[$i]->date_enrgt)."</td>");
		print pmb_bidi("<td>".($t[$i]->encaissement?"*":"")."</td>");
		print pmb_bidi("<td>".$t[$i]->commentaire."</td>");
		print pmb_bidi("<td  style='text-align:right'>".($t[$i]->sens==-1? "<span class='erreur'>":"").comptes::format($t[$i]->montant).($t[$i]->sens==-1? "</span>":"")."</td>");
		$payment_method = '';
		if ($t[$i]->transaction_payment_method_num) {
		    $transaction_payment_method = new transaction_payment_method($t[$i]->transaction_payment_method_num);
		    $payment_method = ' (' . $transaction_payment_method->get_name() . ')';
		}		
		print pmb_bidi("<td style='text-align:right'>".($t[$i]->sens==1 ? $msg["finance_form_empr_libelle_credit"]: $msg["finance_form_empr_libelle_debit"]) . $payment_method . "</td>");
		print pmb_bidi("<td style='text-align:center'>".($t[$i]->realisee ? "X":"")."</td>");
		print pmb_bidi("<td>".formatdate($t[$i]->date_effective)."</td>");
		print "<td>";
		if (!$t[$i]->realisee) {
			print "<input class='finance_checkbox' type='checkbox' value='1' name='trans[".$t[$i]->id_transaction."]' ";
			//$tans="trans_".$t[$i]->id_transaction;
			//if (${$trans}) print "checked";
			print ">";
		}
		print "</td>
		    </tr>\n";
	}
	print "</table>\n";
}
print "</form>
       <script>
            if(parent.document.getElementById('selector_transaction_list')) {
                parent.document.getElementById('selector_transaction_list').style.visibility='visible';
            }
            if(parent.document.getElementById('buttons_transaction_list')) {
                parent.document.getElementById('buttons_transaction_list').style.visibility='visible';
            }
            
            function checkbox_checked(status) {
                var lis_checkbox = document.querySelectorAll('input[type=checkbox].finance_checkbox');
                if (lis_checkbox.length > 0) {
                    for (var i = 0; i < lis_checkbox.length; i++) {
                        if (lis_checkbox[i].checked != status.checked) {
                            lis_checkbox[i].checked = status.checked;
                        }
                    }
                
                    if (status.checked) {
                        status.title = '".$msg['tout_decocher_checkbox']."';
                    }else{
                        status.title = '".$msg['tout_cocher_checkbox']."';
                    }

                } else {
                    status.checked = !status.checked;
                }
            }
        </script>";
?>