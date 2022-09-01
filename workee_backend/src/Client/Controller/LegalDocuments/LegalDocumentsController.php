<?php

namespace App\Client\Controller\LegalDocuments;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class LegalDocumentsController extends AbstractController
{
    public function __construct(
        private string $rpgdDocumentPath,
        private string $cguDocumentPath,
    ) {
    }

    /**
     * @Route("/api/rgpd", name="getRgpd", methods={"GET"})
     */
    public function getRgpd(Request $request): Response
    {
        return new Response(file_get_contents($this->rpgdDocumentPath), 200, [
            'Content-Type' => 'text/plain',
        ]);
    }

    /**
     * @Route("/api/cgu", name="getCgu", methods={"GET"})
     */
    public function getCgu(Request $request)
    {
        return new PdfResponse(
            file_get_contents($this->cguDocumentPath),
            'CGU.pdf',
        );
    }
}
