<?php
// fonction hex2dec
// retourne un tableau associatif (clés : R,V,B) à
// partir d'un code html de couleur hexa (ex : #3FE5AA)

//error_reporting(0);

declare(strict_types=1);

// --- Conversion couleur hexadécimale en RGB ---
function hex2dec(string $couleur = "#000000"): array {
    $rouge = hexdec(substr($couleur, 1, 2));
    $vert  = hexdec(substr($couleur, 3, 2));
    $bleu  = hexdec(substr($couleur, 5, 2));

    return ['R' => $rouge, 'V' => $vert, 'B' => $bleu];
}

// --- Conversion pixel -> millimètre (72 dpi) ---
function px2mm(float $px): float {
    return $px * 25.4 / 72;
}

// --- Conversion entités HTML ---
function txtentities(string $html): string {
    $trans = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $trans = array_flip($trans);
    return strtr($html, $trans);
}

// --- Classe principale ---
class createPDF {

    private string $html;
    private string $title;
    private string $articleurl;
    private string $author;
    private int $date;
    private string $directory = './';
    private string $http = '';
    private int $delete = 60;
    private string $from = 'UTF-8';
    private string $to = 'UTF-8';
    private bool $useiconv = false;
    private bool $bi = true;

    public function __construct(string $_html, string $_title, string $_articleurl, string $_author, int $_date) {
        $this->html = $_html;
        $this->title = $_title;
        $this->articleurl = $_articleurl;
        $this->author = $_author;
        $this->date = $_date;
    }

    private function _convert(string $s): string {
        return $this->useiconv ? iconv($this->from, $this->to, $s) : $s;
    }

    private function _iso2ascii(string $s): string {
        $iso = "áèïéìíåµòóø¹»úùý¾äëöüÁÈÏÉÌÍÅ¥ÒÓØ©«ÚÙÝ®ÄËÖÜ";
        $asc = "acdeeillnorstuuyzaeouACDEEILLNORSTUUYZAEOU";
        return strtr($s, $iso, $asc);
    }

    private function _makeFileName(string $title): string {
        $title = $this->_iso2ascii(strip_tags(trim($title)));
        preg_match_all('/[a-zA-Z0-9]+/', $title, $nt);
        return implode('-', $nt[0]);
    }

    public function run(): void {
        $replace = [
            '<strong>' => '<b>',
            '<br />'   => '<br>',
            '<hr />'   => '<hr>',
            '[r]'      => '<red>',
            '[/r]'     => '</red>',
            '[l]'      => '<blue>',
            '[/l]'     => '</blue>',
            '&#8220;'  => '"',
            '&#8221;'  => '"',
            '&#8222;'  => '"',
            '&#8230;'  => '...',
            '&#8217;'  => '\''
        ];
        $this->html = str_replace(array_keys($replace), array_values($replace), $this->html);

        $pdf = new PDF('P', 'mm', 'A4', $this->title, $this->articleurl, false);
        // Plus besoin de ->Open() avec les versions récentes de FPDF
        $pdf->SetCompression(true);
        $pdf->SetCreator('');
        $pdf->SetDisplayMode('real');
        $pdf->SetTitle($this->_convert($this->title));
        $pdf->SetAuthor($this->author);
        $pdf->AddPage();

        $pdf->PutMainTitle($this->_convert($this->title));
        $pdf->PutMinorHeading('Article URL');
        $pdf->PutMinorTitle($this->articleurl, $this->articleurl);
        $pdf->PutMinorHeading('Author');
        $pdf->PutMinorTitle($this->_convert($this->author));
        $pdf->PutMinorHeading("Published: " . date("F j, Y, g:i a", $this->date));
        $pdf->PutLine();
        $pdf->Ln(10);

        $pdf->WriteHTML($this->_convert($this->html), $this->bi);

        $filename = $this->directory . $this->_makeFileName($this->title) . '.pdf';
        $http     = $this->http . $this->_makeFileName($this->title) . '.pdf';
        $pdf->Output($filename, 'F');
        header("Location: $http");

        // Nettoyage des anciens fichiers PDF
        foreach (glob($this->directory . '*.pdf') as $file) {
            if (filectime($file) + ($this->delete * 60) < time()) {
                @unlink($file);
            }
        }
        exit;
    }
}



////////////////////////////////////
//class PDF extends UFPDF
class PDF extends FPDF
{
    // Variables du parseur HTML
    private bool $B = false;
    private bool $I = false;
    private bool $U = false;
    private array $HREF = [];

