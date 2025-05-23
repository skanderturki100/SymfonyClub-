<?php

// src/Service/PdfService.php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Dompdf\Canvas;

class PdfService
{
    private Dompdf $dompdf;

    public function __construct()
    {
        // Initialisation de DOMPDF avec des options par défaut
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', false); // Désactive le PHP pour des raisons de sécurité
        $this->dompdf = new Dompdf($options);
    }

    /**
     * Génère un PDF à partir du contenu HTML
     */
    public function generatePdfFromHtml(string $htmlContent, string $filename): void
    {
        // Charger le contenu HTML dans DOMPDF
        $this->dompdf->loadHtml($htmlContent);

        // (Optionnel) Définir la taille du papier
        $this->dompdf->setPaper('A4', 'portrait');

        // Ajouter un pied de page sur chaque page
       // $this->dompdf->getCanvas()->page_text(270, 800, 'www.esprit.tn', null, 12, array(0, 0, 0));

        // Rendre le PDF
        $this->dompdf->render();

        // Enregistrer ou afficher le PDF
        $this->dompdf->stream($filename, ['Attachment' => 0]); // 0 signifie afficher dans le navigateur
    }
}
