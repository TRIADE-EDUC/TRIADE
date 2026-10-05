<?php

require_once("classes/fpdf.class.php");

class convert extends FPDF{
	public $logoUrl;	//url du logo déposé sur chaque page...
	public $header;	//header de page...
	public $footers;	//pied de page du document...
	public $resolution;

	public $outlines=array();
	public $OutlineRoot;


	public function __construct($params=array()){
		parent::__construct();
		$this->footers = $params['footers'];
		$this->setCreator(encoding_normalize::utf8_decode($params['creator']));
		$this->SetTextColor(0);
	}

	public function getSize($dimension,$resolution){
		$this->resolution = $resolution;
		$dimension = explode(",",$dimension);
		$resolution = explode(",",$resolution);
		$this->resolution = $resolution[0];
		$size = array(
			$this->convertPxToMm($dimension[0],$resolution[0]),
			$this->convertPxToMm($dimension[1],$resolution[1])
		);
		return $size;
	}

	public function convertPxToMm($px,$dpi=0){
		return ($px*25.4)/($dpi ? $dpi : $this->resolution);
	}

	public function Footer(){
		if ($this->logoUrl !="") $this->Image($this->logoUrl,10,8,20);
		if ($this->header) {
			$this->SetFont('Arial',"",14);
			$this->Cell(80); //Décalage à droite
			$this->Cell(30,10,$this->header,0,'C');
		}

		//si on a un footer spécificique pour la page courante...
		$footer = array();
		if($this->footers['page'.$this->PageNo()]){
			$footer = $this->footers['page'.$this->PageNo()];
		}else if ($this->footers['all']){
			$footer = $this->footers['all'];
		}

		//on applique le footer
		if($footer['name']){
			$this->SetY((-15*$this->h/297));
			$this->SetX((5*$this->w/210));
			//Police Arial italique 8
			$this->SetFont('Arial','I',(8*$this->w/210));
			if($footer['link']){
			    $this->Cell(0,10,encoding_normalize::utf8_decode($footer['name']),0,0,'',false,encoding_normalize::utf8_decode($footer['link']));
			}else{
			    $this->Cell(0,10,encoding_normalize::utf8_decode($footer['name']),0,0,'',false,'');
			}
		}
	}

	public function Error($msg){
		//erreur sur la classe FDPF, on la log avant d'arreter la génération...
		logMsg($msg);
		//Fatal error
		parent::Error($msg);
	}

	/*************************************************************************
	 *  Fonctions pour les signets (provient du site FPDF / Auteur : Olivier  *
	 *  http://www.fpdf.org/fr/script/script1.php                            *
	 *  Modifié par Arnaud RENOU (prise en compte d'un numéro de page        *
	 *************************************************************************/

	public function Bookmark($txt, $page=-1, $level=0, $y=0)	{
		if($y==-1)
			$y=$this->GetY();
		if($page == -1){
			$page = $this->PageNo();
		}
		$this->outlines[]=array('t'=>$txt, 'l'=>$level, 'y'=>($this->h-$y)*$this->k, 'p'=>$page);
	}
	public function BookmarkUTF8($txt,$page=-1, $level=0, $y=0){
		$this->Bookmark($this->_UTF8toUTF16($txt),$page, $level,$y);
	}

	public function _putbookmarks(){
		$nb=count($this->outlines);
		if($nb==0)
			return;
		$lru=array();
		$level=0;
		foreach($this->outlines as $i=>$o)
		{
			if($o['l']>0)
			{
				$parent=$lru[$o['l']-1];
				//Set parent and last pointers
				$this->outlines[$i]['parent']=$parent;
				$this->outlines[$parent]['last']=$i;
				if($o['l']>$level)
				{
					//Level increasing: set first pointer
					$this->outlines[$parent]['first']=$i;
				}
			}
			else
				$this->outlines[$i]['parent']=$nb;
			if($o['l']<=$level and $i>0)
			{
				//Set prev and next pointers
				$prev=$lru[$o['l']];
				$this->outlines[$prev]['next']=$i;
				$this->outlines[$i]['prev']=$prev;
			}
			$lru[$o['l']]=$i;
			$level=$o['l'];
		}
		//Outline items
		$n=$this->n+1;
		foreach($this->outlines as $i=>$o)
		{
			$this->_newobj();
			$this->_out('<</Title '.$this->_textstring($o['t']));
			$this->_out('/Parent '.($n+$o['parent']).' 0 R');
			if(isset($o['prev']))
				$this->_out('/Prev '.($n+$o['prev']).' 0 R');
			if(isset($o['next']))
				$this->_out('/Next '.($n+$o['next']).' 0 R');
			if(isset($o['first']))
				$this->_out('/First '.($n+$o['first']).' 0 R');
			if(isset($o['last']))
				$this->_out('/Last '.($n+$o['last']).' 0 R');
			$this->_out(sprintf('/Dest [%d 0 R /XYZ 0 %.2F null]',1+2*$o['p'],$o['y']));
			$this->_out('/Count 0>>');
			$this->_out('endobj');
		}
		//Outline root
		$this->_newobj();
		$this->OutlineRoot=$this->n;
		$this->_out('<</Type /Outlines /First '.$n.' 0 R');
		$this->_out('/Last '.($n+$lru[0]).' 0 R>>');
		$this->_out('endobj');
	}

	protected function _putresources(){
		parent::_putresources();
		$this->_putbookmarks();
	}

	protected function _putcatalog(){
		parent::_putcatalog();
		if(count($this->outlines)>0)
		{
			$this->_put('/Outlines '.$this->OutlineRoot.' 0 R');
			$this->_put('/PageMode /UseOutlines');
		}
	}

}
?>