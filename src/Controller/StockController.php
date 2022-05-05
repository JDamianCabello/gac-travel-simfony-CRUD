<?php

namespace App\Controller;

use App\Entity\StockHistory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StockController extends AbstractController
{
    #[Route('/stock', name: 'stock')]
    public function index(): Response
    {
        $logs = $this->getDoctrine()->getRepository(StockHistory::class)->findAll();;

        return $this->render('stock/index.html.twig', [
            'logs' => $logs,
        ]);
    }
}
