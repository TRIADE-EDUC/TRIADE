<?php
// fonction hex2dec
// retourne un tableau associatif (cls : R,V,B) 
// partir d'un code html de couleur hexa (ex : #3FE5AA)

error_reporting(0);

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

//conversion pixel -> millimtre en 72 dpi
function px2mm($px){
	return $px*25.4/72;
}

function txtentities($html){
	$trans = get_html_translation_table(HTML_ENTITIES);
	$trans = array_flip($trans);
	return strtr($html, $trans);
}



/************************************/
/* main class createPDF             */
/************************************/
class createPDF {

	function createPDF($_html,$_title,$_articleurl,$_author,$_date) {
		// main vars
		$this->html=$_html;               // html text to _convert to PDF
		$this->title=$_title;             // article title
		$this->articleurl=$_articleurl;   // article URL
		$this->author=$_author;           // article author
		$this->date=$_date;               // date being published
		// other options
		$this->directory='./';            // directory for temp files
		$this->http='';                   // http path
		$this->delete=60;                 // keep temp files for 60 minutes
		$this->from='iso-8859-2';         // input encoding
		$this->to='utf8';               // output encoding
		$this->useiconv=false;            // use iconv
		$this->bi=true;                   // support bold and italic tags
	}

	function _convert($s) {
		if ($this->useiconv) 
			return iconv($this->from,$this->to,$s); 
		else 
			return $s;
	}

	function _iso2ascii($s) {
		$iso="ťةݮ";
		$asc="acdeeillnorstuuyzaeouACDEEILLNORSTUUYZAEOU";
		return strtr($s,$iso,$asc);
	}

	function _makeFileName($title) {
		$title = $this->_iso2ascii(strip_tags(trim($title)));
		preg_match_all('/[a-zA-Z0-9]+/', $title, $nt);
		return implode('-',$nt[0]);
	}

	function run() {
		// change some win codes, and xhtml into html
		$str=array(
		'<strong>' => '<b>',
		'<br />' => '<br>',
		'<hr />' => '<hr>',
		'[r]' => '<red>',
		'[/r]' => '</red>',
		'[l]' => '<blue>',
		'[/l]' => '</blue>',
		'&#8220;' => '"',
		'&#8221;' => '"',
		'&#8222;' => '"',
		'&#8230;' => '...',
		'&#8217;' => '\''
		);
		foreach ($str as $_from => $_to) $this->html = str_replace($_from,$_to,$this->html);

		$pdf=new PDF('P','mm','A4',$this->title,$this->articleurl,false);
		$pdf->Open();
		$pdf->SetCompression(true);
		$pdf->SetCreator("");
		$pdf->SetDisplayMode('real');
		$pdf->SetTitle($this->_convert($this->title));
		$pdf->SetAuthor($this->author);
		$pdf->AddPage();

		// face
		$pdf->PutMainTitle($this->_convert($this->title));
		$pdf->PutMinorHeading('Article URL');
		$pdf->PutMinorTitle($this->articleurl,$this->articleurl);
		$pdf->PutMinorHeading('Author');
		$pdf->PutMinorTitle($this->_convert($this->author));
		$pdf->PutMinorHeading("Published: ".date("F j, Y, g:i a",$this->date));
		$pdf->PutLine();
		$pdf->Ln(10);

		// html
		$pdf->WriteHTML($this->_convert(stripslashes($this->html)),$this->bi);

		// save and redirect
		$filename=$this->directory.$this->_makeFileName($this->title).'.pdf';
		$http=$this->http.$this->_makeFileName($this->title).'.pdf';
		$pdf->Output($filename);
		header("Location: $http");

		// cleanup
		$files=opendir($this->directory);
		while ( false !== ($filename = readdir($files))) {
			if (!(strpos($filename,'.pdf')===false)){
				// delete old temp files
				$time=filectime($this->directory.$filename);
				if (!($time===false) && $time>0) if ($time+$this->delete*60<time())
					unlink($this->directory.$filename);
			}
		}
		// stop processing
		exit;
	}
} 


