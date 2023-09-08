<?php function listingBulletin() { ?>
		<optgroup label="<?php print "Avec Unité d'enseignement (PDF) " ?>">
		<option value='bull01UE' id='select1'><?php print LANGPARAM30?> TRIADE (UE)</option>
		<option value='bull02UE' id='select1'><?php print LANGPARAM30?> Univ. Pro. Afrique (UE)</option>
		<option value='bull03UE' id='select1'><?php print LANGPARAM30?> Pigier (UE)</option>
		<option value='bull04UE' id='select1'><?php print LANGPARAM30?> IPAC (UE)</option>
		<option value='bull04UE3' id='select1'><?php print LANGPARAM30?> Certification IPAC (UE)</option>
		<option value='bull04UE2' id='select1'><?php print "Relevé de notes" ?> IPAC (UE)</option>
		<optgroup label="<?php print "Bulletin avec Unité Enseignement (xls)" ?>">
     		<option value='bull01XL' id='select1'><?php print LANGPARAM30?> TRIADE (UE xls)</option>
     		<option value='bull02XL' id='select1'><?php print LANGPARAM30?> IMSG (UE xls)</option>

		<optgroup label="<?php print "Livret Scolaire Français" ?> (PDF)">
		<option value='bullFr6eme' id='select1'>6e - 5e - 4e - 3e </option>
		<option value='bullFrCycle' id='select1'>Socle en fin de cycle</option>

		<optgroup label="<?php print LANGBULL47 ?> (PDF)">
     		<option value='bull01' id='select1'><?php print LANGPARAM30?> TRIADE (sans sous matière)</option>
<?php if (file_exists("bulletin_construction01a.php")) { ?>
		<option value='bull01a' id='select1'><?php print LANGPARAM30?> TRIADE-ARABE</option>
<?php } ?>
		<option value='bull01b' id='select1'><?php print LANGPARAM30?> IPAC BTS</option>
     		<option value='bull02' id='select1'><?php print LANGPARAM30?> Lisaa</option>
     		<option value='bull112' id='select1'><?php print LANGPARAM30?> Delafosse</option>
     		<option value='bull03' id='select1'><?php print LANGPARAM30?> Arras</option>
		<option value='bull04' id='select1'><?php print LANGPARAM30?> collège Chicago</option>
		<option value='bull08' id='select1'><?php print LANGPARAM30?> Lycée Chicago</option>
		<option value='bull10' id='select1'><?php print LANGPARAM30?> Primaire Montessori</option>
		<option value='bull05' id='select1'><?php print LANGPARAM30?> Inter. Montessori</option>
		<option value='bull07' id='select1'><?php print LANGPARAM30 ." spècif." ?> Inter. Montessori</option>
		<option value='bull09' id='select1'><?php print LANGPARAM30?> Lycée Namur</option>
		<option value='bull11' id='select1'><?php print LANGPARAM30?> Cours Renaissance</option>
		<option value='bull12' id='select1'><?php print LANGPARAM30?> MFREO</option>
		<option value='bull12bis' id='select1'><?php print LANGPARAM30?> MFREO - (2)</option>
		<option value='bull12ter' id='select1'><?php print LANGPARAM30?> Louis Loucheur</option>
		<option value='bull0105' id='select1'><?php print LANGPARAM30?> CFAA 17</option>
		<option value='bull0106' id='select1'><?php print LANGPARAM30?> d-Savio & n-Dame</option>
		<option value='bull0108' id='select1'><?php print LANGPARAM30?> Blaise d'Auriol</option>
		<option value='bull0109' id='select1'><?php print LANGPARAM30?> Cie. Formation</option>
		<option value='bull0110' id='select1'><?php print LANGPARAM30?> CREATEC</option>
		<option value='bull0111' id='select1'><?php print LANGPARAM30?> APCE</option>
		<option value='bull113' id='select1'><?php print LANGPARAM30?> ESG</option>
		<option value='bull500' id='select1'><?php print LANGPARAM30?> Delafosse (S1)</option>
		<option value='bull501' id='select1'><?php print LANGPARAM30?> Delafosse (S2)</option>
		<option value='bull502' id='select1'><?php print LANGPARAM30?> Laghmani Primaire</option>
		<option value='bull503' id='select1'><?php print LANGPARAM30?> Laghmani Collège</option>
		<option value='bulllprb' id='select1'><?php print LANGPARAM30?> LPRB</option>
     		<option value='bull01133' id='select1'><?php print LANGPARAM30?> Pigier (Nîmes)</option>
     		<option value='bull01133a' id='select1'><?php print LANGPARAM30?> Pigier (Paris) V2</option>
		<option value='bull0109-2' id='select1'><?php print LANGPARAM30?> Pigier (Cannes)</option>
     		<option value='bull01144' id='select1'><?php print LANGPARAM30?> AGR</option>
		<option value='bull099' id='select1'><?php print LANGPARAM30?> Institut Regina Pacis</option>
		<option value='bull600' id='select1'><?php print LANGPARAM30?> EMC - Campus</option>
		<option value='bull700' id='select1'><?php print LANGPARAM30?> ISP</option>
		<option value='bull800' id='select1'><?php print LANGPARAM30?> Monts du lyonnais</option>
		<option value='bull01019' id='select1'><?php print LANGPARAM30?> Inst. Sup. de l'ent. Martinique</option>		
		<option value='bull09S' id='select1'><?php print LANGPARAM30?> E.E.P.P</option>	
		<option value='bull099a' id='select1'><?php print LANGPARAM30?> Collège Immaculée Conception</option>	
		<option value='bull05UE' id='select1'><?php print LANGPARAM30?> CLESI</option>
		<optgroup label="<?php print LANGBULL48 ?> (PDF)">
     		<option value='bull0101' id='select1'><?php print LANGPARAM30?> TRIADE (avec sous matière)</option>
     		<option value='bull01022' id='select1'><?php print LANGPARAM30?> L. Jean Perrin (1ère/Term)</option>
     		<option value='bull0102' id='select1'><?php print LANGPARAM30?> L. Jean Perrin (Seconde) </option>
     		<option value='bull0103' id='select1'><?php print LANGPARAM30?> L. Jean Perrin (CPGE) </option>
		<option value='bull0104' id='select1'><?php print LANGPARAM30?> collège Chicago</option>
		<option value='bull0107' id='select1'><?php print LANGPARAM30?> d-Savio & n-Dame</option>
		<option value='bull0208' id='select1'><?php print LANGPARAM30?> Blaise d'Auriol</option>
		<option value='bull0209' id='select1'><?php print LANGPARAM30?> Blaise d'Auriol (2)</option>
		<option value='bull0210' id='select1'><?php print LANGPARAM30?> Pigier (Aix)</option>
		<option value='bull0211' id='select1'><?php print LANGPARAM30?> Pigier (2) (Aix)</option>
		<option value='bull0210a' id='select1'><?php print LANGPARAM30?> Pigier (Paris)</option>
		<option value='bull0210b' id='select1'><?php print LANGPARAM30?> Pigier Partiel (Paris)</option>
		<option value='bull0211a' id='select1'><?php print LANGPARAM30?> LCPC Formation</option>
		<option value='bul9999' id='select1'><?php print LANGPARAM30?> VATEL</option>
		<option value='bul9999en' id='select1'><?php print LANGPARAM30?> VATEL en anglais</option>
		<option value='bul9999es' id='select1'><?php print LANGPARAM30?> VATEL en espagnol</option>
		<option value='bul9999c' id='select1'><?php print LANGPARAM30?> VATEL Master</option>
		<option value='bul9999a' id='select1'><?php print LANGPARAM30?> VATEL Annuel</option>
		<option value='bul9999e' id='select1'><?php print LANGPARAM30?> VATEL Paris Annuel</option>
		<option value='bul9999aa' id='select1'><?php print LANGPARAM30?> VATEL Annuel Beta</option>
		<option value='bul9999f' id='select1'><?php print LANGPARAM30?> VATEL Annuel Beta Paris</option>
		<option value='bul9999b' id='select1'><?php print LANGPARAM30?> VATEL Madrid Annuel</option>
		<option value='bul9999t' id='select1'><?php print LANGPARAM30?> VATEL Tunisie</option>
		<option value='bul9999i' id='select1'><?php print LANGPARAM30?> VATEL Ile Maurice</option>
		<option value='bul9999d' id='select1'><?php print LANGPARAM30?> VATEL Paris</option>
		<option value='bul9999r' id='select1'><?php print LANGPARAM30?> VATEL Réunion</option>
		<option value='bul0303' id='select1'><?php print LANGPARAM30?> Lycée Seminaire</option>
		<option value='bul0304' id='select1'><?php print LANGPARAM30?> Collège Seminaire</option>
		<option value='bull06' id='select1'><?php print LANGPARAM30?> Bonifacio</option>
		<option value='bull0305' id='select1'><?php print LANGPARAM30?> ISMAPP</option>
		<option value='bull0305a' id='select1'><?php print LANGPARAM30?> ISMAPP Annuel</option>
		<option value='bull0305c' id='select1'><?php print LANGPARAM30?> ISMAPP Annuel (Chaire Int.)</option>
		<option value='bull0305b' id='select1'><?php print LANGPARAM30?> ISMAPP 2019</option>
		<option value='bull0305b-nv' id='select1'><?php print LANGPARAM30?> ISMAPP 2019 NV</option>
		<option value='bull0305b-2' id='select1'><?php print LANGPARAM30?> ISMAPP 2020</option>
		<option value='bull0305d' id='select1'><?php print LANGPARAM30?> ISMAPP UE</option>
		<option value='bull01011' id='select1'><?php print LANGPARAM30?> Regina Pacis</option>
		<option value='bull01012' id='select1'><?php print LANGPARAM30?> Collège Saint-Géraud</option>
		<option value='bull01013' id='select1'><?php print LANGPARAM30?> Collège Jeanne d'Arc</option>
		<option value='bull01014' id='select1'><?php print LANGPARAM30?> Lycée Diwan</option>
		<option value='bull01015' id='select1'><?php print LANGPARAM30?> LEAP V1</option>		
		<option value='bull01015-2' id='select1'><?php print LANGPARAM30?> LEAP V2</option>		
		<option value='bull01016' id='select1'><?php print LANGPARAM30?> ORTORAH </option>		
		<option value='bull01017' id='select1'><?php print LANGPARAM30?> LFMP </option>		
		<option value='bull01018' id='select1'><?php print LANGPARAM30?> Int. Excel. Marly  </option>		
     		<option value='bull0101a' id='select1'><?php print LANGPARAM30?> Ecole AFTEC </option>
     		<option value='bull01001' id='select1'><?php print LANGPARAM30?> Arts Collège - Le Cheneraie</option>
<?php 
} 


