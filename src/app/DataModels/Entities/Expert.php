<?php

namespace App\DataModels\Entities;

use App\DataModels\Entities\Bags\ExpertPaymentsBagInterface;
use App\DataModels\Entities\Bags\PlacesBagInterface;
use App\DataModels\Entities\Bags\SpecializationsBagInterface;

class Expert extends AbstractExpert
{
    private int $id;
    private string $firstName;
    private string $lastName;
    private string $patronymic;
    private string $biography;
    private ?string $avatar;
    private ?string $video;
    private ?AbstractTelegramClient $telegramClient;
    private string $telegramPhoneNumber;
    private ?string $whatsappPhoneNumber;
    private ?float $priceWorkHour;
    private ?string $requisites;
    private float $balance;
    private ?bool $isVerification;
    private bool $isBlocked;
    private \DateTime $createdAt;
    private ?\DateTime $updatedAt;
    private ?PlacesBagInterface $placesBag;
    private ?SpecializationsBagInterface $specializationsBag;
    private ?ExpertPaymentsBagInterface $paymentsBag;

    public function __construct(
        int                          $id,
        string                       $firstName,
        string                       $lastName,
        string                       $patronymic,
        string                       $biography,
        string                       $telegramPhoneNumber,
        float                        $balance,
        bool                         $isBlocked,
        \DateTime                    $createdAt,
        ?bool                        $isVerification = null,
        ?AbstractTelegramClient      $telegramClient = null,
        ?float                       $priceWorkHour = null,
        ?string                      $avatar = null,
        ?string                      $video = null,
        ?string                      $whatsappPhoneNumber = null,
        ?string                      $requisites = null,
        ?PlacesBagInterface          $placesBag = null,
        ?SpecializationsBagInterface $specializationsBag = null,
        ?ExpertPaymentsBagInterface  $paymentsBag = null,
        ?\DateTime                   $updatedAt = null
    )
    {
        $this->id = $id;
        $this->avatar = $avatar;
        $this->video = $video;
        $this->biography = $biography;
        $this->requisites = $requisites;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->patronymic = $patronymic;
        $this->telegramClient = $telegramClient;
        $this->telegramPhoneNumber = $telegramPhoneNumber;
        $this->whatsappPhoneNumber = $whatsappPhoneNumber;
        $this->priceWorkHour = $priceWorkHour;
        $this->balance = $balance;
        $this->isVerification = $isVerification;
        $this->isBlocked = $isBlocked;
        $this->placesBag = $placesBag;
        $this->specializationsBag = $specializationsBag;
        $this->paymentsBag = $paymentsBag;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function getBiography(): string
    {
        return $this->biography;
    }

    public function getRequisites(): ?string
    {
        return $this->requisites;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getPatronymic(): string
    {
        return $this->patronymic;
    }

    public function getTelegramClient(): ?AbstractTelegramClient
    {
        return $this->telegramClient;
    }

    public function getTelegramPhoneNumber(): string
    {
        return $this->telegramPhoneNumber;
    }

    public function getWhatsappPhoneNumber(): ?string
    {
        return $this->whatsappPhoneNumber;
    }

    public function getPriceWorkHour(): ?float
    {
        return $this->priceWorkHour;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function getIsVerification(): ?bool
    {
        return $this->isVerification;
    }

    public function getIsBlocked(): bool
    {
        return $this->isBlocked;
    }

    public function getPlacesBag(): ?PlacesBagInterface
    {
        return $this->placesBag;
    }

    public function getSpecializationsBag(): ?SpecializationsBagInterface
    {
        return $this->specializationsBag;
    }

    public function getPaymentsBag(): ?ExpertPaymentsBagInterface
    {
        return $this->paymentsBag;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }
}