////////////////////////////////////
class PDF extends UFPDF
{
//variables du parseur html
var $B;
var $I;
var $U;
var $HREF;
var $fontList;
var $issetfont;
var $issetcolor;
var $PiedPage;		 //Info pied page

function PDF($orientation='P',$unit='mm',$format='A4',$_title,$_url,$_debug=false)
	{
		$this->FPDF($orientation,$unit,$format);
		$this->B=0;
		$this->I=0;
		$this->U=0;
		$this->HREF='';
		$this->PRE=false;
		$this->SetFont('Arial','',12);
		$this->fontlist=array("Times","Courier","ae_AlHor", "ae_AlBattar","ae_AlMothnna","ae_AlMohanad");
		$this->issetfont=false;
		$this->issetcolor=false;
		$this->articletitle=$_title;
		$this->articleurl=$_url;
		$this->debug=$_debug;
		$this->AliasNbPages();
	}

//////////////////////////////////////
//Parser html

function WriteHTML($html)
{
	//Parseur HTML
	$html=strip_tags($html,"<b><u><i><a><img><p><br><strong><em><font><tr><blockquote>"); //supprime tous les tags sauf ceux reconnus
	$html=str_replace("\n",' ',$html); //remplace retour  la ligne par un espace
	$a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE); //clate la chane avec les balises
	foreach($a as $i=>$e)
	{
		if($i%2==0)
		{
			//Texte
			if($this->HREF)
				$this->PutLink($this->HREF,$e);
			else
				$this->Write(5,stripslashes(txtentities($e)));
		}
		else
		{
			//Balise
			if($e{0}=='/')
				$this->CloseTag(strtoupper(substr($e,1)));
			else
			{
				//Extraction des attributs
				$a2=explode(' ',$e);
				$tag=strtoupper(array_shift($a2));
				$attr=array();
				foreach($a2 as $v)
					if(ereg('^([^=]*)=["\']?([^"\']*)["\']?$',$v,$a3))
						$attr[strtoupper($a3[1])]=$a3[2];
				$this->OpenTag($tag,$attr);
			}
		}
	}
}

function OpenTag($tag,$attr) //Balise ouvrante
{
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
			if(isset($attr['SRC']) and (isset($attr['WIDTH']) or isset($attr['HEIGHT']))) {
				if(!isset($attr['WIDTH']))
					$attr['WIDTH'] = 0;
				if(!isset($attr['HEIGHT']))
					$attr['HEIGHT'] = 0;
				$this->Image($attr['SRC'], $this->GetX(), $this->GetY(), px2mm($attr['WIDTH']), px2mm($attr['HEIGHT']));
			}
			break;
		case 'TR':
		case 'BLOCKQUOTE':
		case 'BR':
			$this->Ln(5);
			break;
		case 'P':
			$this->Ln(10);
			break;
		case 'FONT':
			if (isset($attr['COLOR']) and $attr['COLOR']!='') {
				$coul=hex2dec($attr['COLOR']);
				$this->SetTextColor($coul['R'],$coul['V'],$coul['B']);
				$this->issetcolor=true;
			}
			if (isset($attr['FACE']) and in_array(strtolower($attr['FACE']), $this->fontlist)) {
				$this->SetFont(strtolower($attr['FACE']));
				$this->issetfont=true;
			}
			break;
		case 'HR':
	                if( !empty($attr['WIDTH']) )
	                    $Width = $attr['WIDTH'];
	                else
	                    $Width = $this->w - $this->lMargin-$this->rMargin;
		            $this->Ln(2);
	                    $x = $this->GetX();
		            $y = $this->GetY();
	                    $this->SetLineWidth(0.4);
		            $this->Line($x,$y,$x+$Width,$y);
	                    $this->SetLineWidth(0.2);
		            $this->Ln(2);
			break;
	}
}

