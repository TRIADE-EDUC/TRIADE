<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: frbr_cataloging_schemes.tpl.php,v 1.2 2019/05/27 10:50:59 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $frbr_cataloging_schemes_table_line, $frbr_cataloging_schemes_table, $msg;

$frbr_cataloging_schemes_table_line = '
		<tr class="!!odd_even!!" style="cursor: pointer" onmouseover=\'this.className="surbrillance"\' onmouseout=\'this.className="!!odd_even!!"\' >
			<td onmouseup=\'document.location="./modelling.php?categ=frbr&sub=cataloging_schemes&action=edit&scheme_id=!!scheme_id!!";\' >
				!!scheme_name!!
			</td>
			<td onmouseup=\'document.location="./modelling.php?categ=frbr&sub=cataloging_schemes&action=edit&scheme_id=!!scheme_id!!";\' >
				!!scheme_start_entity!!
			</td>
			<td>
				<input type="button" class="bouton" value="X" onclick=\'document.location="./modelling.php?categ=frbr&sub=cataloging_schemes&action=delete&scheme_id=!!scheme_id!!";\'/>
			</td>
		</tr>
';

$frbr_cataloging_schemes_table = '
		<div id="!!entity_id!!" class="row frbr_cataloging_schemes">
			<table>
				<tr>
					<th>'.$msg['652'].'</th>
					<th>'.$msg['frbr_cataloging_scheme_start_entity'].'</th>
					<th></th>
				</tr>
				!!schemes_tab!!
			</table>
			<div class="row">
				<div class="left">
					<input type="button" class="bouton" name="add_frbr_cataloging_scheme" id="add_frbr_cataloging_scheme" value="'.$msg['ajouter'].'" onclick=\'document.location="./modelling.php?categ=frbr&sub=cataloging_schemes&action=edit&scheme_id=0";\'/>
					<div class="row"></div>
				</div>
			</div>
		</div> 
';