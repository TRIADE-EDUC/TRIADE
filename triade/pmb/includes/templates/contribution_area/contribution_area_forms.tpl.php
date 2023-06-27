<?php 
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: contribution_area_forms.tpl.php,v 1.8 2019-05-27 10:33:57 ngantier Exp $

if (stristr($_SERVER['REQUEST_URI'], ".tpl.php")) die("no access");

global $contribution_area_entity_line, $msg, $contribution_area_form_line, $contribution_area_form_table;

$contribution_area_entity_line = '
		<div id="!!entity_id!!" class="notice-parent contribution_forms">
			<div class="row item-expandable">
				<img src="'.get_url_icon('plus.gif').'" class="img_plus" name="imEx" id="!!entity_id!!Img" title="détail" style="border:0px; margin:3px 3px" onclick="expandBase(\'!!entity_id!!\', true);  return false;">
				<span class="notice-heada">
					!!entity_name!! !!forms_number!!
				</span>
			</div>
			<div id="!!entity_id!!Child" class="notice-child contribution_forms" style="margin-bottom: 6px; display: block; width: 94%;" startopen="Yes">
				!!forms_table!!
				<div class="row">
					<div class="left">
						<input type="button" class="bouton" name="add_form_!!entity_type!!" id="add_form_!!entity_type!!" value="'.$msg['ajouter'].'" onclick=\'document.location="./modelling.php?categ=contribution_area&sub=form&type=!!entity_type!!&action=edit&form_id=0";\'/>
						<div class="row"></div>
					</div>
				</div>
				&nbsp;
				&nbsp;
			</div>			
		</div>                   
		<div class="row"></div>
		<div class="row"></div>
		';

$contribution_area_form_line = '
		<tr class="!!odd_even!!" style="cursor: pointer" onmouseover=\'this.className="surbrillance"\' onmouseout=\'this.className="!!odd_even!!"\' >
			<td onmouseup=\'document.location="./modelling.php?categ=contribution_area&sub=form&type=!!form_type!!&action=edit&form_id=!!form_id!!";\' >
				!!form_name!!
			</td>
			<td>
				<input type="button" class="bouton" value="'.$msg['duplicate'].'" onclick=\'document.location="./modelling.php?categ=contribution_area&sub=form&type=!!form_type!!&action=duplicate&form_id=!!form_id!!";\'/>
				<input type="button" class="bouton" value="'.$msg['supprimer'].'" onclick="if(confirm(\''.$msg['confirm_suppr_de'].'!!form_name!! ?\')) document.location=\'./modelling.php?categ=contribution_area&sub=form&type=!!form_type!!&action=delete&form_id=!!form_id!!\';"/>
			</td>
		</tr>	
';

$contribution_area_form_table = '
		<table>
			<tr>
				<th>'.$msg['652'].'</th>
				<th></th>
			</tr>
			!!forms_tab!!
		</table>
';