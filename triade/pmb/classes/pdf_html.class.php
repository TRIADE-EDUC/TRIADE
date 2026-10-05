<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: pdf_html.class.php,v 1.2 2017/07/03 09:07:10 dgoron Exp $

if (stristr($_SERVER['REQUEST_URI'], ".class.php")) die("no access");

// fonction hex2dec
// retourne un tableau associatif (clés : R,V,B) à
// partir d'un code html de couleur hexa (ex : #3FE5AA)
function hex2dec($couleur = "#000000"){
    $R = substr($couleur, 1, 2);
    $rouge = hexdec($R);
    $V = substr($couleur, 3, 2);
    $vert = hexdec($V);
    $B = substr($couleur, 5, 2);
    $bleu = hexdec($B);
    $tbl_couleur = array();
    $tbl_couleur['R']=$rouge;
    $tbl_couleur['V']=$vert;
    $tbl_couleur['B']=$bleu;
    return $tbl_couleur;
}

//conversion pixel -> millimètre en 72 dpi
function px2mm($px){
    return $px*25.4/72;
}

function txtentities($html){
    $trans = get_html_translation_table(HTML_ENTITIES);
    $trans = array_flip($trans);
    return strtr($html, $trans);
}


class PDF_HTML extends FPDF {
	//variables du parseur html
	public $B;
	public $I;
	public $U;
	public $HREF;
	public $TH;
	public $TD;
	public $fontList;
	public $issetfont;
	public $issetcolor;
//	public $lMargin;
//	public $tMargin;
	
	public function __construct($orientation='P', $unit='mm', $format='A4') {
	    //Appel au constructeur parent
	    parent::__construct($orientation,$unit,$format);
	    //Initialisation
	    $this->B=0;
	    $this->I=0;
	    $this->U=0;
	    $this->HREF='';
	    $this->TH='';
	    $this->TD='';
//	    $this->lMargin=10;
//	    $this->tMargin='';
	    $this->fontlist=array('arial', 'times', 'courier', 'helvetica', 'symbol');
	    $this->issetfont=false;
	    $this->issetcolor=false;
	}
	
	public function WriteHTML($html) {
	    //Parseur HTML
	    $html=strip_tags($html,"<b><u><i><a><img><p><br><strong><em><font><tr><blockquote><th><td>"); //supprime tous les tags sauf ceux reconnus
	    $html=str_replace("\n",' ',$html); //remplace retour à la ligne par un espace
	    $a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE); //éclate la chaîne avec les balises
	    