    private string $title;
    private string $articleUrl;

    public function __construct(
        string $orientation = 'P',
        string $unit = 'mm',
        string $size = 'A4',
        string $title = '',
        string $articleUrl = '',
        bool $unicode = false
    ) {
        parent::__construct($orientation, $unit, $size);
        $this->title = $title;
        $this->articleUrl = $articleUrl;
    }

    // --- Fonctions d’écriture simples ---
    public function PutMainTitle(string $txt): void {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, $txt, 0, 1, 'C');
        $this->Ln(5);
    }

    public function PutMinorHeading(string $txt): void {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 6, $txt, 0, 1, 'L');
    }

    public function PutMinorTitle(string $txt, ?string $url = null): void {
        $this->SetFont('Arial', '', 11);
        if ($url) {
            $this->SetTextColor(0, 0, 255);
            $this->Write(6, $txt, $url);
            $this->SetTextColor(0);
        } else {
            $this->Write(6, $txt);
        }
        $this->Ln(4);
    }

    public function PutLine(): void {
        $this->Ln(3);
        $this->SetDrawColor(180, 180, 180);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(5);
    }

    // --- Gestion HTML basique ---
    public function WriteHTML(string $html, bool $allowBasicTags = true): void {
        // Nettoyage minimal
        $html = str_replace("\n", ' ', $html);
        $a = preg_split('/<(.*)>/U', $html, -1, PREG_SPLIT_DELIM_CAPTURE);

        foreach ($a as $i => $e) {
            if ($i % 2 == 0) {
                // Texte
                if ($this->HREF) {
                    $this->PutLink($this->HREF['href'], $e);
                } else {
                    $this->Write(5, $e);
                }
            } else {
                // Balise
                if ($e[0] == '/') {
                    $this->CloseTag(strtoupper(substr($e, 1)));
                } else {
                    $a2 = explode(' ', strtoupper($e));
                    $tag = array_shift($a2);
                    $attr = [];
                    foreach ($a2 as $v) {
                        if (preg_match('/([^=]*)=["\']?([^"\']*)/', $v, $a3)) {
                            $attr[strtoupper($a3[1])] = $a3[2];
                        }
                    }
                    $this->OpenTag($tag, $attr);
                }
            }
        }
    }

    private function OpenTag(string $tag, array $attr): void {
        switch ($tag) {
            case 'B': $this->SetStyle('B', true); break;
            case 'I': $this->SetStyle('I', true); break;
            case 'U': $this->SetStyle('U', true); break;
            case 'A':
                $this->HREF = ['href' => $attr['HREF'] ?? ''];
                break;
            case 'BR':
                $this->Ln(5);
                break;
            case 'P':
                $this->Ln(8);
                break;
            case 'HR':
                $this->Ln(2);
                $this->SetDrawColor(150, 150, 150);
                $this->Line($this->GetX(), $this->GetY(), $this->GetX() + 190, $this->GetY());
                $this->Ln(3);
                break;
            case 'IMG':
                if (isset($attr['SRC'])) {
                    $x = $this->GetX();
                    $y = $this->GetY();
                    $w = isset($attr['WIDTH']) ? (float)$attr['WIDTH'] : 0;
                    $h = isset($attr['HEIGHT']) ? (float)$attr['HEIGHT'] : 0;
                    $this->Image($attr['SRC'], $x, $y, $w, $h);
                    $this->Ln($h ? $h + 2 : 10);
                }
                break;
        }
    }

    private function CloseTag(string $tag): void {
        switch ($tag) {
            case 'B': $this->SetStyle('B', false); break;
            case 'I': $this->SetStyle('I', false); break;
            case 'U': $this->SetStyle('U', false); break;
            case 'A': $this->HREF = []; break;
        }
    }

    private function SetStyle(string $tag, bool $enable): void {
        $this->$tag = $enable;
        $style = '';
        foreach (['B', 'I', 'U'] as $s) {
            if ($this->$s) $style .= $s;
        }
        $this->SetFont('', $style);
    }

    private function PutLink(string $URL, string $txt): void {
        $this->SetTextColor(0, 0, 255);
        $this->SetStyle('U', true);
        $this->Write(5, $txt, $URL);
        $this->SetStyle('U', false);
        $this->SetTextColor(0);
    }
}



?>
