<?php

namespace App\DataModels\Entities;

class WorkingDay extends AbstractWorkingDay
{
    private int $id;
    private string $name;
    private \DateTime $dateTime;

    public function __construct(int $id, string $name, \DateTime $dateTime)
    {
        $this->id = $id;
        $this->name = $name;
        $this->dateTime = $dateTime;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDateTime(): \DateTime
    {
        return $this->dateTime;
    }
}
