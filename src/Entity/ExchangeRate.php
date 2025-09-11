<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ExchangeRateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

#[ORM\Entity(repositoryClass: ExchangeRateRepository::class)]
class ExchangeRate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private readonly int $id;

    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s'])]
    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct(#[ORM\Column(length: 7)]
        private string $symbol, #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 8)]
        private string $price)
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): static
    {
        $this->symbol = $symbol;

        return $this;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
