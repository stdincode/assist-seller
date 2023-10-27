<?php

namespace App\Repositories;

use App\DataModels\Entities\AbstractConsultation;
use App\DataModels\Entities\AbstractConsultationRequest;
use App\DataModels\Entities\AbstractExpert;
use App\DataModels\Entities\AbstractExpertConsultationRequest;
use App\DataModels\Entities\AbstractPlace;
use App\DataModels\Entities\AbstractSpecialization;
use App\DataModels\Entities\AbstractStudent;
use App\DataModels\Entities\AbstractTelegramMenuSession;
use App\DataModels\Entities\AbstractWorkingDay;
use App\DataModels\Entities\AbstractWorkingHour;
use App\DataModels\Entities\Bags\ConsultationRequestsBagInterface;
use App\DataModels\Entities\Bags\ConsultationsBagInterface;
use App\DataModels\Entities\Bags\ExpertConsultationRequestsBagInterface;
use App\DataModels\Entities\Bags\ExpertsBagInterface;
use App\DataModels\Entities\Bags\WorkingDaysBag;
use App\DataModels\Entities\Bags\WorkingDaysBagInterface;
use App\DataModels\Entities\Bags\WorkingHoursBag;
use App\DataModels\Entities\Bags\WorkingHoursBagInterface;
use App\DataModels\Entities\Consultation;
use App\DataModels\Entities\ConsultationRequest;
use App\DataModels\Entities\ConsultationRequestStatus;
use App\DataModels\Entities\ConsultationStatus;
use App\DataModels\Entities\Expert;
use App\DataModels\Entities\ExpertConsultationRequest;
use App\DataModels\Entities\ExpertConsultationRequestStatus;
use App\DataModels\Entities\Place;
use App\DataModels\Entities\Specialization;
use App\DataModels\Entities\Student;
use App\DataModels\Entities\TelegramClient;
use App\DataModels\Entities\WorkingDay;
use App\DataModels\Entities\WorkingHour;
use DateInterval;
use DatePeriod;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use IntlDateFormatter;

class ConsultationStorageRepository implements ConsultationStorageRepositoryInterface
{
    private string $consultationRequestsTableName;
    private string $consultationRequestStatusesTableName;
    private string $studentsTableName;
    private string $expertsTableName;
    private string $placesTableName;
    private string $specializationsTableName;
    private string $consultationsTableName;
    private string $expertConsultationRequestsTableName;
    private string $expertConsultationRequestStatusesTableName;
    private string $consultationStatusesTableName;
    private string $telegramClientsTableName;

    public function __construct(
        string $consultationsTableName,
        string $consultationStatusesTableName,
        string $consultationRequestsTableName,
        string $expertConsultationRequestsTableName,
        string $consultationRequestStatusesTableName,
        string $expertConsultationRequestStatusesTableName,
        string $studentsTableName,
        string $expertsTableName,
        string $placesTableName,
        string $specializationsTableName,
        string $telegramClientsTableName
    )
    {
        $this->consultationsTableName = $consultationsTableName;
        $this->consultationStatusesTableName = $consultationStatusesTableName;
        $this->consultationRequestsTableName = $consultationRequestsTableName;
        $this->consultationRequestStatusesTableName = $consultationRequestStatusesTableName;
        $this->expertConsultationRequestsTableName = $expertConsultationRequestsTableName;
        $this->expertConsultationRequestStatusesTableName = $expertConsultationRequestStatusesTableName;
        $this->studentsTableName = $studentsTableName;
        $this->expertsTableName = $expertsTableName;
        $this->placesTableName = $placesTableName;
        $this->specializationsTableName = $specializationsTableName;
        $this->telegramClientsTableName = $telegramClientsTableName;
    }

    public const CONSULTATION_REQUEST_STATUSES = [
        [
            'id' => 1,
            'name' => 'Ожидает выбора эксперта',
        ],
        [
            'id' => 2,
            'name' => 'Эксперт выбран',
        ],
        [
            'id' => 3,
            'name' => 'Отмена',
        ],
    ];

