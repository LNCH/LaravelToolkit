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

        // Loads the active model if set, for use in getOne(), update(), delete(), etc.
        if ($this->activeModelInstance) {
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

    /**
     * Returns a single instance of the repository class.
     *
     * Will either return the first record, or if a model is set, it will
     * return the given model, or model that corresponds to a given model ID.
     *
     * @param $model
     * @return Model|null
     * @throws InvalidModelException
     */
    public function getOne($model = null): ?Model
    {
        $this->loadModel($model);
        return $this->query()->first();
    }

    /**
     * Creates a new record.
     *
     * @param array $data
     * @return Model|null
     * @throws InvalidModelException
     */
    public function create(array $data)
    {
        return $this->persist($data);
    }

    /**
     * Updates a given model.
     *
     * @param       $model
     * @param array $data
     * @return Model|null
     * @throws InvalidModelException
     */
    public function update($model, array $data)
    {
        return $this->persist($data, $model);
    }

    // beforeDelete()

    /**
     * Deletes the given model.
     *
     * @param $model
     * @return bool|null
     * @throws InvalidModelException
     */
    public function delete($model)
    {
        $this->loadModel($model);

        return $this->activeModelInstance
            ? $this->activeModelInstance->delete()
            : null;
    }

    // afterDelete()

    // findOne()

    // findAll()

    /**
     * Handles persisting data to the DB layer.
     *
     * If the model is provided it will perform an update, if no model
     * provided, a new instance will be created.
     *
     * @param array $data
     * @param null  $model
     * @return Model|null
     * @throws InvalidModelException
     */
    public function persist(array $data, $model = null)
    {
        $this->loadModel($model);
        $model = $this->activeModelInstance ?? $this->make();

        $model->fill($data);

        if ($model->save()) {
            return $model;
        }

        return null;
    }

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
        if (!$model) { return; } // $model may be null, if so, we do nothing

        if ($model instanceof Model && !$model instanceof $this->model) {
            throw new InvalidModelException();
        }

        $this->activeModelInstance = $model instanceof $this->model
            ? $model
            : $this->make()->find($model);
    }
}
