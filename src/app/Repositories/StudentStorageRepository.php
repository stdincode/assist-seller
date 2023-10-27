<?php

namespace App\Repositories;

use App\DataModels\Entities\Bags\StudentsBagInterface;
use App\DataModels\Entities\Student;
use App\DataModels\Entities\AbstractStudent;
use App\DataModels\Entities\TelegramClient;
use App\DataModels\Entities\AbstractTelegramClient;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class StudentStorageRepository implements StudentStorageRepositoryInterface
{
    private string $studentsTableName;
    private string $consultationsTableName;
    private string $telegramClientsTableName;

    public function __construct(
        string $studentsTableName,
        string $consultationsTableName,
        string $telegramClientsTableName
    )
    {
        $this->studentsTableName = $studentsTableName;
        $this->consultationsTableName = $consultationsTableName;
        $this->telegramClientsTableName = $telegramClientsTableName;
    }

    public function getStudents(StudentsBagInterface $studentsBag): StudentsBagInterface
    {
        $students = $this->buildQueryGetStudents()->get();

        foreach ($students->all() as $student) {
            $studentsBag->add($this->createStudentDataModel($student));
        }

        $studentsBag->setTotal($students->count());

        return $studentsBag;
    }

    public function getStudentById(int $id): ?AbstractStudent
    {
        $student = $this->buildQueryGetStudents()
            ->where(['s.id' => $id])
            ->first();

        if (!$student) return null;

        return $this->createStudentDataModel($student);
    }

    public function getStudentByContactPhoneNumber(int $contactPhoneNumber): ?AbstractStudent
    {
        $student = $this->buildQueryGetStudents()
            ->where(['s.contact_phone_number' => $contactPhoneNumber])
            ->first();

        if (!$student) return null;

        return $this->createStudentDataModel($student);
    }

    public function getStudentByTelegramClientId(int $telegramClientId): ?AbstractStudent
    {
        $student = $this->buildQueryGetStudents()
            ->where(['s.telegram_client_id' => $telegramClientId])
            ->first();

        if (!$student) return null;

        return $this->createStudentDataModel($student);
    }

    public function createStudent(
        string                 $firstName,
        AbstractTelegramClient $telegramClient,
        ?int                   $contactPhoneNumber
    ): ?AbstractStudent
    {
        $isBlocked = false;
        $currentDateTime = new \DateTime();

        $id = DB::table($this->studentsTableName)->insertGetId([
            'first_name' => $firstName,
            'telegram_client_id' => $telegramClient->getId(),
            'contact_phone_number' => $contactPhoneNumber,
            'is_blocked' => $isBlocked,
            'created_at' => $currentDateTime,
        ]);

        if (!$id) return null;

        return new Student(
            id: $id,
            firstName: $firstName,
            isBlocked: $isBlocked,
            createdAt: $currentDateTime,
            telegramClient: $telegramClient,
            contactPhoneNumber: $contactPhoneNumber
        );
    }

    public function updateStudent(
        int     $id,
        ?string $firstName,
        ?int    $contactPhoneNumber,
        ?bool   $isBlocked
    ): bool
    {
        $values['updated_at'] = new \DateTime();

        if ($firstName !== null) $values['first_name'] = $firstName;
        if ($isBlocked !== null) $values['is_blocked'] = $isBlocked;
        if ($contactPhoneNumber !== null) $values['contact_phone_number'] = $contactPhoneNumber;

        return DB::table($this->studentsTableName)->where(['id' => $id])->update($values);
    }

    public function deleteStudent(int $id): bool
    {
        return DB::table($this->studentsTableName)->where(['id' => $id])->delete();
    }

    private function buildQueryGetStudents(): Builder
    {
        return DB::table("{$this->studentsTableName} as s")
            ->leftJoin("{$this->telegramClientsTableName} as tc", 'tc.id', '=', 's.telegram_client_id')
            ->select([
                's.id as id',
                's.first_name as first_name',
                'tc.id as telegram_client_id',
                'tc.telegram_id as telegram_id',
                'tc.telegram_first_name as telegram_first_name',
                'tc.telegram_last_name as telegram_last_name',
                'tc.telegram_username as telegram_username',
                'tc.created_at as telegram_client_created_at',
                's.contact_phone_number as contact_phone_number',
                's.is_blocked as is_blocked',
                's.created_at as created_at',
            ]);
    }

    private function buildQueryGetStudentConsultationsWithExpenses(): Builder
    {
        $consultationPaidStatusKey = array_search('Оплачено', array_column(ConsultationStorageRepository::CONSULTATION_STATUSES, 'name'));

        return DB::table("{$this->studentsTableName} as s")
            ->leftJoin("{$this->consultationsTableName} as c", 'c.student_id', '=', 's.id')
            ->select([
                's.id as id',
                's.first_name as first_name',
                's.contact_phone_number as contact_phone_number',
                's.is_blocked as is_blocked',
                's.created_at as created_at',
                DB::raw('SUM(c.cost) as expenses'),
            ])
            ->where(['c.status_id' => ConsultationStorageRepository::CONSULTATION_STATUSES[$consultationPaidStatusKey]['id']])
            ->groupBy('s.id');
    }

    private function createStudentDataModel($student): Student
    {
        $telegramClient = new TelegramClient(
            id: $student->telegram_client_id,
            telegramId: $student->telegram_id,
            telegramFirstName: $student->telegram_first_name,
            telegramLastName: $student->telegram_last_name,
            telegramUsername: $student->telegram_username,
            createdAt: new \DateTime($student->telegram_client_created_at)
        );

        return new Student(
            id: $student->id,
            firstName: $student->first_name,
            isBlocked: $student->is_blocked,
            createdAt: new \DateTime($student->created_at),
            telegramClient: $telegramClient,
            contactPhoneNumber: $student->contact_phone_number
        );
    }

}