    public const CONSULTATION_STATUSES = [
        [
            'id' => 1,
            'name' => 'Ожидание оплаты',
        ],
        [
            'id' => 2,
            'name' => 'Оплачено',
        ],
        [
            'id' => 3,
            'name' => 'Проведена',
        ],
        [
            'id' => 4,
            'name' => 'Отменена учеником',
        ],
        [
            'id' => 5,
            'name' => 'Отменена экспертом',
        ],
    ];

    public const EXPERT_CONSULTATION_REQUEST_STATUSES = [
        [
            'id' => 1,
            'name' => 'Ожидает',
        ],
        [
            'id' => 2,
            'name' => 'Принято',
        ],
        [
            'id' => 3,
            'name' => 'Отклонено',
        ],
    ];

    public function getUpcomingConsultations(ConsultationsBagInterface $consultationsBag): ConsultationsBagInterface
    {
        $paidStatusId = self::CONSULTATION_STATUSES[1]['id'];
        $currentDateTime = new \DateTime();
        $consultations = $this->buildGetConsultationsQuery()
            ->where(['c.status_id' => $paidStatusId])
            ->where('cr.consultation_datetime', '>=', $currentDateTime)
            ->get();

        foreach ($consultations->all() as $consultation) {
            $consultationsBag->add($this->buildConsultationDataModel(consultation: $consultation));
        }

        return $consultationsBag;
    }

    public function updateConsultation(
        int $consultationId,
        ?string $expertLink = null,
        ?string $studentLink = null,
        ?int $statusId = null,
        ?float $studentConsultationRating = null,
        ?float $studentCallQualityRating = null,
        ?string $studentComment = null,
        ?\DateTime $studentCommentDatetime = null,
        ?float $expertCallQualityRating = null,
        ?string $expertComment = null,
        ?\DateTime $expertCommentDatetime = null
    ): bool
    {
        $values = ['updated_at' => new \DateTime()];

        if ($expertLink) $values['expert_link'] = $expertLink;
        if ($studentLink) $values['student_link'] = $studentLink;
        if ($studentConsultationRating) $values['student_consultation_rating'] = $studentConsultationRating;
        if ($studentCallQualityRating) $values['student_call_quality_rating'] = $studentCallQualityRating;
        if ($studentComment) $values['student_comment'] = $studentComment;
        if ($studentCommentDatetime) $values['student_comment_datetime'] = $studentCommentDatetime;
        if ($expertCallQualityRating) $values['expert_call_quality_rating'] = $expertCallQualityRating;
        if ($expertComment) $values['expert_comment'] = $expertComment;
        if ($expertCommentDatetime) $values['expert_comment_datetime'] = $expertCommentDatetime;

        if ($statusId) {
            $values['status_id'] = $statusId;
            $values['last_change_status_datetime'] = new \DateTime();
        }

        if (empty($values)) return false;

        return DB::table($this->consultationsTableName)
            ->where(['id' => $consultationId,])
            ->update($values);
    }

    public function getConsultationRequestsByStudent(
        ConsultationRequestsBagInterface $consultationRequestsBag,
        AbstractStudent $student
    ): ConsultationRequestsBagInterface
    {
        $consultationRequests = $this->buildGetConsultationRequestsQuery()
            ->where(['cr.student_id' => $student->getId()])
            ->where(['cr.status_id' => self::CONSULTATION_REQUEST_STATUSES[0]['id']])
            ->get();

        foreach ($consultationRequests->all() as $consultationRequest) {
            $consultationRequestsBag->add($this->buildConsultationRequestDataModel(
                consultationRequest: $consultationRequest,
                student: $student
            ));
        }

        $consultationRequestsBag->setTotal($consultationRequests->count());

        return $consultationRequestsBag;
    }

    public function getConsultationRequestById(int $id): ?AbstractConsultationRequest
    {
        $consultationRequest = $this->buildGetConsultationRequestsQuery()
            ->where(['cr.id' => $id])
            ->first();

        if (!$consultationRequest) return null;

        return $this->buildConsultationRequestDataModel($consultationRequest);
    }

