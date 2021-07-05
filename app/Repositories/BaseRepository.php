<?php

namespace App\Repositories;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;

abstract class BaseRepository
{
    /**
     * @var string
     */
    protected $model;

    /**
     * @var \Illuminate\Container\Container
     */
    protected $app;

    /**
     * Creates a new repository instance.
     *
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->makeModel();
    }

    /**
     * Configures the Model
     *
     * @return string
     */
    abstract public function model(): string;

    /**
     * Makes Model instance
     *
     * @return void
     * @throws \Exception
     */
    protected function makeModel(): void
    {
        $model = $this->app->make($this->model());

        if (! $model instanceof Model) {
            throw new Exception(sprintf('Class %s must be an instance of %s', $this->model(), Model::class));
        }

        $this->model = $model;
    }

    /**
     * @param  array|mixed  $columns
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function all($columns = ['*'])
    {
        return $this->model->all($columns);
    }

    /**
     * @param  array  $attributes
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  array $attributes
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update(Model $model, array $attributes)
    {
        $model->fill($attributes);
        $model->save();

        return $model;
    }

    /**
     * @param  mixed  $id
     * @param  array  $columns
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Support\Collection
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail($id, array $columns = ['*'])
    {
        return $this->model->findOrFail($id, $columns);
    }
}
