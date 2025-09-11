<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ExchangeRate;
use App\Service\ExchangeRateService;
use App\Validator\Pair as PairConstraint;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ExchangeRateApiController extends AbstractController
{
    public function __construct(private readonly ExchangeRateService $exchangeRateService, private readonly ValidatorInterface $validator, private readonly LoggerInterface $logger)
    {
    }

    #[Route('/api/rates/last-24h', name: 'app_rate_last24h')]
    public function getLast24hRates(Request $request): JsonResponse
    {
        $input = (string) $request->get('pair', 'BTC/USD');
        $pair = strtoupper(trim($input));
        $violations = $this->validator->validate($pair, [new PairConstraint()]);
        if (\count($violations) > 0) {
            $this->logger->error($violations[0]->getMessage());

            return $this->json(['error' => $violations[0]->getMessage()], 400);
        }
        [$base, $quote] = array_map('trim', explode('/', $pair));
        $symbols = $quote.$base; // e.g., BTC/USD -> USDBTC

        $rates = $this->exchangeRateService->getLast24hRates($symbols);

        return $this->json(['datasets' => $this->buildDataset($pair.' (last 24h)', $rates)]);
    }

    #[Route('/api/rates/day', name: 'app_rate_get_selected_day')]
    public function getSelectedDayRates(Request $request): JsonResponse
    {
        $input = (string) $request->get('pair', 'BTC/USD');
        $pair = strtoupper(trim($input));
        $violations = $this->validator->validate($pair, [new PairConstraint()]);
        $date = (string) $request->get('date');
        if (\count($violations) > 0) {
            $this->logger->error($violations[0]->getMessage());

            return $this->json(['error' => $violations[0]->getMessage()], 400);
        }
        if ('' === $date) {
            $dateErrorMessage = 'Missing date. Expected format YYYY-MM-DD or ISO date';
            $this->logger->error($dateErrorMessage);

            return $this->json(['error' => $dateErrorMessage], 400);
        }
        [$base, $quote] = array_map('trim', explode('/', $pair));
        $symbols = $quote.$base;

        try {
            $day = new \DateTimeImmutable($date);
        } catch (\DateMalformedStringException $e) {
            $this->logger->error($e->getMessage());

            return $this->json(['error' => $e->getMessage()], 400);
        }
        $rates = $this->exchangeRateService->getSelectedDayRates($symbols, $day);

        return $this->json(['datasets' => $this->buildDataset($pair.' ('.$day->format('Y-m-d').')', $rates)]);
    }

    /**
     * @param ExchangeRate[] $rates
     *
     * @return array<int, array{x: string, y: float}>
     */
    private function getDataPoints(array $rates): array
    {
        $dataPoints = array_map(static fn ($rate) => [
            'x' => $rate->getCreatedAt()->format('Y-m-d H:i:s'),
            'y' => (float) $rate->getPrice(),
        ], $rates);

        return $dataPoints;
    }

    /**
     * @param ExchangeRate[] $rates
     *
     * @return array<int, array{
     *     label: string,
     *     data: array<int, array{x: string, y: float}>,
     *     borderColor: string,
     *     backgroundColor: string,
     *     tension: float
     * }>
     */
    private function buildDataset(string $label, array $rates): array
    {
        return [[
            'label' => $label,
            'data' => $this->getDataPoints($rates),
            'borderColor' => 'rgba(75, 192, 192, 1)',
            'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
            'tension' => 0.1,
        ]];
    }
}
