<?php

namespace App\DataModels\Entities\Bags;

use App\DataModels\Entities\AbstractTelegramClientStep;
use App\Enums\SortParams\SortDirections;

interface TelegramClientStepsBagInterface
{
    /**
     * @return AbstractTelegramClientStep[]
     */
    public function getAll(): array;

    /**
     * @param AbstractTelegramClientStep $telegramClientStep
     * @return void
     */
    public function add(AbstractTelegramClientStep $telegramClientStep): void;

    /**
     * @return int
     */
    public function getCount(): int;

    /**
     * @return int
     */
    public function getTotal(): int;

    /**
     * @param int|null $total
     * @return void
     */
    public function setTotal(?int $total): void;

    /**
     * @return string
     */
    public function getSort(): string;

    /**
     * @param string|null $sort
     * @return void
     */
    public function setSort(?string $sort): void;

    /**
     * @return SortDirections
     */
    public function getSortDirection(): SortDirections;

    /**
     * @param string|null $sortDirection
     * @return void
     */
    public function setSortDirection(?string $sortDirection): void;

    /**
     * @return int
     */
    public function getPage(): int;

    /**
     * @param int|null $page
     * @return void
     */
    public function setPage(?int $page): void;

    /**
     * @return int
     */
    public function getPerPage(): int;

    /**
     * @param int|null $perPage
     * @return void
     */
    public function setPerPage(?int $perPage): void;
}
