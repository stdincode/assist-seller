<?php

namespace App\DataModels\Entities;

use App\DataModels\Entities\Bags\ExpertPaymentsBagInterface;
use App\DataModels\Entities\Bags\PlacesBagInterface;
use App\DataModels\Entities\Bags\SpecializationsBagInterface;

abstract class AbstractExpert implements EntityInterface
{
    /**
     * @return int
     */
    abstract public function getId(): int;

    /**
     * @return string|null
     */
    abstract public function getAvatar(): ?string;

    /**
     * @return string|null
     */
    abstract public function getVideo(): ?string;

    /**
     * @return string
     */
    abstract public function getBiography(): string;

    /**
     * @return string|null
     */
    abstract public function getRequisites(): ?string;

    /**
     * @return string
     */
    abstract public function getFirstName(): string;

    /**
     * @return string
     */
    abstract public function getLastName(): string;

    /**
     * @return string
     */
    abstract public function getPatronymic(): string;

    /**
     * @return AbstractTelegramClient|null
     */
    abstract public function getTelegramClient(): ?AbstractTelegramClient;

    /**
     * @return string
     */
    abstract public function getTelegramPhoneNumber(): string;

    /**
     * @return string|null
     */
    abstract public function getWhatsappPhoneNumber(): ?string;

    /**
     * @return float|null
     */
    abstract public function getPriceWorkHour(): ?float;

    /**
     * @return float
     */
    abstract public function getBalance(): float;

    /**
     * @return bool|null
     */
    abstract public function getIsVerification(): ?bool;

    /**
     * @return bool
     */
    abstract public function getIsBlocked(): bool;

    /**
     * @return PlacesBagInterface|null
     */
    abstract public function getPlacesBag(): ?PlacesBagInterface;

    /**
     * @return SpecializationsBagInterface|null
     */
    abstract public function getSpecializationsBag(): ?SpecializationsBagInterface;

    /**
     * @return ExpertPaymentsBagInterface|null
     */
    abstract public function getPaymentsBag(): ?ExpertPaymentsBagInterface;

    /**
     * @return \DateTime
     */
    abstract public function getCreatedAt(): \DateTime;

    /**
     * @return \DateTime|null
     */
    abstract public function getUpdatedAt(): ?\DateTime;

    public function asArray(): array
    {
        return [
            'id' => $this->getId(),
            'first_name' => $this->getFirstName(),
            'last_name' => $this->getLastName(),
            'patronymic' => $this->getPatronymic(),
            'biography' => $this->getBiography(),
            'avatar' => $this->getAvatar(),
            'video' => $this->getVideo(),
            'telegram_client' => $this->getTelegramClient()?->asArray(),
            'telegram_phone_number' => $this->getTelegramPhoneNumber(),
            'whatsapp_phone_number' => $this->getWhatsappPhoneNumber(),
            'price_work_hour' => $this->getPriceWorkHour(),
            'requisites' => $this->getRequisites(),
            'balance' => $this->getBalance(),
            'is_verification' => $this->getIsVerification(),
            'is_blocked' => $this->getIsBlocked(),
            'places' => $this->getPlacesBag()?->asArray(),
            'specializations' => $this->getSpecializationsBag()?->asArray(),
            'payments' => $this->getPaymentsBag()?->asArray(),
            'created_at' => $this->getCreatedAt(),
        ];
    }

}