    public function createConsultationRequest(
        string $text,
        \DateTime $consultationDateTime,
        AbstractTelegramMenuSession $telegramMenuSession,
        AbstractPlace $place,
        AbstractSpecialization $specialization,
        AbstractStudent $student
    ): ?AbstractConsultationRequest
    {
        $currentDateTime = new \DateTime();
        $defaultStatus = new ConsultationRequestStatus(
            id: self::CONSULTATION_REQUEST_STATUSES[0]['id'],
            name: self::CONSULTATION_REQUEST_STATUSES[0]['name']
        );
        $id = DB::table($this->consultationRequestsTableName)->insertGetId([
            'text' => $text,
            'consultation_datetime' => $consultationDateTime,
            'telegram_menu_session_id' => $telegramMenuSession->getId(),
            'place_id' => $place->getId(),
            'specialization_id' => $specialization->getId(),
            'student_id' => $student->getId(),
            'status_id' => $defaultStatus->getId(),
            'last_change_status_datetime' => $currentDateTime,
            'created_at' => $currentDateTime,
        ]);

        if (!$id) return null;

        return new ConsultationRequest(
            id: $id,
            student: $student,
            text: $text,
            consultationDateTime: $consultationDateTime,
            place: $place,
            specialization: $specialization,
            status: $defaultStatus,
            lastChangeStatusDatetime: $currentDateTime,
            createdAt: $currentDateTime,
            telegramMenuSession: $telegramMenuSession
        );
    }

    public function updateConsultationRequest(int $consultationRequestId, int $statusId): bool
    {
        $status = DB::table($this->consultationRequestStatusesTableName)
            ->where(['id' => $statusId])
            ->first();

        if (!$status) return false;

        return DB::table($this->consultationRequestsTableName)
            ->where(['id' => $consultationRequestId])
            ->update([
                'status_id' => $statusId,
                'last_change_status_datetime' => new \DateTime(),
            ]);
    }

    public function updateExpertConsultationRequests(array $ids, int $statusId): bool
    {
        $status = DB::table($this->expertConsultationRequestStatusesTableName)
            ->where(['id' => $statusId])
            ->first();

        if (!$status) return false;

        return DB::table($this->expertConsultationRequestsTableName)
            ->whereIn('id', $ids)
            ->update(['status_id' => $statusId]);
    }

    public function createConsultation(
        AbstractExpertConsultationRequest $expertConsultationRequest,
        AbstractConsultationRequest $consultationRequest,
        AbstractTelegramMenuSession $telegramMenuSession
    ): ?AbstractConsultation
    {
        $defaultStatus = new ConsultationStatus(
            id: self::CONSULTATION_STATUSES[0]['id'],
            name: self::CONSULTATION_STATUSES[0]['name']
        );
        $currentDateTime = new \DateTime();

        $id = DB::table($this->consultationsTableName)
            ->insertGetId([
                'expert_consultation_request_id' => $expertConsultationRequest->getId(),
                'consultation_request_id' => $consultationRequest->getId(),
                'telegram_menu_session_id' => $telegramMenuSession->getId(),
                'expert_link' => null,
                'student_link' => null,
                'status_id' => $defaultStatus->getId(),
                'last_change_status_datetime' => $currentDateTime,
                'cost' => $expertConsultationRequest->getExpert()->getPriceWorkHour(),
                'student_consultation_rating' => null,
                'student_call_quality_rating' => null,
                'student_comment' => null,
                'student_comment_datetime' => null,
                'expert_call_quality_rating' => null,
                'expert_comment' => null,
                'expert_comment_datetime' => null,
                'created_at' => $currentDateTime,
            ]);

        if (!$id) return null;

        return new Consultation(
            id: $id,
            status: $defaultStatus,
            cost: $expertConsultationRequest->getExpert()->getPriceWorkHour(),
            lastChangeStatusDatetime: $currentDateTime,
            createdAt: $currentDateTime,
            consultationRequest: $consultationRequest,
            expertConsultationRequest: $expertConsultationRequest,
            telegramMenuSession: $telegramMenuSession
        );
    }

