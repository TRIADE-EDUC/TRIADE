<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pmbxml2ead_output.class.php,v 1.1 2018/07/25 06:19:17 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

require_once ($base_path."/admin/convert/convert_output.class.php");

class pmbxml2ead_output extends convert_output {
	public function _get_header_($output_params) {
	      $r="<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>".chr(0x0D).chr(0x0A);
	      $r.="<ead>".chr(0x0D).chr(0x0A);
	      $r.="	<eadheader>".chr(0x0D).chr(0x0A);
	      $r.="		<eadid countrycode=\"FR\">FRAD072_1</eadid>".chr(0x0D).chr(0x0A);
	      $r.="		<filedesc>".chr(0x0D).chr(0x0A);
	      $r.="			<titlestmt>".chr(0x0D).chr(0x0A);
	      $r.="				<titleproper>Inventaire en EAD</titleproper>".chr(0x0D).chr(0x0A);
	      $r.="				<author></author>".chr(0x0D).chr(0x0A);
	      $r.="			</titlestmt>".chr(0x0D).chr(0x0A);
	      $r.="		</filedesc>".chr(0x0D).chr(0x0A);
	      $r.="		<profiledesc>".chr(0x0D).chr(0x0A);
	      $r.="			<creation>".chr(0x0D).chr(0x0A);
	      $r.="				<date></date>".chr(0x0D).chr(0x0A);
	      $r.="			</creation>".chr(0x0D).chr(0x0A);
	      $r.="			<langusage>Document rédigé en".chr(0x0D).chr(0x0A);
	      $r.="				<language langcode=\"fre\">français</language>".chr(0x0D).chr(0x0A);
	      $r.="			</langusage>".chr(0x0D).chr(0x0A);
	      $r.="		</profiledesc>".chr(0x0D).chr(0x0A);
	      $r.="	</eadheader>".chr(0x0D).chr(0x0A);
	      $r.="	<archdesc level=\"fonds\">".chr(0x0D).chr(0x0A);
	      $r.="		<did>".chr(0x0D).chr(0x0A);
	      $r.="			<unittitle>Fond de document</unittitle>".chr(0x0D).chr(0x0A);
	      $r.="		</did>".chr(0x0D).chr(0x0A);
	    return $r;
	}
	
	public function _get_footer_($output_params) {
		$r="";
		$r.="	</archdesc>".chr(0x0D).chr(0x0A);
		$r.="</ead>";
	    return $r;
	}
}

?>