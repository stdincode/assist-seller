<?php

namespace App\DataModels\Entities;

use Ramsey\Uuid\UuidInterface;

abstract class AbstractAnswer implements EntityInterface
{
    abstract public function getId(): UuidInterface;

    abstract public function getText(): string;

    public function asArray(): array
    {
        return [
            'id' => $this->getId()->toString(),
            'text' => $this->getText(),
        ];
    }
}
