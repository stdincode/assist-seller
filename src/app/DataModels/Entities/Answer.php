<?php

namespace App\DataModels\Entities;

use Ramsey\Uuid\UuidInterface;

class Answer extends AbstractAnswer
{
    private UuidInterface $id;
    private string $text;

    public function __construct(
        UuidInterface $id,
        string $text
    )
    {
        $this->id = $id;
        $this->text = $text;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
