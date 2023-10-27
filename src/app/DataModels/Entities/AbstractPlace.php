<?php

namespace App\DataModels\Entities;

abstract class AbstractPlace implements EntityInterface
{
    /**
     * @return int
     */
    abstract public function getId(): int;

    /**
     * @return string
     */
    abstract public function getName(): string;

    public function asArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
        ];
    }

}