	    foreach($a as $i=>$e) {
	        if($i%2==0) {
	            //Texte
	            if($this->HREF)
	                $this->PutLink($this->HREF,$e);
	            elseif($this->TH)
	            	$this->PutCell($this->TH,30, 0, $e);
	            elseif($this->TD)
	            	$this->PutCell($this->TD, 30, 0, $e);
	            else
	                $this->Write(5,stripslashes(txtentities($e)));
	                
	        } else {
	            //Balise
	            if($e[0]=='/')
	                $this->CloseTag(strtoupper(substr($e,1)));
	            else {
	                //Extraction des attributs
	                $a2=explode(' ',$e);
	                $tag=strtoupper(array_shift($a2));
	                $attr=array();
	                foreach($a2 as $v) {
	                    if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
	                        $attr[strtoupper($a3[1])]=$a3[2];
	                }
	                $this->OpenTag($tag,$attr);
	            }
	        }
	    }
	}
	
	public function OpenTag($tag, $attr) {
	    //Balise ouvrante
	    switch($tag){
	        case 'STRONG':
	            $this->SetStyle('B',true);
	            break;
	        case 'EM':
	            $this->SetStyle('I',true);
	            break;
	        case 'B':
	        case 'I':
	        case 'U':
	            $this->SetStyle($tag,true);
	            break;
	        case 'A':
	            $this->HREF=$attr['HREF'];
	            break;
	        case 'IMG':
	            if(isset($attr['SRC']) && (isset($attr['WIDTH']) || isset($attr['HEIGHT']))) {
	                if(!isset($attr['WIDTH']))
	                    $attr['WIDTH'] = 0;
	                if(!isset($attr['HEIGHT']))
	                    $attr['HEIGHT'] = 0;
	                $this->Image($attr['SRC'], $this->GetX(), $this->GetY(), px2mm($attr['WIDTH']), px2mm($attr['HEIGHT']));
	            }
	            break;
	        case 'TH':
	        	$this->TH = 'TH';
//	        	$this->lMargin += 5;
//	        	$this->SetXY(5, $this->tMargin);
	        	break;
//	        	$this->Cell(50,6,$e,1,1,'L');
//	        	break;
	        case 'TD':
	        	$this->TD = 'TD';
	        	break;
	        case 'TR':
//	        	$y = $this->getY();
//	        	$this->setY($y+5);
//	        	$this->setX(10);
	        	$this->Ln(5);
//	        	$this->SetXY($this->lMargin, $this->tMargin);
	        	break;
	        case 'BLOCKQUOTE':
	        case 'BR':
	            $this->Ln(5);
	            break;
	        case 'P':
	            $this->Ln(10);
	            break;
	        case 'FONT':
	            if (isset($attr['COLOR']) && $attr['COLOR']!='') {
	                $coul=hex2dec($attr['COLOR']);
	                $this->SetTextColor($coul['R'],$coul['V'],$coul['B']);
	                $this->issetcolor=true;
	            }
	            if (isset($attr['FACE']) && in_array(strtolower($attr['FACE']), $this->fontlist)) {
	                $this->SetFont(strtolower($attr['FACE']));
	                $this->issetfont=true;
	            }
	            break;
	    }
	}
	
	public function CloseTag($tag) {
	    //Balise fermante
	    if($tag=='STRONG')
	        $tag='B';
	    if($tag=='EM')
	        $tag='I';
	    if($tag=='B' || $tag=='I' || $tag=='U')
	        $this->SetStyle($tag,false);
	    if($tag=='A')
	        $this->HREF='';
	    if($tag=='FONT'){
	        if ($this->issetcolor==true) {
	            $this->SetTextColor(0);
	        }
	        if ($this->issetfont) {
	            $this->SetFont('arial');
	            $this->issetfont=false;
	        }
	    }
	}
	
	public function SetStyle($tag, $enable) {
	    //Modifie le style et sélectionne la police correspondante
	    $this->$tag+=($enable ? 1 : -1);
	    $style='';
	    foreach(array('B','I','U') as $s) {
	        if($this->$s>0)
	            $style.=$s;
	    }
	    $this->SetFont('',$style);
	}
	
	public function PutLink($URL, $txt){
	    //Place un hyperlien
	    $this->SetTextColor(0,0,255);
	    $this->SetStyle('U',true);
	    $this->Write(5,$txt,$URL);
	    $this->SetStyle('U',false);
	    $this->SetTextColor(0);
	}
	
	public function PutCell($tag,$w,$h,$txt){
		global $largeur_page,$marge_page_droite,$marge_page_gauche;
	    //Tab
	    if ($tag == 'TH') 
	    	$this->SetStyle('B',true);
		
//	    $this->SetXY($this->lMargin, $this->tMargin);
//		$this->setX($w);
//	    $this->MultiCell((, $h, $txt, 1, 'L', 0);
		$x = $this->getX();
    	$this->Cell(($largeur_page - $marge_page_droite - $x),$h,$txt,0,1,'L');
	    $x = $x + $w;
	    $this->setX($x);
//	    
	    
	    if ($tag == 'TH')
	    	$this->SetStyle('B',false);
	    
	    if ($tag == 'TH')
	    	$this->TH = '';
	    else
	    	$this->TD = '';
	}

}//fin classe

?>