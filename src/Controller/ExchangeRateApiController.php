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

    #[Route('/api/rates/last-24h', name: 'app_rate_last24h')]
    public function getLast24hRates(Request $request): JsonResponse
    {
        $pair = $request->get('pair');

        $symbols = explode('/', $pair);
        $symbols = implode('', array_reverse($symbols));
        $rates = $this->exchangeRateService->getLast24hRates($symbols);

        return $this->json([
            'data' => $rates,
        ]);
    }

    #[Route('/api/rates/day', name: 'app_rate_get_selected_day')]
    public function getSelectedDayRates(Request $request): JsonResponse
    {
        $pair = $request->get('pair');
        $date = $request->get('date');

        $symbols = explode('/', $pair);
        $symbols = implode('', array_reverse($symbols));
        $day = new \DateTimeImmutable($date);
        $rates = $this->exchangeRateService->getSelectedDayRates($symbols, $day);

        return $this->json([
            'data' => $rates,
        ]);
    }
}
