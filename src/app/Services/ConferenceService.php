<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Ramsey\Uuid\UuidInterface;

class ConferenceService implements ConferenceServiceInterface
{
    private string $conferenceAppId;
    private string $conferenceAppHost;
    private string $conferenceAppSecretKey;

    public function __construct(
        string $conferenceAppId,
        string $conferenceAppHost,
        string $conferenceAppSecretKey
    )
    {
        $this->conferenceAppId = $conferenceAppId;
        $this->conferenceAppHost = $conferenceAppHost;
        $this->conferenceAppSecretKey = $conferenceAppSecretKey;
    }

    public function buildConferenceLink(
        UuidInterface $roomUuid,
        \DateTime $conferenceDateTime,
        string $memberName,
        ?string $memberAvatarLink = null,
        ?string $memberEmail = null
    ): string
    {
        $roomName = $roomUuid->toString();
        $user = [
            'name' => $memberName,
        ];
        if ($memberAvatarLink) $user['avatar'] = $memberAvatarLink ;
        if ($memberEmail) $user['email'] = $memberEmail;

        $payload = [
            'context' => [
                'user' => $user
            ],
            'aud' => $this->conferenceAppId,
            'iss' => $this->conferenceAppId,
            'sub' => $this->conferenceAppHost,
            'room' => $roomName,
            'exp' => $conferenceDateTime->modify('+2 hours')->getTimestamp(),
        ];

        $token = JWT::encode($payload, $this->conferenceAppSecretKey, 'HS256');

        return "https://$this->conferenceAppHost/$roomName?jwt=$token";
    }

}

