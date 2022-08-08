<?php

require_once dirname(__FILE__) . '/../tcpdf/tcpdf.php';

class Pdf extends Core
{
    private $document_author = 'ecozaym24';

    private $tcpdf;

    public function create($template, $name, $filename, $download = false, $yandex = false)
    {

        $this->tcpdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $this->tcpdf->SetCreator(PDF_CREATOR);
        $this->tcpdf->SetAuthor($this->document_author);
        $this->tcpdf->SetTitle($name);
        $this->tcpdf->SetSubject('TCPDF Tutorial');
        $this->tcpdf->SetKeywords('');
        $this->tcpdf->SetMargins(20, 6, 10, 0);
        $this->tcpdf->setFooterMargin(0);
        $this->tcpdf->SetAutoPageBreak(TRUE, 0);

        // set font
        $this->tcpdf->SetFont('dejavusans', '', 9);

        $this->tcpdf->SetPrintHeader(false);
        $this->tcpdf->SetPrintFooter(false);

        $this->tcpdf->AddPage();

        $this->tcpdf->writeHTML($template, true, false, true, false, '');

        $this->tcpdf->IncludeJS("print();");

        if ($download) {
            $this->tcpdf->Output($download . '.pdf', 'D');
        } elseif ($yandex) {
            $this->tcpdf->Output($this->config->root_url  . '/files/users/' . $yandex . '.pdf', 'F');
        } else {
            $this->tcpdf->Output($filename . '.pdf', 'I');
        }
    }
}
