<?php

namespace Luyiyuan\Toolkits\Components\Service;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 *
 */
class EloquentStrategy implements Strategy
{
    /**
     * @var Model
     */
    private Model $model;

    /**
     * @param  Model  $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param  Model  $model
     * @return $this
     */
    public function setModel(Model $model): EloquentStrategy
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @throws Throwable
     */
    public function batchInsert(array $data): array
    {
        DB::beginTransaction();
        try {
            foreach (array_chunk($data, 500) as $attributes) {
                $this->model->query()->insert($attributes);
            }
        } catch (Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
        DB::commit();

        return $data;
    }

    /**
     * @param  array  $data
     * @param  string  $uniqueId
     * @return array
     */
    public function batchUpdate(array $data, string $uniqueId = 'id'): array
    {
        DB::beginTransaction();
        try {
            $model = (new $this->model());
            foreach ($data as $attribute) {
                $model->query()->where($uniqueId, $attribute[$uniqueId])->update($attribute);
            }
        } catch (Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
        DB::commit();

        return $data;
    }

    /**
     * @throws Throwable
     */
    public function batchDelete(array $data, string $uniqueId = 'id'): array
    {
        DB::beginTransaction();
        try {
            foreach (array_chunk($data, 500) as $attributes) {
                $uniqueIds = array_column($attributes, $uniqueId);
                $this->model->query()->whereIn($uniqueId, $uniqueIds)->delete();
            }
        } catch (Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
        DB::commit();

        return $data;
    }
}
