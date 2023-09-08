<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pnb_record_orders.tpl.php,v 1.5 2019-05-27 09:59:12 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

global $pnb_record_orders_tpl, $msg, $pnb_record_orders_tpl_line;

$pnb_record_orders_tpl = "
	<div class='row'>
		<script type=\"text/javascript\" src='./javascript/pnb.js'></script>			
		<table style='border:0px' class='expl-list'>
			<tr>
				<th class='center'>
					" . $msg['edit_pnb_order_id'] . "
				</th>
				<th class='center'>
					" . $msg['edit_pnb_order_line_id'] . "
				</th>
				<th class='center'>
					" . $msg['edit_pnb_order_loan_max_duration'] . "
				</th>
				<th class='center'>
					" . $msg['edit_pnb_order_nb_remaining_loans'] . "
				</th>
				<th class='center'>
					" . $msg['edit_pnb_order_nb_simultaneous_loans'] . "
				</th>
				<th class='center'>
					" . $msg['edit_pnb_order_nb_consult_in_situ'] . "
				</th>
				<th class='center'>
					" . $msg['edit_pnb_order_nb_consult_ex_situ'] . "
				</th>
				<th class='center'>
					" . $msg['edit_pnb_order_offer_date'] . "
				</th>
				<th class='center'>
					" . $msg['edit_pnb_order_offer_date_end'] . "
				</th>
			</tr>
			!!order_lines!!
		</table>
	</div>
";

$pnb_record_orders_tpl_line = "
	<tr>
		<td class='center'>
			!!order_id!!
		</td>
		<td class='center'>
			!!line_id!!
		</td>
		<td class='center'>
			!!loan_max_duration!!
		</td>
		<td class='center'>
			<script type=\"text/javascript\">
				addLoadEvent(function() {		
					pnb_get_loans_completed_number('!!record_id!!', '!!line_id!!');
				});
			</script>
			<span id='nb_loans_!!line_id!!'></span>
			!!nb_loans!!
		</td>
		<td class='center'>
			!!nb_simultaneous_loans!!
		</td>
		<td class='center'>
			!!nb_consult_in_situ!!
		</td>
		<td class='center'>
			!!nb_consult_ex_situ!!
		</td>
		<td class='center'>
			!!offer_date!!
		</td>
		<td class='center'>
			!!offer_date_end!!
		</td>
	</tr>
";