    public function getConsultationById(int $id): ?AbstractConsultation
    {
        $consultation = $this->buildGetConsultationsQuery()
            ->where(['c.id' => $id])
            ->first();

        if (!$consultation) return null;

        return $this->buildConsultationDataModel($consultation);
    }

    public function getConsultationsByStudentId(
        int $studentId,
        ConsultationsBagInterface $consultationsBag
    ): ConsultationsBagInterface
    {
        $consultations = $this->buildGetConsultationsQuery()
            ->where(['st.id' => $studentId])
            ->whereIn('c.status_id', [self::CONSULTATION_STATUSES[0]['id'], self::CONSULTATION_STATUSES[1]['id'], self::CONSULTATION_STATUSES[2]['id']])
            ->get();

        foreach ($consultations as $consultation) {
            $consultationsBag->add($this->buildConsultationDataModel($consultation));
        }
        $consultationsBag->setTotal($consultations->count());

        return $consultationsBag;
    }

    public function changeStatusOfConsultation(int $consultationId, int $statusId): bool
    {
        $status = DB::table($this->consultationStatusesTableName)
            ->where(['id' => $statusId])
            ->first();

        if (!$status) return false;

        return DB::table($this->consultationsTableName)
            ->where(['id' => $consultationId])
            ->update(['status_id' => $statusId]);
    }

    public function getExpertConsultationRequestsByConsultationRequestId(
        int $consultationRequestId,
        ExpertConsultationRequestsBagInterface $expertConsultationRequestsBag
    ): ExpertConsultationRequestsBagInterface
    {
        $expertConsultationRequests = $this->buildGetExpertConsultationRequestsQuery()
            ->where(['ecr.consultation_request_id' => $consultationRequestId])
            ->where(['ecr.status_id' => self::EXPERT_CONSULTATION_REQUEST_STATUSES[0]['id']])
            ->get();

        foreach ($expertConsultationRequests->all() as $expertConsultationRequest) {
            $expertConsultationRequestsBag->add($this->buildExpertConsultationRequestDataModel(
                expertConsultationRequest: $expertConsultationRequest
            ));
        }

        return $expertConsultationRequestsBag;
    }

    public function getExpertConsultationRequestById(int $id): ?AbstractExpertConsultationRequest
    {
        $expertConsultationRequest = $this->buildGetExpertConsultationRequestsQuery()
            ->where(['ecr.id' => $id])
            ->first();

        if (!$expertConsultationRequest) return null;

        return $this->buildExpertConsultationRequestDataModel(expertConsultationRequest: $expertConsultationRequest);
    }

    public function createExpertConsultationRequest(
        AbstractExpert $expert,
        AbstractConsultationRequest $consultationRequest
    ): ?AbstractExpertConsultationRequest
    {
        $currentDateTime = new \DateTime();
        $defaultStatus = new ExpertConsultationRequestStatus(
            id: self::EXPERT_CONSULTATION_REQUEST_STATUSES[0]['id'],
            name: self::EXPERT_CONSULTATION_REQUEST_STATUSES[0]['name']
        );
        $expertConsultationRequestId = DB::table($this->expertConsultationRequestsTableName)
            ->insertGetId([
                'expert_id' => $expert->getId(),
                'status_id' => $defaultStatus->getId(),
                'consultation_request_id' => $consultationRequest->getId(),
                'created_at' => $currentDateTime,
            ]);

        return new ExpertConsultationRequest(
            id: $expertConsultationRequestId,
            expert: $expert,
            status: $defaultStatus,
            createdAt: $currentDateTime,
            consultationRequest: $consultationRequest
        );
    }

    public function getConsultationsByExpertId(
        int $expertId,
        ConsultationsBagInterface $consultationsBag
    ): ConsultationsBagInterface
    {
        $consultations = $this->buildGetConsultationsQuery()
            ->where(['e.id' => $expertId])
            ->whereIn('c.status_id', [self::CONSULTATION_STATUSES[0]['id'], self::CONSULTATION_STATUSES[1]['id'], self::CONSULTATION_STATUSES[2]['id']])
            ->get();

        foreach ($consultations as $consultation) {
            $consultationsBag->add($this->buildConsultationDataModel($consultation));
        }
        $consultationsBag->setTotal($consultations->count());

        return $consultationsBag;
    }