function CloseTag($tag) //Balise fermante
{
	if($tag=='STRONG')
		$tag='B';
	if($tag=='EM')
		$tag='I';
	if($tag=='B' or $tag=='I' or $tag=='U')
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

function SetStyle($tag,$enable)
{
	//Modifie le style et slectionne la police correspondante
	$this->$tag+=($enable ? 1 : -1);
	$style='';
	foreach(array('U','U','U') as $s)
		if($this->$s>0)
			$style.=$s;
	$this->SetFont('',$style);
}

function PutLink($URL,$txt)
{
	//Place un hyperlien
	$this->SetTextColor(0,0,255);
	$this->SetStyle('U',true);
	$this->Write(5,$txt,$URL);
	$this->SetStyle('U',false);
	$this->SetTextColor(0);
}

function Footer()
	{
		//Go to 1.5 cm from bottom
		$this->SetY(-5);
		//Select Arial italic 8
		$this->SetFont('Times','',8);
		//Print centered page number
		$this->SetTextColor(0,0,0);
		//$this->Cell(0,4,'Page '.$this->PageNo().'/{nb}',0,1,'C');
		$this->SetTextColor(0,0,180);
		$info=$this->GetPiedPage();
		if ($info != "") { $info=" - $info"; }

		$this->Cell(0,4,"T.R.I.A.D.E.   - www.triade-educ.com $info",0,0,'C',0,'http://www.triade-educ.com');
		$this->mySetTextColor(-1);
	}



function SetPiedPage($info) {
	$this->PiedPage=$info;
}

function GetPiedPage() {
	return($this->PiedPage);
}


function PutLine()
	{
		$this->Ln(2);
		$this->Line($this->GetX(),$this->GetY(),$this->GetX()+187,$this->GetY());
		$this->Ln(3);
	}


function mySetTextColor($r,$g=0,$b=0){
		static $_r=0, $_g=0, $_b=0;

		if ($r==-1) 
			$this->SetTextColor($_r,$_g,$_b);
		else {
			$this->SetTextColor($r,$g,$b);
			$_r=$r;
			$_g=$g;
			$_b=$b;
		}
	}

	function PutMainTitle($title) {
		if (strlen($title)>55)
			$title=substr($title,0,55)."...";
		$this->SetTextColor(33,32,95);
		$this->SetFontSize(20);
		$this->SetFillColor(255,204,120);
		$this->Cell(0,20,$title,1,1,"C",1);
		$this->SetFillColor(255,255,255);
		$this->SetFontSize(12);
		$this->Ln(5);
	}

	function PutMinorHeading($title) {
		$this->SetFontSize(12);
		$this->Cell(0,5,$title,0,1,"C");
		$this->SetFontSize(12);
	}

	function PutMinorTitle($title,$url='') {
		$title=str_replace('http://','',$title);
		if (strlen($title)>70)
			if (!(strrpos($title,'/')==false))
				$title=substr($title,strrpos($title,'/')+1);
		$title=substr($title,0,70);
		$this->SetFontSize(16);
		if ($url!='') {
			$this->SetStyle('U',false);
			$this->SetTextColor(0,0,180);
			$this->Cell(0,6,$title,0,1,"C",0,$url);
			$this->SetTextColor(0,0,0);
			$this->SetStyle('U',false);
		} else
			$this->Cell(0,6,$title,0,1,"C",0);
		$this->SetFontSize(12);
		$this->Ln(4);
	}


function WriteHTML2($html)
{
	//HTML parser
	$html=str_replace("\n",' ',$html);
	$a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
	foreach($a as $i=>$e)
	{
		if($i%2==0)
		{
			//Text
			if($this->HREF)
				$this->PutLink($this->HREF,$e);
			else
				$this->Write(5,$e);
		}
		else
		{
			//Tag
			if($e{0}=='/')
				$this->CloseTag(strtoupper(substr($e,1)));
			else
			{
				//Extract attributes
				$a2=explode(' ',$e);
				$tag=strtoupper(array_shift($a2));
				$attr=array();
				foreach($a2 as $v)
					if(ereg('^([^=]*)=["\']?([^"\']*)["\']?$',$v,$a3))
						$attr[strtoupper($a3[1])]=$a3[2];
				$this->OpenTag($tag,$attr);
			}
		}
	}
}


function WriteTable($data,$w)
{
	$this->SetLineWidth(.3);
	$this->SetFillColor(255,255,255);
	$this->SetTextColor(0);
	$this->SetFont('');
	foreach($data as $row)
	{
		$nb=0;
		for($i=0;$i<count($row);$i++)
			$nb=max($nb,$this->NbLines($w[$i],trim($row[$i])));
		$h=5*$nb;
		$this->CheckPageBreak($h);
		for($i=0;$i<count($row);$i++)
		{
			$x=$this->GetX();
			$y=$this->GetY();
			$this->Rect($x,$y,$w[$i],$h);
			$this->MultiCell($w[$i],5,trim($row[$i]),0,'C');
			//Put the position to the right of the cell
			$this->SetXY($x+$w[$i],$y);					
		}
		$this->Ln($h);

	}
}

function NbLines($w,$txt)
{
	//Computes the number of lines a MultiCell of width w will take
	$cw=&$this->CurrentFont['cw'];
	if($w==0)
		$w=$this->w-$this->rMargin-$this->x;
	$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
	$s=str_replace("\r",'',$txt);
	$nb=strlen($s);
	if($nb>0 and $s[$nb-1]=="\n")
		$nb--;
	$sep=-1;
	$i=0;
	$j=0;
	$l=0;
	$nl=1;
	while($i<$nb)
	{
		$c=$s[$i];
		if($c=="\n")
		{
			$i++;
			$sep=-1;
			$j=$i;
			$l=0;
			$nl++;
			continue;
		}
		if($c==' ')
			$sep=$i;
		$l+=$cw[$c];
		if($l>$wmax)
		{
			if($sep==-1)
			{
				if($i==$j)
					$i++;
			}
			else
				$i=$sep+1;
			$sep=-1;
			$j=$i;
			$l=0;
			$nl++;
		}
		else
			$i++;
	}
	return $nl;
}

function CheckPageBreak($h)
{
	//If the height h would cause an overflow, add a new page immediately
	if($this->GetY()+$h>$this->PageBreakTrigger)
		$this->AddPage($this->CurOrientation);
}

function ParseTable($Table)
{
	$_var='';
	$htmlText = $Table;
	$parser = new HtmlParser ($htmlText);
	while ($parser->parse()) {
		if(strtolower($parser->iNodeName)=='table')
		{
			if($parser->iNodeType == NODE_TYPE_ENDELEMENT)
				$_var .='/::';
			else
				$_var .='::';
		}

		if(strtolower($parser->iNodeName)=='tr')
		{
			if($parser->iNodeType == NODE_TYPE_ENDELEMENT)
				$_var .='!-:'; //opening row
			else
				$_var .=':-!'; //closing row
		}
		if(strtolower($parser->iNodeName)=='td' && $parser->iNodeType == NODE_TYPE_ENDELEMENT)
		{
			$_var .='#,#';
		}
		if ($parser->iNodeName=='Text' && isset($parser->iNodeValue))
		{
			$_var .= $parser->iNodeValue;
		}
	}
	$elems = split(':-!',str_replace('/','',str_replace('::','',str_replace('!-:','',$_var)))); //opening row
	foreach($elems as $key=>$value)
	{
		if(trim($value)!='')
		{
			$elems2 = split('#,#',$value);
			array_pop($elems2);
			$data[] = $elems2;
		}
	}
	return $data;
}

}//fin classe
?>