function listBulletinBlanc() {
?>
		<optgroup label="<?php print LANGBULL49 ?>">
		<option value='bull407' id='select1'><?php print LANGPARAM30?> EduServices BTS Blanc</option>
		<option value='bull408' id='select1'><?php print LANGPARAM30?> EduServices TAS</option>
		<option value='bull410' id='select1'><?php print LANGPARAM30?> EduServices Partiel Blanc</option>
		<option value='bull401' id='select1'><?php print LANGPARAM30?> BAC Blanc</option>
		<option value='bull402' id='select1'><?php print LANGPARAM30?> BTS Blanc</option>
		<option value='bull403' id='select1'><?php print LANGPARAM30?> Brevet Blanc</option>
		<option value='bull409' id='select1'><?php print LANGPARAM30?> Brevet Professionnel Blanc</option>
		<option value='bull404' id='select1'><?php print LANGPARAM30?> CAP Blanc</option>
		<option value='bull405' id='select1'><?php print LANGPARAM30?> BEP Blanc</option>
		<option value='bull406' id='select1'><?php print LANGPARAM30?> Partiel Blanc</option>
     		<option value='bull0101b' id='select1'><?php print LANGPARAM30?> BTS Blanc Ecole AFTEC </option>

<?php
}

function RecupBulletin($value) {
	switch($value) {

		case 'bull01UE' : $im=LANGPARAM30." Triade (UE)"		; break;
		case 'bull03UE' : $im=LANGPARAM30." Pigier (UE)"		; break;
		case 'bull01' 	: $im=LANGPARAM30." Triade (sans sous matière)" ; break;
		case 'bull01b' 	: $im=LANGPARAM30." IPAC BTS" 			; break;
		case 'bull01a' 	: $im=LANGPARAM30." Triade-Arabe" 		; break;
		case 'bull02' 	: $im=LANGPARAM30." Lissa" 			; break;
		case 'bull112' 	: $im=LANGPARAM30." Delafosse" 			; break;
		case 'bull03' 	: $im=LANGPARAM30." Arras" 			; break;
		case 'bull04' 	: $im=LANGPARAM30." collège Chicago" 		; break;
		case 'bull08' 	: $im=LANGPARAM30." Lycée Chicago" 		; break;
		case 'bull10' 	: $im=LANGPARAM30." Primaire Montessori" 	; break;
		case 'bull05' 	: $im=LANGPARAM30." Inter. Montessori" 		; break;
		case 'bull07' 	: $im=LANGPARAM30." spècif Inter. Montessori" 	; break;
		case 'bull09' 	: $im=LANGPARAM30." Lycée Namur" 		; break;
		case 'bull11' 	: $im=LANGPARAM30." Cours Renaissance" 		; break;
		case 'bull12' 	: $im=LANGPARAM30." MFREO" 			; break;
		case 'bull12bis': $im=LANGPARAM30." MFREO - (2)" 		; break;
		case 'bull12ter': $im=LANGPARAM30." Louis Loucheur" 		; break;
		case 'bull0105' : $im=LANGPARAM30." CFAA 17" 			; break;
		case 'bull0106' : $im=LANGPARAM30." d-Savio & n-Dame" 		; break;
		case 'bull0108' : $im=LANGPARAM30." Blaise d'Auriol" 		; break;
		case 'bull0109' : $im=LANGPARAM30." Cie. Formation" 		; break;
		case 'bull0109-2': $im=LANGPARAM30." Pigier (Cannes)" 		; break;
		case 'bull0110' : $im=LANGPARAM30." CREATEC" 			; break;
		case 'bull0111' : $im=LANGPARAM30." APCE" 			; break;
		case 'bull113' 	: $im=LANGPARAM30." ESG" 			; break;
		case 'bull500' 	: $im=LANGPARAM30." Delafosse (S1)" 		; break;
		case 'bull501' 	: $im=LANGPARAM30." Delafosse (S2)" 		; break;
		case 'bull502' 	: $im=LANGPARAM30." Laghmani Primaire" 		; break;
		case 'bull503' 	: $im=LANGPARAM30." Laghmani Collège" 		; break;
		case 'bull0101' : $im=LANGPARAM30." TRIADE (avec sous matière)"	; break;
		case 'bull0101a' : $im=LANGPARAM30." Ecole AFTEC"        	; break;
		case 'bull0101b' : $im=LANGPARAM30." BTS Blanc Ecole AFTEC"   	; break;
		case 'bull01022': $im=LANGPARAM30." L. Jean Perrin (1ère/Term)" ; break;
		case 'bull0102' : $im=LANGPARAM30." L. Jean Perrin (Seconde)" 	; break;
		case 'bull0103' : $im=LANGPARAM30." L. Jean Perrin (CPGE)" 	; break;
		case 'bull0104' : $im=LANGPARAM30." collège Chicago" 		; break;
		case 'bull0107' : $im=LANGPARAM30." d-Savio & n-Dame" 		; break;
		case 'bull0208' : $im=LANGPARAM30." Blaise d'Auriol" 		; break;
		case 'bull0209' : $im=LANGPARAM30." Blaise d'Auriol (2)" 	; break;
		case 'bul9999'  : $im=LANGPARAM30." VATEL" 			; break;
		case 'bul9999en': $im=LANGPARAM30." VATEL en anglais"		; break;
		case 'bul9999es': $im=LANGPARAM30." VATEL en espagnol"		; break;
		case 'bul9999c' : $im=LANGPARAM30." VATEL Master"		; break;
		case 'bul9999a' : $im=LANGPARAM30." VATEL Annuel" 		; break;
		case 'bul9999e' : $im=LANGPARAM30." VATEL Annuel Paris" 	; break;
		case 'bul9999aa': $im=LANGPARAM30." VATEL Annuel beta"		; break;
		case 'bul9999f' : $im=LANGPARAM30." VATEL Annuel beta Paris"	; break;
		case 'bul9999b' : $im=LANGPARAM30." VATEL Madrid Annuel" 	; break;
		case 'bul9999t' : $im=LANGPARAM30." VATEL Tunisie"	 	; break;
		case 'bul9999i' : $im=LANGPARAM30." VATEL Ile Maurice"	 	; break;
		case 'bul9999d' : $im=LANGPARAM30." VATEL Paris "	 	; break;
		case 'bul9999r' : $im=LANGPARAM30." VATEL Réunion "	 	; break;
		case 'bul0303' 	: $im=LANGPARAM30." Lycée Seminaire" 		; break;
		case 'bul0304' 	: $im=LANGPARAM30." Collège Seminaire" 		; break;
		case 'bull0305' : $im=LANGPARAM30." ISMAPP" 			; break;
		case 'bull06' 	: $im=LANGPARAM30." Bonifacio" 			; break;
		case 'bull407' 	: $im=LANGPARAM30." Cie. Formation BTS Blanc" 	; break;
		case 'bull408' 	: $im=LANGPARAM30." Cie. Formation TAS" 	; break;
		case 'bull401' 	: $im=LANGPARAM30." BAC Blanc" 			; break;
		case 'bull402' 	: $im=LANGPARAM30." BTS Blanc" 			; break;
		case 'bull403' 	: $im=LANGPARAM30." Brevet Blanc" 		; break;
		case 'bull404' 	: $im=LANGPARAM30." CAP Blanc" 			; break;
		case 'bull405' 	: $im=LANGPARAM30." BEP Blanc" 			; break;
		case 'bull406' 	: $im=LANGPARAM30." Partiel Blanc" 		; break;
		case 'bull409' 	: $im=LANGPARAM30." Brevet Professionnel Blanc"	  ; break;
		case 'bull410' 	: $im=LANGPARAM30." Cie. Formation Partiel Blanc" ; break;
		case 'bulllprb' : $im=LANGPARAM30." LPRB" 			; break;
		case 'bull01133': $im=LANGPARAM30." Pigier Nîmes"		; break; 
		case 'bull01133a': $im=LANGPARAM30." Pigier Paris V2"		; break; 
		case 'bull01144': $im=LANGPARAM30." AGR"			; break; 
		case 'bull01011': $im=LANGPARAM30." Regina Pacis"		; break;
		case 'bull01012': $im=LANGPARAM30." Collège Saint-Géraud"	; break;
		case 'bull01013': $im=LANGPARAM30." Collège Jeanne d'Arc"	; break;
		case 'bull01014': $im=LANGPARAM30." Lycée Diwan"		; break;
		case 'bull01015': $im=LANGPARAM30." LEAP V1"			; break;
		case 'bull01015-2': $im=LANGPARAM30." LEAP V2"			; break;
		case 'bull01016': $im=LANGPARAM30." ORTORAH"			; break;
		case 'bull0305a': $im=LANGPARAM30." ISMAPP Annuel"		; break;
		case 'bull0305c': $im=LANGPARAM30." ISMAPP Annuel (Chaire Int.)"; break;
		case 'bull0305b': $im=LANGPARAM30." ISMAPP 2019"              ; break;
		case 'bull0305b-nv': $im=LANGPARAM30." ISMAPP 2019 NV"              ; break;
		case 'bull0305b-2': $im=LANGPARAM30." ISMAPP 2020"              ; break;
		case 'bull0305d': $im=LANGPARAM30." ISMAPP UE "                 ; break;
		case 'bull099'  : $im=LANGPARAM30." Institut Regina Pacis"	; break;
		case 'bull600'  : $im=LANGPARAM30." EMC - Campus"		; break;
		case 'bull700'  : $im=LANGPARAM30." ISP"			; break;
		case 'bull0211' : $im=LANGPARAM30." Pigier 2 Aix"		; break;
		case 'bull0211a': $im=LANGPARAM30." LCPC Formation"		; break;
		case 'bull0210' : $im=LANGPARAM30." Pigier Aix"			; break;
		case 'bull0210a': $im=LANGPARAM30." Pigier Paris"		; break;
		case 'bull0210b': $im=LANGPARAM30." Pigier Partiel Paris"	; break;
		case 'bull800'  : $im=LANGPARAM30." Monts du lyonnais"		; break;
		case 'bull01016': $im=LANGPARAM30." ORTORAH"			; break;
		case 'bull01017': $im=LANGPARAM30." LFMP"			; break;
		case 'bull01018': $im=LANGPARAM30." Int. Excel. Marly "		; break;
		case 'bull01019': $im=LANGPARAM30." Inst. Sup. de l'ent. Martinique "; break;
		case 'bull01XL':  $im=LANGPARAM30." Triade (UE xls)"	        ; break;
		case 'bull02XL':  $im=LANGPARAM30." IMSG (UE xls)"	        ; break;
		case 'bull09S':   $im=LANGPARAM30." E.E.P.P "			; break;
		case 'bull02UE':  $im=LANGPARAM30." Univ. Pro. Afrique (UE) "	; break;		
		case 'bull04UE':  $im=LANGPARAM30." IPAC (UE)"			; break;
		case 'bull04UE3':  $im=LANGPARAM30." Certification IPAC (UE)"	; break;
		case 'bull04UE2': $im="Relevé de notes IPAC (UE)"		; break;
		case 'bull05UE':  $im=LANGPARAM30." CLESI"			; break;
		case 'bull099a':  $im=LANGPARAM30." Collège Immaculée Conception "	; break;
		case 'bullFr6eme': $im=LANGPARAM30." Livret Scolaire 6e-5e-4e-3e "	; break;
		case 'bullFrCycle': $im=LANGPARAM30." Socle en fin de cycle "		; break ;
		case 'bull01001'  : $im=LANGPARAM30." Arts Collège - Le Cheneraie"	; break ;
		default		  : $im=''; break;
	}
	return($im);
}
 

?>