    public function getWorkingDays(\DateTime $beginDate = new \DateTime()): WorkingDaysBagInterface
    {
        $endDate = (new \DateTime($beginDate->format('Y-m-d')))->modify('+14 days');
        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($beginDate, $interval, $endDate);
        $intlFormatter = new IntlDateFormatter('ru_RU', IntlDateFormatter::SHORT, IntlDateFormatter::SHORT);
        $intlFormatter->setPattern("E d MMM");
        $daysBag = new WorkingDaysBag();
        foreach ($period as $dt) {
            $daysBag->add(
                new WorkingDay(
                    id: (int)$dt->format('d'),
                    name: $intlFormatter->format($dt),
                    dateTime: $dt
                ),
            );
        }

        return $daysBag;
    }

    public function getWorkingDayById(int $id): ?AbstractWorkingDay
    {
        $workingDays = $this->getWorkingDays();

        foreach ($workingDays->getAll() as $workingDay) {
            if ($id === $workingDay->getId()) return $workingDay;
        }

        return null;
    }

    public function getWorkingHours(
        \DateTime $selectedDate,
        \DateTime $beginTime = new \DateTime()
    ): WorkingHoursBagInterface
    {
        $beginTime->setTimezone(new \DateTimeZone('Europe/Moscow'));
        $defaultBeginTime = clone $beginTime;
        $defaultBeginTime->setTime('08', '00');
        $beginTime->setTime($beginTime->format('H'), 0);
        $beginTime->modify('+2 hours');
        $endTime = new \DateTime('tomorrow', new \DateTimeZone('Europe/Moscow'));
        $interval = DateInterval::createFromDateString('1 hour');
        $currentDate = new \DateTime();

        if (
            $selectedDate->format('Y-m-d') === $currentDate->format('Y-m-d') &&
            $beginTime->format('Y-m-d') === $currentDate->format('Y-m-d') &&
            $beginTime > $defaultBeginTime
        ) {
            $period = new DatePeriod($beginTime, $interval, $endTime);
        } else {
            $period = new DatePeriod($defaultBeginTime, $interval, $endTime);
        }

        $intlFormatter = new IntlDateFormatter('ru_RU', IntlDateFormatter::SHORT, IntlDateFormatter::SHORT);
        $intlFormatter->setPattern("HH:00");
        $intlFormatter->setTimeZone(new \DateTimeZone('Europe/Moscow'));
        $hoursBag = new WorkingHoursBag();
        foreach ($period as $dt) {
            $hoursBag->add(
                new WorkingHour(
                    id: (int)$dt->format('H'),
                    name: "с {$intlFormatter->format($dt)}",
                    dateTime: $dt
                )
            );
        }

        return $hoursBag;
    }

    public function getWorkingHourById(
        int $id,
        \DateTime $selectedDate,
        \DateTime $beginTime = new \DateTime()
    ): ?AbstractWorkingHour
    {
        $workingHours = $this->getWorkingHours($selectedDate, $beginTime);

        foreach ($workingHours->getAll() as $workingHour) {
            if ($id === $workingHour->getId()) return $workingHour;
        }

        return null;
    }

    private function buildGetConsultationRequestsQuery(): Builder
    {
        return DB::table("$this->consultationRequestsTableName as cr")
            ->leftJoin("$this->studentsTableName as st", 'st.id', '=', 'cr.student_id')
            ->leftJoin("$this->telegramClientsTableName as tc", 'tc.id', '=', 'st.telegram_client_id')
            ->leftJoin("$this->consultationRequestStatusesTableName as crs", 'crs.id', '=', 'cr.status_id')
            ->leftJoin("$this->placesTableName as p", 'p.id', '=', 'cr.place_id')
            ->leftJoin("$this->specializationsTableName as sp", 'sp.id', '=', 'cr.specialization_id')
            ->select([
                'cr.id as id',
                'cr.text as text',
                'cr.consultation_datetime as consultation_datetime',
                'st.id as student_id',
                'st.first_name as student_first_name',
                'st.contact_phone_number as student_contact_phone_number',
                'st.is_blocked as student_is_blocked',
                'st.telegram_client_id as student_telegram_client_id',
                'st.created_at as student_created_at',
                'st.updated_at as student_updated_at',
                'tc.id as student_telegram_client_id',
                'tc.telegram_id as student_telegram_id',
                'tc.telegram_username as student_telegram_client_username',
                'tc.telegram_first_name as student_telegram_client_first_name',
                'tc.telegram_last_name as student_telegram_client_last_name',
                'tc.created_at as student_telegram_client_created_at',
                'p.id as place_id',
                'p.name as place_name',
                'sp.id as specialization_id',
                'sp.name as specialization_name',
                'crs.id as status_id',
                'crs.name as status_name',
                'cr.last_change_status_datetime as last_change_status_datetime',
                'cr.created_at as created_at',
            ]);
    }

