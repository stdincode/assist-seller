<?php

namespace App\Repositories;


use App\DataModels\Entities\Bags\TelegramClientStepMessagesBag;
use App\DataModels\Entities\Bags\TelegramClientStepMessagesBagInterface;
use App\DataModels\Entities\Bags\TelegramClientStepsBag;
use App\DataModels\Entities\TelegramClient;
use App\DataModels\Entities\AbstractTelegramClient;
use App\DataModels\Entities\AbstractTelegramMenu;
use App\DataModels\Entities\AbstractTelegramMenuSession;
use App\DataModels\Entities\AbstractTelegramMenuVersion;
use App\DataModels\Entities\TelegramClientMessage;
use App\DataModels\Entities\TelegramClientStep;
use App\DataModels\Entities\TelegramMenu;
use App\DataModels\Entities\TelegramMenuSession;
use App\DataModels\Entities\TelegramMenuVersion;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class TelegramStorageRepository implements TelegramStorageRepositoryInterface
{
    public const TEXT_ANSWER_IS_BACK = 'Назад';
    public const COMMON_MENU_NAME = 'common_menu';
    public const EXPERT_MENU_NAME = 'expert_menu';
    public const CONSULTATION_REQUEST_EXPERT_MENU_NAME = 'consultation_request_expert_menu';
    public const STUDENT_MENU_NAME = 'student_menu';
    private string $telegramClientsTableName;
    private string $telegramMenusTableName;
    private string $telegramMenuVersionsTableName;
    private string $telegramMenuSessionsTableName;
    private string $telegramClientStepsTableName;
    private string $telegramClientStepMessagesTableName;
    private string $expertsTableName;
    private string $studentsTableName;

    public function __construct(
        string $telegramClientsTableName,
        string $telegramMenusTableName,
        string $telegramMenuVersionsTableName,
        string $telegramMenuSessionsTableName,
        string $telegramClientStepsTableName,
        string $telegramClientStepMessagesTableName,
        string $expertsTableName,
        string $studentsTableName
    )
    {
        $this->telegramClientsTableName = $telegramClientsTableName;
        $this->telegramMenusTableName = $telegramMenusTableName;
        $this->telegramMenuVersionsTableName = $telegramMenuVersionsTableName;
        $this->telegramMenuSessionsTableName = $telegramMenuSessionsTableName;
        $this->telegramClientStepsTableName = $telegramClientStepsTableName;
        $this->telegramClientStepMessagesTableName = $telegramClientStepMessagesTableName;
        $this->expertsTableName = $expertsTableName;
        $this->studentsTableName = $studentsTableName;
    }

    public function getTelegramClient(int $id): ?AbstractTelegramClient
    {
        $client = DB::table($this->telegramClientsTableName)
            ->select([
                'id',
                'telegram_id',
                'telegram_first_name',
                'telegram_last_name',
                'telegram_username',
                'created_at',
                'updated_at',
            ])
            ->where([
                'id' => $id,
            ])
            ->first();

        if (!$client) return null;

        return $this->buildTelegramClientDataModel($client);
    }

    public function getExpertTelegramClient(int $expertId): ?AbstractTelegramClient
    {
        $client = DB::table("$this->telegramClientsTableName as tc")
            ->leftJoin("$this->expertsTableName as e", 'e.telegram_client_id', '=', 'tc.id')
            ->select([
                'tc.id as id',
                'tc.telegram_id as telegram_id',
                'tc.telegram_first_name as telegram_first_name',
                'tc.telegram_last_name as telegram_last_name',
                'tc.telegram_username as telegram_username',
                'tc.created_at as created_at',
                'tc.updated_at as updated_at',
            ])
            ->where([
                'e.id' => $expertId,
            ])
            ->first();

        if (!$client) return null;

        return $this->buildTelegramClientDataModel($client);
    }

    public function getStudentTelegramClient(int $studentId): ?AbstractTelegramClient
    {
        $client = DB::table("$this->telegramClientsTableName as tc")
            ->leftJoin("$this->studentsTableName as s", 's.telegram_client_id', '=', 'tc.id')
            ->select([
                'tc.id as id',
                'tc.telegram_id as telegram_id',
                'tc.telegram_first_name as telegram_first_name',
                'tc.telegram_last_name as telegram_last_name',
                'tc.telegram_username as telegram_username',
                'tc.created_at as created_at',
                'tc.updated_at as updated_at',
            ])
            ->where([
                's.id' => $studentId,
            ])
            ->first();

        if (!$client) return null;

        return $this->buildTelegramClientDataModel($client);
    }

    public function getClientByTelegramId(int $telegramId): ?AbstractTelegramClient
    {
        $client = DB::table($this->telegramClientsTableName)
            ->select([
                'id',
                'telegram_id',
                'telegram_first_name',
                'telegram_last_name',
                'telegram_username',
                'created_at',
                'updated_at',
            ])
            ->where([
                'telegram_id' => $telegramId,
            ])
            ->first();

        if (!$client) return null;

        return $this->buildTelegramClientDataModel($client);
    }

    public function createTelegramClient(
        int $telegramId,
        ?string $telegramFirstName,
        ?string $telegramLastName,
        ?string $telegramUsername
    ): ?AbstractTelegramClient
    {
        $createdAt = new \DateTime();
        $id = DB::table($this->telegramClientsTableName)
            ->insertGetId([
                'telegram_id' => $telegramId,
                'telegram_first_name' => $telegramFirstName,
                'telegram_last_name' => $telegramLastName,
                'telegram_username' => $telegramUsername,
                'created_at' => $createdAt,
            ]);

        if (!$id) return null;

        return new TelegramClient(
            id: $id,
            telegramId: $telegramId,
            telegramFirstName: $telegramFirstName,
            telegramLastName: $telegramLastName,
            telegramUsername: $telegramUsername,
            createdAt: $createdAt
        );
    }

    public function getCommonTelegramMenu(): ?AbstractTelegramMenu
    {
        $menu = DB::table($this->telegramMenusTableName)
            ->select([
                'id',
                'name',
            ])
            ->where(['name' => self::COMMON_MENU_NAME,])
            ->first();

        if (!$menu) return null;

        return $this->createTelegramMenuDataModel($menu);
    }

    public function getExpertTelegramMenu(): ?AbstractTelegramMenu
    {
        $menu = DB::table($this->telegramMenusTableName)
            ->select([
                'id',
                'name',
            ])
            ->where(['name' => self::EXPERT_MENU_NAME,])
            ->first();

        if (!$menu) return null;

        return $this->createTelegramMenuDataModel($menu);
    }

    public function getConsultationRequestExpertTelegramMenu(): ?AbstractTelegramMenu
    {
        $menu = DB::table($this->telegramMenusTableName)
            ->select([
                'id',
                'name',
            ])
            ->where(['name' => self::CONSULTATION_REQUEST_EXPERT_MENU_NAME,])
            ->first();

        if (!$menu) return null;

        return $this->createTelegramMenuDataModel($menu);
    }

    public function getStudentTelegramMenu(): ?AbstractTelegramMenu
    {
        $menu = DB::table($this->telegramMenusTableName)
            ->select([
                'id',
                'name',
            ])
            ->where(['name' => self::STUDENT_MENU_NAME,])
            ->first();

        if (!$menu) return null;

        return $this->createTelegramMenuDataModel($menu);
    }

    public function getLastTelegramMenuVersion(int $menuId): ?AbstractTelegramMenuVersion
    {
        $menuVersion = DB::table("{$this->telegramMenuVersionsTableName} as tmv")
            ->leftJoin("{$this->telegramMenusTableName} as tm", 'tm.id', '=', 'tmv.telegram_menu_id')
            ->select([
                'tmv.id as id',
                'tm.id as menu_id',
                'tm.name as menu_name',
                'tmv.start_step_id as start_step_id',
                'tmv.updated_at as updated_at',
                'tmv.created_at as created_at',
            ])
            ->where([
                'tm.id' => $menuId,
            ])
            ->orderBy('tmv.created_at', 'desc')
            ->first();

        if (!$menuVersion) return null;

        return new TelegramMenuVersion(
            id: $menuVersion->id,
            telegramMenu: new TelegramMenu(
                id: $menuVersion->menu_id,
                name: $menuVersion->menu_name
            ),
            startStepId: Uuid::fromString($menuVersion->start_step_id),
            createdAt: new \DateTime($menuVersion->created_at),
            updatedAt: $menuVersion->updated_at ? new \DateTime($menuVersion->updated_at) : null
        );
    }
    public function getOpenTelegramSession(AbstractTelegramClient $telegramClient): ?AbstractTelegramMenuSession
    {
        $hours = config('telegram.telegram_menu_session_life_time');
        $minSessionLifeDateTime = (new \DateTime())->modify("-{$hours} hours");

        $session = DB::table("{$this->telegramMenuSessionsTableName} as tms")
            ->leftJoin("{$this->telegramMenuVersionsTableName} as tmv", 'tmv.id', '=', 'tms.telegram_menu_version_id')
            ->leftJoin("{$this->telegramMenusTableName} as tm", 'tm.id', '=', 'tmv.telegram_menu_id')
            ->select([
                'tms.id as id',
                'tms.telegram_chat_id as telegram_chat_id',
                'tm.id as menu_id',
                'tm.name as menu_name',
                'tmv.id as menu_version_id',
                'tmv.start_step_id as menu_version_start_step_id',
                'tmv.updated_at as menu_version_updated_at',
                'tmv.created_at as menu_version_created_at',
                'tms.closed_at as closed_at',
                'tms.updated_at as updated_at',
                'tms.created_at as created_at',
            ])
            ->where([
                'tms.telegram_client_id' => $telegramClient->getId(),
                'tms.closed_at' => null,
            ])
            ->where('tms.created_at', '>', $minSessionLifeDateTime)
            ->orderBy('tms.created_at', 'desc')
            ->first();

        if (!$session) return null;

        $telegramClientSteps = DB::table($this->telegramClientStepsTableName)
            ->select([
                'id',
                'step_id',
                'telegram_message_id',
                'created_at',
            ])
            ->where(['telegram_menu_session_id' => $session->id,])
            ->orderBy('created_at')
            ->get()
            ->all();


        $telegramClientStepsBag = new TelegramClientStepsBag();
        array_map(
            function ($telegramClientStep) use ($telegramClientStepsBag) {
                $telegramClientStepMessagesBag = $this->getTelegramClientStepMessagesBag(telegramClientStepId: $telegramClientStep->id);

                $telegramClientStepsBag->add(new TelegramClientStep(
                    id: $telegramClientStep->id,
                    stepId: Uuid::fromString($telegramClientStep->step_id),
                    telegramMessageId: $telegramClientStep->telegram_message_id,
                    stepMessagesBag: $telegramClientStepMessagesBag,
                    createdAt: new \DateTime($telegramClientStep->created_at))
                );
            },
            $telegramClientSteps
        );

        return new TelegramMenuSession(
            id: $session->id,
            telegramChatId: $session->telegram_chat_id,
            telegramClient: $telegramClient,
            telegramMenuVersion: new TelegramMenuVersion(
                id: $session->menu_version_id,
                telegramMenu: new TelegramMenu(
                    id: $session->menu_id,
                    name: $session->menu_name
                ),
                startStepId: Uuid::fromString($session->menu_version_start_step_id),
                createdAt: new \DateTime($session->menu_version_created_at),
                updatedAt: $session->menu_version_updated_at ? new \DateTime($session->menu_version_updated_at) : null
            ),
            telegramClientStepsBag: $telegramClientStepsBag,
            createdAt: new \DateTime($session->created_at),
            updatedAt: $session->updated_at ? new \DateTime($session->updated_at) : null,
            closedAt: $session->closed_at ? new \DateTime($session->closed_at) : null
        );
    }

    public function getLastTelegramSession(AbstractTelegramClient $telegramClient): ?AbstractTelegramMenuSession
    {
        $session = DB::table("{$this->telegramMenuSessionsTableName} as tms")
            ->leftJoin("{$this->telegramMenuVersionsTableName} as tmv", 'tmv.id', '=', 'tms.telegram_menu_version_id')
            ->leftJoin("{$this->telegramMenusTableName} as tm", 'tm.id', '=', 'tmv.telegram_menu_id')
            ->select([
                'tms.id as id',
                'tms.telegram_chat_id as telegram_chat_id',
                'tm.id as menu_id',
                'tm.name as menu_name',
                'tmv.id as menu_version_id',
                'tmv.start_step_id as menu_version_start_step_id',
                'tmv.updated_at as menu_version_updated_at',
                'tmv.created_at as menu_version_created_at',
                'tms.closed_at as closed_at',
                'tms.updated_at as updated_at',
                'tms.created_at as created_at',
            ])
            ->where([
                'tms.telegram_client_id' => $telegramClient->getId(),
            ])
            ->orderBy('tms.created_at', 'desc')
            ->first();

        if (!$session) return null;

        return new TelegramMenuSession(
            id: $session->id,
            telegramChatId: $session->telegram_chat_id,
            telegramClient: $telegramClient,
            telegramMenuVersion: new TelegramMenuVersion(
                id: $session->menu_version_id,
                telegramMenu: new TelegramMenu(
                    id: $session->menu_id,
                    name: $session->menu_name
                ),
                startStepId: Uuid::fromString($session->menu_version_start_step_id),
                createdAt: new \DateTime($session->menu_version_created_at),
                updatedAt: $session->menu_version_updated_at ? new \DateTime($session->menu_version_updated_at) : null
            ),
            telegramClientStepsBag: new TelegramClientStepsBag(),
            createdAt: new \DateTime($session->created_at),
            updatedAt: $session->updated_at ? new \DateTime($session->updated_at) : null,
            closedAt: $session->closed_at ? new \DateTime($session->closed_at) : null
        );
    }

    public function createTelegramSession(
        AbstractTelegramClient $telegramClient,
        AbstractTelegramMenuVersion $telegramMenuVersion,
        int $chatId
    ): ?AbstractTelegramMenuSession
    {
        $createdAt = new \DateTime();

        $sessionId = DB::table($this->telegramMenuSessionsTableName)
            ->insertGetId([
                'telegram_client_id' => $telegramClient->getId(),
                'telegram_chat_id' => $chatId,
                'telegram_menu_version_id' => $telegramMenuVersion->getId(),
                'created_at' => $createdAt,
            ]);

        if (!$sessionId) return null;

        return new TelegramMenuSession(
            id: $sessionId,
            telegramChatId: $chatId,
            telegramClient: $telegramClient,
            telegramMenuVersion: $telegramMenuVersion,
            telegramClientStepsBag: new TelegramClientStepsBag(),
            createdAt: $createdAt
        );
    }

    public function closeTelegramSession(int $sessionId): bool
    {
        $updatedAt = new \DateTime();

        return DB::table($this->telegramMenuSessionsTableName)
            ->where(['id' => $sessionId])
            ->update([
                'closed_at' => $updatedAt,
                'updated_at' => $updatedAt,
            ]);
    }

    public function saveTelegramClientStep(UuidInterface $stepId, int $sessionId, int $telegramMessageId): bool
    {
        $createdAt = new \DateTime();

        $clientStep = DB::table($this->telegramClientStepsTableName)
            ->insertGetId([
                'step_id' => $stepId->toString(),
                'telegram_message_id' => $telegramMessageId,
                'telegram_menu_session_id' => $sessionId,
                'created_at' => $createdAt,
            ]);

        return (bool)$clientStep;
    }

    public function saveTelegramClientStepMessage(int $clientStepId, int $messageId, string $message): bool
    {
        $createdAt = new \DateTime();

        $clientStepMessage = DB::table($this->telegramClientStepMessagesTableName)
            ->insertGetId([
                'telegram_client_step_id' => $clientStepId,
                'telegram_message_id' => $messageId,
                'message' => $message,
                'created_at' => $createdAt,
            ]);

        return (bool)$clientStepMessage;
    }

    private function createTelegramMenuDataModel($menu): AbstractTelegramMenu
    {
        return new TelegramMenu(id: $menu->id, name: $menu->name);
    }

    private function getTelegramClientStepMessagesBag(int $telegramClientStepId): TelegramClientStepMessagesBagInterface
    {
        $telegramClientStepMessages = DB::table($this->telegramClientStepMessagesTableName)
            ->select([
                'id',
                'telegram_message_id',
                'message',
                'created_at',
            ])
            ->where(['telegram_client_step_id' => $telegramClientStepId,])
            ->orderBy('created_at')
            ->get()
            ->all();

        $telegramClientStepMessagesBag = new TelegramClientStepMessagesBag();
        array_map(
            function ($telegramClientStepMessage) use ($telegramClientStepMessagesBag) {
                $telegramClientStepMessagesBag->add(new TelegramClientMessage(
                        id: $telegramClientStepMessage->id,
                        telegramMessageId: $telegramClientStepMessage->telegram_message_id,
                        telegramMessage: $telegramClientStepMessage->message,
                        createdAt: new \DateTime($telegramClientStepMessage->created_at))
                );
            },
            $telegramClientStepMessages
        );

        return $telegramClientStepMessagesBag;
    }

    private function buildTelegramClientDataModel($telegramClient): TelegramClient
    {
        return new TelegramClient(
            id: $telegramClient->id,
            telegramId: $telegramClient->telegram_id,
            telegramFirstName: $telegramClient->telegram_first_name,
            telegramLastName: $telegramClient->telegram_last_name,
            telegramUsername: $telegramClient->telegram_username,
            createdAt: new \DateTime($telegramClient->created_at),
            updatedAt: $telegramClient->updated_at ? new \DateTime($telegramClient->updated_at) : null
        );
    }
}
