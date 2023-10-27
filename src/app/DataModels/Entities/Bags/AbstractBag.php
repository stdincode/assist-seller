<?php

namespace App\DataModels\Entities\Bags;

use App\DataModels\Entities\EntityInterface;
use App\Enums\SortParams\SortDirections;

abstract class AbstractBag
{
    protected array $models;
    protected int $total;
    private ?string $sort;
    private ?SortDirections $sortDirection;
    private ?int $page;
    private ?int $perPage;

    /**
     * @param EntityInterface[] $models
     */
    public function __construct(array $models = [])
    {
        $this->models = $models;
    }

    public function asArray(): array
    {
        return array_map(
            fn (EntityInterface $model) => $model->asArray(),
            $this->getAll()
        );
    }

    /**
     * @return EntityInterface[]
     */
    public function getAll(): array
    {
        return $this->models;
    }

    /**
     * @param EntityInterface $model
     * @return void
     */
    public function add(EntityInterface $model): void
    {
        $this->models[] = $model;

    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return count($this->models);
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @param int|null $total
     * @return void
     */
    public function setTotal(?int $total): void
    {
        $this->total = $total;
    }

    /**
     * @return string
     */
    public function getSort(): string
    {
        return $this->sort ?? 'created_at';
    }

    /**
     * @param string|null $sort
     * @return void
     */
    public function setSort(?string $sort): void
    {
        $this->sort = $sort;
    }

    /**
     * @return SortDirections
     */
    public function getSortDirection(): SortDirections
    {
        return $this->sortDirection ?? SortDirections::ASC;
    }

    /**
     * @param string|null $sortDirection
     * @return void
     */
    public function setSortDirection(?string $sortDirection): void
    {
        if ($sortDirection === 'desc') {
            $this->sortDirection = SortDirections::DESC;
        } elseif ($sortDirection === 'asc') {
            $this->sortDirection = SortDirections::ASC;
        }
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page ?? 1;
    }

    /**
     * @param int|null $page
     * @return void
     */
    public function setPage(?int $page): void
    {
        $this->page = $page;
    }

    /**
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->perPage ?? 10;
    }

    /**
     * @param int|null $perPage
     * @return void
     */
    public function setPerPage(?int $perPage): void
    {
        $this->perPage = $perPage;
    }
}