    private function buildConsultationRequestDataModel(
        $consultationRequest,
        ?AbstractStudent $student = null,
        ?AbstractTelegramMenuSession $telegramMenuSession = null
    ): ConsultationRequest
    {
        return new ConsultationRequest(
            id: $consultationRequest->id,
            student: $student ??
                new Student(
                    id: $consultationRequest->student_id,
                    firstName: $consultationRequest->student_first_name,
                    isBlocked: $consultationRequest->student_is_blocked,
                    createdAt: new \DateTime($consultationRequest->student_created_at),
                    telegramClient: new TelegramClient(
                        id: $consultationRequest->student_telegram_client_id,
                        telegramId: $consultationRequest->student_telegram_id,
                        telegramFirstName: $consultationRequest->student_telegram_client_first_name,
                        telegramLastName: $consultationRequest->student_telegram_client_last_name,
                        telegramUsername: $consultationRequest->student_telegram_client_username,
                        createdAt: new \DateTime($consultationRequest->student_telegram_client_created_at)
                    ),
                    contactPhoneNumber: $consultationRequest->student_contact_phone_number,
                    updatedAt: $consultationRequest->student_updated_at ? new \DateTime($consultationRequest->student_updated_at) : null
                ),
            text: $consultationRequest->text,
            consultationDateTime: new \DateTime($consultationRequest->consultation_datetime),
            place: new Place(
                id: $consultationRequest->place_id,
                name: $consultationRequest->place_name
            ),
            specialization: new Specialization(
                id: $consultationRequest->specialization_id,
                name: $consultationRequest->specialization_name
            ),
            status: new ConsultationRequestStatus(
                id: $consultationRequest->status_id,
                name: $consultationRequest->status_name
            ),
            lastChangeStatusDatetime: new \DateTime($consultationRequest->last_change_status_datetime),
            createdAt: new \DateTime($consultationRequest->created_at),
            telegramMenuSession: $telegramMenuSession ?? null
        );
    }

    private function buildGetExpertConsultationRequestsQuery(): Builder
    {
        return DB::table("$this->expertConsultationRequestsTableName as ecr")
            ->leftJoin("$this->expertConsultationRequestStatusesTableName as ecrs", 'ecrs.id', '=', 'ecr.status_id')
            ->leftJoin("$this->expertsTableName as e", 'e.id', '=', 'ecr.expert_id')
            ->select([
                'ecr.id as id',
                'ecrs.id as status_id',
                'ecrs.name as status_name',
                'ecr.created_at as created_at',
                'ecr.updated_at as updated_at',
                'e.id as expert_id',
                'e.first_name as expert_first_name',
                'e.last_name as expert_last_name',
                'e.patronymic as expert_patronymic',
                'e.biography as expert_biography',
                'e.avatar as expert_avatar',
                'e.video as expert_video',
                'e.telegram_phone_number as expert_telegram_phone_number',
                'e.whatsapp_phone_number as expert_whatsapp_phone_number',
                'e.price_work_hour as expert_price_work_hour',
                'e.requisites as expert_requisites',
                'e.balance as expert_balance',
                'e.is_verification as expert_is_verification',
                'e.is_blocked as expert_is_blocked',
                'e.created_at as expert_created_at',
                'e.updated_at as expert_updated_at',
            ]);
    }

