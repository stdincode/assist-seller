<?php

namespace App\DataModels\Entities;

abstract class AbstractWorkingDay implements EntityInterface
{
    /**
     * @return int
     */
    abstract public function getId(): int;

    /**
     * @return string
     */
    abstract public function getName(): string;

    /**
     * @return \DateTime
     */
    abstract public function getDateTime(): \DateTime;

    public function asArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'datetime' => $this->getDateTime(),
        ];
    }

}
