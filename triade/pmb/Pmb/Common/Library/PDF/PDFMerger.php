<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: PDFMerger.php,v 1.1.2.4 2024/06/28 07:32:17 qvarin Exp $

namespace Pmb\Common\Library\PDF;

use setasign\Fpdi\Tcpdf\Fpdi;

if (stristr($_SERVER['REQUEST_URI'], basename(__FILE__))) {
    die("no access");
}

class PDFMerger
{
    /**
     * Envoi le PDF vers le navigateur
     *
     * @var string
     */
    public const OUTPUT_DEST_I = 'I';

    /**
     * Télechargement du PDF dans le navigateur
     *
     * @var string
     */
    public const OUTPUT_DEST_D = 'D';

    /**
     * Envoi le PDF vers un fichier local
     *
     * @var string
     */
    public const OUTPUT_DEST_F = 'F';

    /**
     * Envoi le PDF vers un fichier local
     * et envoi le PDF vers le navigateur
     *
     * @var string
     */
    public const OUTPUT_DEST_FI = 'FI';

    /**
     * Envoi le PDF vers un fichier local
     * et télecharge le PDF dans le navigateur
     *
     * @var string
     */
    public const OUTPUT_DEST_FD = 'FD';

    /**
     * Retourne le PDF en base64 (RFC 2045)
     *
     * @var string
     */
    public const OUTPUT_DEST_E = 'E';

    /**
     * Retourne le binaire du PDF
     *
     * @var string
     */
    public const OUTPUT_DEST_S = 'S';

    /**
     * Objet Fpdi
     *
     * @var Fpdi
     */
    private $pdf;

    /**
     * Ajout d'une page vide a la fin d'un pdf
     * (si les page sont impaires)
     *
     * @var boolean
     */
    private $addBlankPage = false;

    /**
     * Tableau des sommaires
     *
     * @var array [$filename => $title]
     */
    private $summary = [];

    /**
     * Ajout d'un sommaire
     *
     * @var boolean
     */
    private $addSummary = false;

    /**
     * Constructeur
     *
     * @param boolean $addBlankPage Permet d'ajouter une page vide a la fin d'un pdf si les page sont impaires
     * @param array $summary Tableau des sommaires [$filename => $title]
     */
    public function __construct(bool $addBlankPage = false, array $summary = [])
    {
        $this->addBlankPage = $addBlankPage;
        $this->summary = $summary;
        $this->addSummary = !empty($this->summary);

        $this->pdf = new Fpdi();
        $this->pdf->setPrintHeader(false);
        $this->pdf->setPrintFooter(false);
    }

    /**
     * Liste des pdf a fusionner
     * Les fichiers qui ne sont pas des pdf seront ignore
     *
     * @param string[] $files
     * @return void
     */
    public function merge(array $files)
    {
        $files = array_filter($files, [PDFChecker::class, 'isPDF']);
        $lastIndex = count($files) - 1;

        foreach ($files as $index => $file) {
            if ($index >= $lastIndex) {
                // On n'ajoute pas de page vide pour le dernier pdf
                $this->addBlankPage = false;
            }
            if ($this->addBlankPage && $this->summary) {
            	if ($index === 0) {
                	$this->pdf->addPage();
            	}
            }
            $this->importPDF($file);
        }

        if ($this->pdf->getPage() % 2 !== 0) {
            $this->pdf->addPage();
        }
    }

    /**
     * Output
     *
     * @param string $outputFilename
     * @param string $dest
     * @return mixed
     */
    public function output(string $outputFilename = 'output.pdf', string $dest = self::OUTPUT_DEST_F)
    {
        if ($this->addSummary) {
            $this->generateSummary();
        }
        return $this->pdf->Output($outputFilename, $dest);
    }

    /**
     * Importation d'un pdf
     *
     * @param string $file
     * @return void
     */
    protected function importPDF(string $file)
    {
        $pageCount = $this->pdf->setSourceFile($file);
        for ($i = 1; $i <= $pageCount; $i++) {
            $tplIdx = $this->pdf->importPage($i);
            $this->pdf->addPage();
            if ($this->addSummary && $i === 1 && isset($this->summary[$file])) {
                $this->pdf->Bookmark($this->summary[$file], 0, 0, '', 'B', array(0, 64, 128));
            }
            $this->pdf->useTemplate($tplIdx, 0, 0, 210);
        }

        if ($this->addBlankPage && 0 !== $pageCount % 2) {
            $this->pdf->addPage();
        }
    }

    /**
     * Ajout d'un sommaire
     *
     * @return void
     */
    protected function generateSummary()
    {
        // On récupère le nombre de page avant l'ajout du sommaire
        $pagesBeforeTOC = $this->pdf->getNumPages();

        // Ajout d'une page vide pour le sommaire
        $this->pdf->addTOCPage();

        // Ajout du titre du sommaire
        $this->pdf->MultiCell(0, 0, 'Sommaire', 0, 'C', 0, 1, '', '', true, 0);
        $this->pdf->Ln();

        // Ajout d'une page vide pour le sommaire
        $this->pdf->addTOC(1, 'courier', '.', 'Sommaire', 'B', array(128,0,0));

        // Fin du sommaire
        $this->pdf->endTOCPage();
        $this->pdf->endPage(true);

        // On récupère le nombre de page apres l'ajout du sommaire
        $pagesAfterTOC = $this->pdf->getNumPages();

        // On ajoute une page vide entre le sommaire et les pdfs
        $pageCount = $pagesAfterTOC - $pagesBeforeTOC;
        if ($this->addBlankPage && 1 === $pageCount % 2) {
            $this->pdf->setPage($pageCount + 1);
            $this->pdf->addPage();
        }
    }
}