    private function buildExpertConsultationRequestDataModel(
        $expertConsultationRequest,
        AbstractConsultationRequest $consultationRequest = null
    ): AbstractExpertConsultationRequest
    {
        return new ExpertConsultationRequest(
            id: $expertConsultationRequest->id,
            expert: new Expert(
                id: $expertConsultationRequest->expert_id,
                firstName: $expertConsultationRequest->expert_first_name,
                lastName: $expertConsultationRequest->expert_last_name,
                patronymic: $expertConsultationRequest->expert_patronymic,
                biography: $expertConsultationRequest->expert_biography,
                telegramPhoneNumber: $expertConsultationRequest->expert_telegram_phone_number,
                balance: $expertConsultationRequest->expert_balance,
                isBlocked: $expertConsultationRequest->expert_is_blocked,
                createdAt: new \DateTime($expertConsultationRequest->expert_created_at),
                isVerification: $expertConsultationRequest->expert_is_verification,
                priceWorkHour: $expertConsultationRequest->expert_price_work_hour,
                avatar: $expertConsultationRequest->expert_avatar,
                video: $expertConsultationRequest->expert_video,
                whatsappPhoneNumber: $expertConsultationRequest->expert_whatsapp_phone_number,
                requisites: $expertConsultationRequest->expert_requisites,
                updatedAt: $expertConsultationRequest->expert_updated_at ? new \DateTime($expertConsultationRequest->expert_updated_at) : null
            ),
            status: new ExpertConsultationRequestStatus(
                id: $expertConsultationRequest->status_id,
                name: $expertConsultationRequest->status_name
            ),
            createdAt: new \DateTime($expertConsultationRequest->created_at),
            updatedAt: $expertConsultationRequest->updated_at ? new \DateTime($expertConsultationRequest->updated_at) : null,
            consultationRequest: $consultationRequest
        );
    }

    private function buildGetConsultationsQuery(): Builder
    {
        return DB::table("$this->consultationsTableName as c")
            ->leftJoin("$this->consultationStatusesTableName as cs", 'cs.id', '=', 'c.status_id')
            ->leftJoin("$this->consultationRequestsTableName as cr", 'cr.id', '=', 'c.consultation_request_id')
            ->leftJoin("$this->placesTableName as p", 'p.id', '=', 'cr.place_id')
            ->leftJoin("$this->specializationsTableName as sp", 'sp.id', '=', 'cr.specialization_id')
            ->leftJoin("$this->consultationRequestStatusesTableName as crs", 'crs.id', '=', 'cr.status_id')
            ->leftJoin("$this->studentsTableName as st", 'st.id', '=', 'cr.student_id')
            ->leftJoin("$this->expertConsultationRequestsTableName as ecr", 'ecr.id', '=', 'c.expert_consultation_request_id')
            ->leftJoin("$this->expertConsultationRequestStatusesTableName as ecrs", 'ecrs.id', '=', 'ecr.status_id')
            ->leftJoin("$this->expertsTableName as e", 'e.id', '=', 'ecr.expert_id')
            ->select([
                'c.id as id',
                'cs.id as status_id',
                'cs.name as status_name',
                'c.last_change_status_datetime as last_change_status_datetime',
                'c.cost as cost',
                'c.created_at as created_at',
                'c.updated_at as updated_at',
                'c.expert_link as expert_link',
                'c.student_link as student_link',
                'cr.id as consultation_request_id',
                'cr.text as consultation_request_text',
                'cr.consultation_datetime as consultation_datetime',
                'st.id as student_id',
                'st.first_name as student_first_name',
                'st.contact_phone_number as student_contact_phone_number',
                'st.is_blocked as student_is_blocked',
                'st.telegram_client_id as student_telegram_client_id',
                'st.created_at as student_created_at',
                'st.updated_at as student_updated_at',
                'p.id as place_id',
                'p.name as place_name',
                'sp.id as specialization_id',
                'sp.name as specialization_name',
                'crs.id as consultation_request_status_id',
                'crs.name as consultation_request_status_name',
                'cr.last_change_status_datetime as consultation_request_last_change_status',
                'cr.created_at as consultation_request_created_at',
                'ecr.id as expert_consultation_request_id',
                'ecrs.id as expert_consultation_request_status_id',
                'ecrs.name as expert_consultation_request_status_name',
                'ecr.created_at as expert_consultation_request_created_at',
                'ecr.updated_at as expert_consultation_request_updated_at',
                'e.id as expert_id',
                'e.first_name as expert_first_name',
                'e.last_name as expert_last_name',
                'e.patronymic as expert_patronymic',
                'e.biography as expert_biography',
                'e.avatar as expert_avatar',
                'e.video as expert_video',
                'e.telegram_phone_number as expert_telegram_phone_number',
                'e.whatsapp_phone_number as expert_whatsapp_phone_number',
                'e.price_work_hour as expert_price_work_hour',
                'e.requisites as expert_requisites',
                'e.balance as expert_balance',
                'e.is_verification as expert_is_verification',
                'e.is_blocked as expert_is_blocked',
                'e.created_at as expert_created_at',
                'e.updated_at as expert_updated_at',
            ]);
    }

