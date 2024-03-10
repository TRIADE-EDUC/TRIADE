<?php
/***************************************************************************
 *                              T.R.I.A.D.E
 *                            ---------------
 *
 *   begin                : Janvier 2000
 *   copyright            : (C) 2000 E. TAESCH - T. TRACHET - 
 *   Site                 : http://www.triade-educ.com
 *
 *
 ***************************************************************************/
/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
/**
 * Simple test class for doing fake livesearch
 *
 * @category   HTML
 * @package    AJAX
 * @author     Joshua Eichorn <josh@bluga.net>
 * @copyright  2005 Joshua Eichorn
 * @license    http://www.opensource.org/licenses/lgpl-license.php  LGPL
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/HTML_AJAX
 */

class livesearch {
	/**
	 * Perform a search
	 *
	 * @return array
	 */

	function search($input,$quoi,$target,$form,$champs) {

			
/*
		$fp=fopen("./essai.txt","a+");
		fwrite($fp,"$input:$quoi:$target:$form:$champs:\r\n");
		fclose($fp);
*/	 
	 	

		$ret = array();

		include_once('../common/config.inc.php');
		include_once('./db_triade_ajax.php');
		include_once('./lib_prefixe.php');
		$cnx=cnx_ajax();



		if ($quoi == "eleve") {
			$datajax=recherche_ajax_eleve($input,PREFIXE);
			$datajax=array_unique($datajax);
			if (empty($datajax)) {
                                $ret[0] = '';
			}else{
				foreach($datajax as $key => $value) {
					if (stristr($value,$input)) {
						$value=trim($value);
						$ret[$key] = htmlentities($value);
					}
				}
			}
		}


		if ($quoi == "sanction") {
			$datajax=recherche_ajax_sanction($input,PREFIXE);
			$datajax=array_unique($datajax);
			if (empty($datajax)) {
				$ret[0] = '';
			}else{
				foreach($datajax as $key => $value) {
					$value=trim($value);
					$ret[$key] = sansaccent($value);
				}
			}
		}


		if ($quoi == "entreprise") {
			$datajax=recherche_ajax_entreprise($input,PREFIXE);
			$datajax=array_unique($datajax);
			 if (empty($datajax)) {
                                $ret[0] = '';
                        }else{
				foreach($datajax as $key => $value) {
					$value=trim($value);
					$ret[$key] = sansaccent($value);
				}
			}
		}


		if ($quoi == "matiere") {
                        $datajax=recherche_ajax_matiere($input,PREFIXE);
                        $datajax=array_unique($datajax);
                         if (empty($datajax)) {
                                $ret[0] = '';
                        }else{
                                foreach($datajax as $key => $value) {
					$value=preg_replace('/0$/','',$value);
                                        $value=trim($value);
                                        $ret[$key] = sansaccent($value);
                                }
                        }
                }




		array_unshift($ret, $champs);
		array_unshift($ret, $form);
		array_unshift($ret, $target);

		return $ret ;

	}


}

?>
