<?php

namespace App\Repositories;

use App\DataModels\Entities\Bags\ExpertPaymentsBag;
use App\DataModels\Entities\Bags\ExpertPaymentsBagInterface;
use App\DataModels\Entities\Bags\ExpertPaymentStatusesBagInterface;
use App\DataModels\Entities\Bags\ExpertsBagInterface;
use App\DataModels\Entities\Bags\PlacesBag;
use App\DataModels\Entities\Bags\PlacesBagInterface;
use App\DataModels\Entities\Bags\SpecializationsBag;
use App\DataModels\Entities\Bags\SpecializationsBagInterface;
use App\DataModels\Entities\Expert;
use App\DataModels\Entities\AbstractExpert;
use App\DataModels\Entities\ExpertPayment;
use App\DataModels\Entities\AbstractExpertPayment;
use App\DataModels\Entities\ExpertPaymentStatus;
use App\DataModels\Entities\Place;
use App\DataModels\Entities\Specialization;
use App\DataModels\Entities\TelegramClient;
use App\DataModels\Entities\AbstractTelegramClient;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class ExpertStorageRepository implements ExpertStorageRepositoryInterface
{
    public const EXPERT_PAYMENT_STATUSES = [
        [
            'id' => 1,
            'name' => 'Запрос',
        ],
        [
            'id' => 2,
            'name' => 'Отклонена',
        ],
        [
            'id' => 3,
            'name' => 'Проведена',
        ],
    ];
    private string $expertsTableName;
    private string $expertPlacesTableName;
    private string $placesTableName;
    private string $expertSpecializationsTableName;
    private string $specializationsTableName;
    private string $expertPaymentsTableName;
    private string $expertPaymentStatusesTableName;
    private string $telegramClient;

    public function __construct(
        string $expertsTableName,
        string $expertPlacesTableName,
        string $expertSpecializationsTableName,
        string $expertPaymentsTableName,
        string $expertPaymentStatusesTableName,
        string $placesTableName,
        string $specializationsTableName,
        string $telegramClient
    )
    {
        $this->expertsTableName = $expertsTableName;
        $this->expertPlacesTableName = $expertPlacesTableName;
        $this->expertSpecializationsTableName = $expertSpecializationsTableName;
        $this->expertPaymentsTableName = $expertPaymentsTableName;
        $this->expertPaymentStatusesTableName = $expertPaymentStatusesTableName;
        $this->placesTableName = $placesTableName;
        $this->specializationsTableName = $specializationsTableName;
        $this->telegramClient = $telegramClient;
    }

    public function getExperts(ExpertsBagInterface $expertsBag): ExpertsBagInterface
    {
        $experts = $this->buildQueryGetExperts()->get();

        foreach ($experts->all() as $expert) {
            $expertsBag->add($this->createExpertDataModel(expert: $expert));
        }

        $expertsBag->setTotal($experts->count());

        return $expertsBag;
    }

    public function getExpertById(int $id): ?AbstractExpert
    {
        $expert = $this->buildQueryGetExperts()
            ->where(['e.id' => $id])
            ->first();

        if (!$expert) return null;

        return $this->createExpertDataModel(expert: $expert, withPayments: true);
    }

    public function getExpertByTelegramClientId(int $telegramClientId): ?AbstractExpert
    {
        $expert = $this->buildQueryGetExperts()
            ->where(['tc.id' => $telegramClientId])
            ->first();

        if (!$expert) return null;

        return $this->createExpertDataModel(expert: $expert);
    }

    public function getExpertByTelegramPhoneNumber(int $telegramPhoneNumber): ?AbstractExpert
    {
        $expert = $this->buildQueryGetExperts()
            ->where(['e.telegram_phone_number' => $telegramPhoneNumber])
            ->first();

        if (!$expert) return null;

        return $this->createExpertDataModel(expert: $expert);
    }

    public function getExpertByWhatsappPhoneNumber(int $whatsappPhoneNumber): ?AbstractExpert
    {
        $expert = $this->buildQueryGetExperts()
            ->where(['e.whatsapp_phone_number' => $whatsappPhoneNumber])
            ->first();

        if (!$expert) return null;

        return $this->createExpertDataModel(expert: $expert);
    }

    public function createExpert(
        string                 $firstName,
        string                 $lastName,
        string                 $patronymic,
        string                 $biography,
        AbstractTelegramClient $telegramClient,
        int                    $telegramPhoneNumber,
        ?int                   $whatsappPhoneNumber,
        float                  $priceWorkHour,
        string                 $requisites,
        ?string                $avatar,
        ?string                $video,
        array                  $placeIds,
        array                  $specializationIds
    ): ?AbstractExpert
    {
        $isVerification = null;
        $isBlocked = false;
        $balance = 0.0;
        $currentDateTime = new \DateTime();
        $paymentsBag = new ExpertPaymentsBag();

        $id = DB::table($this->expertsTableName)
            ->insertGetId(
                [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'patronymic' => $patronymic,
                    'biography' => $biography,
                    'telegram_client_id' => $telegramClient->getId(),
                    'telegram_phone_number' => $telegramPhoneNumber,
                    'whatsapp_phone_number' => $whatsappPhoneNumber,
                    'price_work_hour' => $priceWorkHour,
                    'requisites' => $requisites,
                    'avatar' => $avatar,
                    'video' => $video,
                    'balance' => $balance,
                    'is_verification' => $isVerification,
                    'is_blocked' => $isBlocked,
                    'created_at' => $currentDateTime,
                ]
            );
        if (!$id) return null;

        $expertPlacesBag = $this->insertExpertPlaces($id, $placeIds);

        $expertSpecializationsBag = $this->insertExpertSpecializations($id, $specializationIds);

        return new Expert(
            id: $id,
            firstName: $firstName,
            lastName: $lastName,
            patronymic: $patronymic,
            biography: $biography,
            telegramPhoneNumber: $telegramPhoneNumber,
            balance: $balance,
            isBlocked: $isBlocked,
            createdAt: $currentDateTime,
            isVerification: $isVerification,
            telegramClient: $telegramClient,
            priceWorkHour: $priceWorkHour,
            avatar: $avatar,
            video: $video,
            whatsappPhoneNumber: $whatsappPhoneNumber,
            requisites: $requisites,
            placesBag: $expertPlacesBag ?? null,
            specializationsBag: $expertSpecializationsBag ?? null,
            paymentsBag: $paymentsBag
        );
    }

    public function updateExpert(
        int     $id,
        ?string $firstName,
        ?string $lastName,
        ?string $patronymic,
        ?string $biography,
        ?string $avatar,
        ?string $video,
        ?int    $telegramPhoneNumber,
        ?int    $whatsappPhoneNumber,
        ?float  $priceWorkHour,
        ?string $requisites,
        ?float  $balance,
        ?bool   $isVerification,
        ?bool   $isBlocked,
        ?array  $placeIds,
        ?array  $specializationIds
    ): bool
    {
        $values['updated_at'] = new \DateTime();

        if ($telegramPhoneNumber !== null) $values['telegram_phone_number'] = $telegramPhoneNumber;
        if ($whatsappPhoneNumber !== null) $values['whatsapp_phone_number'] = $whatsappPhoneNumber;
        if ($firstName !== null) $values['first_name'] = $firstName;
        if ($lastName !== null) $values['last_name'] = $lastName;
        if ($patronymic !== null) $values['patronymic'] = $patronymic;
        if ($biography !== null) $values['biography'] = $biography;
        if ($avatar !== null) $values['avatar'] = $avatar;
        if ($video !== null) $values['video'] = $video;
        if ($priceWorkHour !== null) $values['price_work_hour'] = $priceWorkHour;
        if ($requisites !== null) $values['requisites'] = $requisites;
        if ($balance !== null) $values['balance'] = $balance;
        if ($isVerification !== null) $values['is_verification'] = $isVerification;
        if ($isBlocked !== null) $values['is_blocked'] = $isBlocked;

        if ($placeIds !== null) {
            $placesUpdateResult = DB::table($this->expertPlacesTableName)->where(['expert_id' => $id])->delete();
            if (!empty($placeIds)) {
                $this->insertExpertPlaces($id, $placeIds);
            }
        } else {
            $placesUpdateResult = false;
        }

        if ($specializationIds !== null) {
            $specializationUpdateResult = DB::table($this->expertSpecializationsTableName)->where(['expert_id' => $id])->delete();
            if (!empty($specializationIds)) {
                $this->insertExpertSpecializations($id, $specializationIds);
            }
        } else {
            $specializationUpdateResult = false;
        }

        if (count($values) > 1) {
            $updateResult = DB::table($this->expertsTableName)
                ->where(['id' => $id])
                ->update($values);
        } else {
            $updateResult = false;
        }

        return $updateResult || $placesUpdateResult || $specializationUpdateResult;
    }

    public function deleteExpert(int $id): bool
    {
        DB::table($this->expertPlacesTableName)
            ->where(['expert_id' => $id])
            ->delete();

        DB::table($this->expertSpecializationsTableName)
            ->where(['expert_id' => $id])
            ->delete();

        return DB::table($this->expertsTableName)
            ->where(['id' => $id])
            ->delete();
    }

    public function getExpertPayments(int $expertId, ExpertPaymentsBagInterface $expertPaymentsBag): ExpertPaymentsBagInterface
    {
        $expertPayments = DB::table("{$this->expertPaymentsTableName} as ep")
            ->leftJoin("{$this->expertPaymentStatusesTableName} as eps", 'eps.id', '=', 'ep.status_id')
            ->select([
                'ep.id as id',
                'ep.amount as amount',
                'eps.id as status_id',
                'eps.name as status_name',
                'ep.created_at as created_at',
            ])
            ->where(['ep.expert_id' => $expertId])
            ->get();
        foreach ($expertPayments->all() as $expertPayment) {
            $expertPaymentStatus = new ExpertPaymentStatus(
                id: $expertPayment->status_id,
                name: $expertPayment->status_name
            );

            $expertPaymentsBag->add(
                new ExpertPayment(
                    id: $expertPayment->id,
                    amount: $expertPayment->amount,
                    status: $expertPaymentStatus,
                    createdAt: new \DateTime($expertPayment->created_at)
                )
            );
        }

        $expertPaymentsBag->setTotal($expertPayments->count());

        return $expertPaymentsBag;
    }

    public function getExpertPayment(int $expertPaymentId): ?AbstractExpertPayment
    {
        $expertPayment = DB::table("{$this->expertPaymentsTableName} as ep")
            ->leftJoin("{$this->expertPaymentStatusesTableName} as eps", 'eps.id', '=', 'ep.status_id')
            ->select([
                'ep.id as id',
                'ep.amount as amount',
                'eps.id as status_id',
                'eps.name as status_name',
                'ep.created_at as created_at',
                'ep.updated_at as updated_at',
            ])
            ->where(['ep.id' => $expertPaymentId])
            ->first();

        if (!$expertPayment) return null;

        return new ExpertPayment(
            id: $expertPayment->id,
            amount: $expertPayment->amount,
            status: new ExpertPaymentStatus(
                id: $expertPayment->status_id,
                name: $expertPayment->status_name
            ),
            createdAt: new \DateTime($expertPayment->created_at),
            updatedAt: $expertPayment->updated_at ? new \DateTime($expertPayment->updated_at) : null
        );
    }

    public function getLastWaitingExpertPayment(int $expertId): ?AbstractExpertPayment
    {
        $expertPayment = DB::table("{$this->expertPaymentsTableName} as ep")
            ->leftJoin("{$this->expertPaymentStatusesTableName} as eps", 'eps.id', '=', 'ep.status_id')
            ->select([
                'ep.id as id',
                'ep.amount as amount',
                'eps.id as status_id',
                'eps.name as status_name',
                'ep.created_at as created_at',
                'ep.updated_at as updated_at',
            ])
            ->where([
                'ep.expert_id' => $expertId,
                'ep.status_id' => self::EXPERT_PAYMENT_STATUSES[0]['id'],
            ])
            ->first();

        if (!$expertPayment) return null;

        return new ExpertPayment(
            id: $expertPayment->id,
            amount: $expertPayment->amount,
            status: new ExpertPaymentStatus(
                id: $expertPayment->status_id,
                name: $expertPayment->status_name
            ),
            createdAt: new \DateTime($expertPayment->created_at),
            updatedAt: $expertPayment->updated_at ? new \DateTime($expertPayment->updated_at) : null
        );
    }

    public function createExpertPayment(
        int $expertId,
        float $amount,
        int $paymentStatusId,
        string $paymentStatusName
    ): ?AbstractExpertPayment
    {
        $currentDatetime = new \DateTime();

        $id = DB::table($this->expertPaymentsTableName)
            ->insert([
                'expert_id' => $expertId,
                'amount' => $amount,
                'status_id' => $paymentStatusId,
                'created_at' => $currentDatetime,
            ]);
        if (!$id) return null;

        $expertPaymentStatus = new ExpertPaymentStatus(
            id: $paymentStatusId,
            name: $paymentStatusName
        );

        return new ExpertPayment(
            id: $id,
            amount: $amount,
            status: $expertPaymentStatus,
            createdAt: $currentDatetime
        );
    }

    public function updateExpertPayment(int $id, int $statusId): bool
    {
        $currentDatetime = new \DateTime();

        $status = DB::table($this->expertPaymentStatusesTableName)
            ->where(['id' => $statusId])
            ->first();
        if (!$status) return false;

        return DB::table($this->expertPaymentsTableName)
            ->where(['id' => $id])
            ->update([
                'status_id' => $statusId,
                'updated_at' => $currentDatetime,
            ]);
    }
    public function getExpertPaymentStatuses(ExpertPaymentStatusesBagInterface $expertPaymentStatusesBag): ExpertPaymentStatusesBagInterface
    {
        $expertPaymentStatuses = DB::table($this->expertPaymentStatusesTableName)->get();

        foreach ($expertPaymentStatuses->all() as $expertPaymentStatus) {
            $expertPaymentStatusesBag->add(new ExpertPaymentStatus(
                id: $expertPaymentStatus->id,
                name: $expertPaymentStatus->name
            ));
        }

        $expertPaymentStatusesBag->setTotal($expertPaymentStatuses->count());

        return $expertPaymentStatusesBag;
    }

    public function getRelevantExperts(ExpertsBagInterface $expertsBag, int $placeId, int $specializationId): ExpertsBagInterface
    {
        $experts = $this->buildQueryGetExperts()
            ->leftJoin("{$this->expertPlacesTableName} as ep", 'ep.expert_id', '=', 'e.id')
            ->leftJoin("{$this->expertSpecializationsTableName} as es", 'es.expert_id', '=', 'e.id')
            ->where([
                'ep.place_id' => $placeId,
                'es.specialization_id' => $specializationId,
                'e.is_blocked' => false,
                'e.is_verification' => true,
            ])
            ->get();

        foreach ($experts->all() as $expert) {
            $expertsBag->add($this->createExpertDataModel(expert: $expert));
        }

        $expertsBag->setTotal($experts->count());

        return $expertsBag;
    }

    private function insertExpertPlaces(int $expertId, array $placeIds): ?PlacesBagInterface
    {
        $places = DB::table($this->placesTableName)
            ->get()
            ->toArray();

        $expertPlacesBag = new PlacesBag();
        $insertPlaces = [];
        foreach ($placeIds as $placeId) {
            $key = array_search($placeId, array_column($places, 'id'));
            if ($key !== false) {
                $insertPlaces[] = [
                    'expert_id' => $expertId,
                    'place_id' => $placeId,
                ];
                $place = $places[$key];
                $expertPlacesBag->add(new Place(id: $place->id, name: $place->name));
            }
        }
        $resultInsert = DB::table($this->expertPlacesTableName)->insert($insertPlaces);

        return $resultInsert ? $expertPlacesBag : null;
    }

    private function insertExpertSpecializations(int $expertId, array $specializationIds): ?SpecializationsBagInterface
    {
        $specializations = DB::table($this->specializationsTableName)
            ->get()
            ->toArray();

        $expertSpecializationsBag = new SpecializationsBag();
        $insertSpecializations = [];
        foreach ($specializationIds as $specializationId) {
            $key = array_search($specializationId, array_column($specializations, 'id'));
            if ($key !== false) {
                $insertSpecializations[] = [
                    'expert_id' => $expertId,
                    'specialization_id' => $specializationId,
                ];
                $specialization = $specializations[$key];
                $expertSpecializationsBag->add(new Specialization(id: $specialization->id, name: $specialization->name));
            }
        }
        $resultInsert = DB::table($this->expertSpecializationsTableName)->insert($insertSpecializations);

        return $resultInsert ? $expertSpecializationsBag : null;
    }

    private function buildQueryGetExperts(): Builder
    {
        return DB::table("{$this->expertsTableName} as e")
            ->join("{$this->telegramClient} as tc", 'tc.id', '=', 'e.telegram_client_id')
            ->select([
                'e.id as id',
                'e.first_name as first_name',
                'e.last_name as last_name',
                'e.patronymic as patronymic',
                'e.biography as biography',
                'e.avatar as avatar',
                'e.video as video',
                'tc.id as telegram_client_id',
                'tc.telegram_id as telegram_id',
                'tc.telegram_first_name as telegram_first_name',
                'tc.telegram_last_name as telegram_last_name',
                'tc.telegram_username as telegram_username',
                'tc.created_at as telegram_client_created_at',
                'e.telegram_phone_number as telegram_phone_number',
                'e.whatsapp_phone_number as whatsapp_phone_number',
                'e.price_work_hour as price_work_hour',
                'e.requisites as requisites',
                'e.balance as balance',
                'e.is_verification as is_verification',
                'e.is_blocked as is_blocked',
                'e.created_at as created_at',
            ]);
    }

    private function getExpertPlacesBag(int $id): PlacesBagInterface
    {
        $expertPlaces = DB::table("{$this->expertPlacesTableName} as ep")
            ->leftJoin("{$this->placesTableName} as p", 'p.id', '=', 'ep.place_id')
            ->select([
                'p.id as id',
                'p.name as name',
            ])
            ->where(['ep.expert_id' => $id])
            ->get();

        $expertPlacesBag = new PlacesBag();
        foreach ($expertPlaces->all() as $expertPlace) {
            $expertPlacesBag->add(
                new Place(id: $expertPlace->id, name: $expertPlace->name)
            );
        }

        return $expertPlacesBag;
    }

    private function getExpertSpecializationsBag(int $id): SpecializationsBagInterface
    {
        $expertSpecializations = DB::table("{$this->expertSpecializationsTableName} as es")
            ->leftJoin("{$this->specializationsTableName} as s", 's.id', '=', 'es.specialization_id')
            ->select([
                's.id as id',
                's.name as name',
            ])
            ->where(['es.expert_id' => $id])
            ->get();

        $expertSpecializationsBag = new SpecializationsBag();
        foreach ($expertSpecializations->all() as $expertSpecialization) {
            $expertSpecializationsBag->add(
                new Specialization(id: $expertSpecialization->id, name: $expertSpecialization->name)
            );
        }

        return $expertSpecializationsBag;
    }

    private function createExpertDataModel($expert, $withPayments = false): Expert
    {
        $paymentsBag = new ExpertPaymentsBag();
        if ($withPayments) {
            $payments = $this->getExpertPayments(
                expertId: $expert->id,
                expertPaymentsBag: $paymentsBag
            );
        }

        $expertPlacesBag = $this->getExpertPlacesBag($expert->id);
        $expertSpecializationsBag = $this->getExpertSpecializationsBag($expert->id);

        if (property_exists($expert, 'telegram_client_id')) {
            $telegramClient = new TelegramClient(
                id: $expert->telegram_client_id,
                telegramId: $expert->telegram_id,
                telegramFirstName: $expert->telegram_first_name,
                telegramLastName: $expert->telegram_last_name,
                telegramUsername: $expert->telegram_username,
                createdAt: new \DateTime($expert->telegram_client_created_at)
            );
        } else {
            $telegramClient = null;
        }

        return new Expert(
            id: $expert->id,
            firstName: $expert->first_name,
            lastName: $expert->last_name,
            patronymic: $expert->patronymic,
            biography: $expert->biography,
            telegramPhoneNumber: $expert->telegram_phone_number,
            balance: $expert->balance,
            isBlocked: $expert->is_blocked,
            createdAt: new \DateTime($expert->created_at),
            isVerification: $expert->is_verification,
            telegramClient: $telegramClient,
            priceWorkHour: $expert->price_work_hour,
            avatar: $expert->avatar,
            video: $expert->video,
            whatsappPhoneNumber: $expert->whatsapp_phone_number,
            requisites: $expert->requisites,
            placesBag: $expertPlacesBag,
            specializationsBag: $expertSpecializationsBag,
            paymentsBag: $paymentsBag
        );
    }

}
