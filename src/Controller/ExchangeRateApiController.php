<?php

namespace App\Controller;

use App\Service\ExchangeRateService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ExchangeRateApiController extends AbstractController
{
    public function __construct(private readonly ExchangeRateService $exchangeRateService)
    {
    }

    #[Route('/exchange/rate/api', name: 'app_exchange_rate_api')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ExchangeRateApiController.php',
        ]);
    }

    #[Route('/api/rates/last-24h', name: 'app_rate_last24h')]
    public function getLast24hRates(Request $request): JsonResponse
    {
        $symbols = $request->get('symbols', []);
        $rates = $this->exchangeRateService->getRates(json_decode($symbols));
        //dd($rates);

        return $this->json([
            'data' => $rates,
        ]);
    }

    #[Route('/api/rates/save', name: 'app_rate_save')]
    public function save(Request $request): JsonResponse
    {
        $symbols = $request->get('symbols', []);
        $this->exchangeRateService->saveRates(json_decode($symbols));

        return $this->json([
            'success' => true,
        ]);
    }
}
