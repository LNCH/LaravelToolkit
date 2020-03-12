<?php

namespace Lnch\LaravelToolkit\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Lnch\LaravelToolkit\Repositories\Exceptions\ModelNotDefinedException;

class BaseEloquentRepository
{
    /**
     * @var string The fully qualified class name of the base Eloquent model
     *              that the repository belongs to.
     */
    protected $model;

    public function __construct()
    {
        if (!$this->model) {
            throw new ModelNotDefinedException('The repository class is missing an Eloquent model.');
        }
    }

    /**
     * Instantiates a new instance of the given model.
     *
     * @return Model
     */
    protected function make(): Model
    {
        return new $this->model;
    }

    /**
     * Creates a new query builder instance for the given model.
     *
     * @return Builder
     */
    protected function query(): Builder
    {
        return $this->make()->newQuery();
    }

    /**
     * Returns all records for the given model.
     *
     * @return Collection
     */
    public function getAll()
    {
        return $this->query()->get();
    }

    // getOne()

    // create()

    // update()

    // save()

    // beforeDelete()
    // delete()
    // afterDelete()

    // findOne()

    // findAll()
}
