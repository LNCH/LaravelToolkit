<?php

namespace Lnch\LaravelToolkit\Repositories;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Lnch\LaravelToolkit\Repositories\Exceptions\InvalidModelException;
use Lnch\LaravelToolkit\Repositories\Exceptions\ModelNotDefinedException;

class BaseEloquentRepository
{
    /**
     * @var string The fully qualified class name of the base Eloquent model
     *              that the repository belongs to.
     */
    protected $model;

    private $activeModelInstance;

    public function __construct()
    {
        if (!$this->model) {
            throw new ModelNotDefinedException("The '".get_class($this)."' repository class
                is missing an Eloquent model.");
        }
    }

    /**
     * Instantiates a new instance of the given model.
     *
     * @return Model
     */
    protected function make(): Model
    {
        $model = new $this->model;
        $this->modelPk = $model->getKeyName();

        return $model;
    }

    /**
     * Creates a new query builder instance for the given model.
     *
     * @return Builder
     */
    protected function query(): Builder
    {
        $query = $this->make()->newQuery();

        if ($this->activeModelInstance) {
            dd($this->activeModelInstance);
            $query->where(
                $this->activeModelInstance->getKeyName(),
                $this->activeModelInstance->getKey()
            );
        }

        return $query;
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

    public function getOne($model = null)
    {
        if ($model) {
            $this->loadModel($model);
        }

        return $this->query()->first();
    }

    // create()

    // update()

    // save()

    // beforeDelete()
    // delete()
    // afterDelete()

    // findOne()

    // findAll()

    /**
     * Loads an active instance of the given model for use in queries.
     *
     * Takes either an instance of the repository model, or a value for the
     * model's primary key.
     *
     * @param $model
     * @throws InvalidModelException
     */
    private function loadModel($model): void
    {
        if ($model instanceof Model && !$model instanceof $this->model) {
            throw new InvalidModelException();
        }

        $this->activeModelInstance = $model instanceof Model
            ? $model
            : $this->model::find($model)->first();
    }
}
