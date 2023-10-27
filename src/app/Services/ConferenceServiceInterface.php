<?php

namespace App\Services;

use Ramsey\Uuid\UuidInterface;

interface ConferenceServiceInterface
{
    public function buildConferenceLink(
        UuidInterface $roomUuid,
        \DateTime $conferenceDateTime,
        string $memberName,
        ?string $memberAvatarLink = null,
        ?string $memberEmail = null
    ): string;

}
