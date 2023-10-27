<?php

namespace App\Repositories;


use App\DataModels\Entities\AbstractTelegramClient;
use App\DataModels\Entities\AbstractTelegramMenu;
use App\DataModels\Entities\AbstractTelegramMenuSession;
use App\DataModels\Entities\AbstractTelegramMenuVersion;
use Ramsey\Uuid\UuidInterface;

interface TelegramStorageRepositoryInterface
{
    public function getTelegramClient(int $id): ?AbstractTelegramClient;

    public function getExpertTelegramClient(int $expertId): ?AbstractTelegramClient;

    public function getStudentTelegramClient(int $studentId): ?AbstractTelegramClient;

    public function getClientByTelegramId(int $telegramId): ?AbstractTelegramClient;

    public function createTelegramClient(
        int $telegramId,
        ?string $telegramFirstName,
        ?string $telegramLastName,
        ?string $telegramUsername
    ): ?AbstractTelegramClient;

    public function getCommonTelegramMenu(): ?AbstractTelegramMenu;

    public function getExpertTelegramMenu(): ?AbstractTelegramMenu;
    public function getConsultationRequestExpertTelegramMenu(): ?AbstractTelegramMenu;

    public function getStudentTelegramMenu(): ?AbstractTelegramMenu;

    public function getLastTelegramMenuVersion(int $menuId): ?AbstractTelegramMenuVersion;

    public function getOpenTelegramSession(AbstractTelegramClient $telegramClient): ?AbstractTelegramMenuSession;

    public function getLastTelegramSession(AbstractTelegramClient $telegramClient): ?AbstractTelegramMenuSession;

    public function closeTelegramSession(int $sessionId): bool;

    public function createTelegramSession(
        AbstractTelegramClient $telegramClient,
        AbstractTelegramMenuVersion $telegramMenuVersion,
        int $chatId
    ): ?AbstractTelegramMenuSession;

    public function saveTelegramClientStep(UuidInterface $stepId, int $sessionId, int $telegramMessageId): bool;

    public function saveTelegramClientStepMessage(int $clientStepId, int $messageId, string $message): bool;

}