    private function buildConsultationDataModel($consultation): AbstractConsultation
    {
        return new Consultation(
            id: $consultation->id,
            status: new ConsultationStatus(
                id: $consultation->status_id,
                name: $consultation->status_name
            ),
            cost: $consultation->cost,
            lastChangeStatusDatetime: new \DateTime($consultation->last_change_status_datetime),
            createdAt: new \DateTime($consultation->created_at),
            updatedAt: $consultation->updated_at ? new \DateTime($consultation->updated_at) : null,
            expertLink: $consultation->expert_link,
            studentLink: $consultation->student_link,
            consultationRequest: new ConsultationRequest(
                id: $consultation->consultation_request_id,
                student: new Student(
                    id: $consultation->student_id,
                    firstName: $consultation->student_first_name,
                    isBlocked: $consultation->student_is_blocked,
                    createdAt: new \DateTime($consultation->student_created_at),
                    contactPhoneNumber: $consultation->student_contact_phone_number
                ),
                text: $consultation->consultation_request_text,
                consultationDateTime: new \DateTime($consultation->consultation_datetime),
                place: new Place(
                    id: $consultation->place_id,
                    name: $consultation->place_name
                ),
                specialization: new Specialization(
                    id: $consultation->specialization_id,
                    name: $consultation->specialization_name
                ),
                status: new ConsultationRequestStatus(
                    id: $consultation->consultation_request_status_id,
                    name: $consultation->consultation_request_status_name
                ),
                lastChangeStatusDatetime: new \DateTime($consultation->consultation_request_last_change_status),
                createdAt: new \DateTime($consultation->consultation_request_created_at)
            ),
            expertConsultationRequest: new ExpertConsultationRequest(
                id: $consultation->expert_consultation_request_id,
                expert: new Expert(
                    id: $consultation->expert_id,
                    firstName: $consultation->expert_first_name,
                    lastName: $consultation->expert_last_name,
                    patronymic: $consultation->expert_patronymic,
                    biography: $consultation->expert_biography,
                    telegramPhoneNumber: $consultation->expert_telegram_phone_number,
                    balance: $consultation->expert_balance,
                    isBlocked: $consultation->expert_is_blocked,
                    createdAt: new \DateTime($consultation->expert_created_at),
                    isVerification: $consultation->expert_is_verification,
                    priceWorkHour: $consultation->expert_price_work_hour,
                    avatar: $consultation->expert_avatar,
                    video: $consultation->expert_video,
                    whatsappPhoneNumber: $consultation->expert_whatsapp_phone_number,
                    requisites: $consultation->expert_requisites,
                    updatedAt: $consultation->expert_updated_at ? new \DateTime($consultation->expert_updated_at) : null
                ),
                status: new ExpertConsultationRequestStatus(
                    id: $consultation->expert_consultation_request_status_id,
                    name: $consultation->expert_consultation_request_status_name
                ),
                createdAt: new \DateTime($consultation->expert_consultation_request_created_at)
            ),
        );
    }

}
