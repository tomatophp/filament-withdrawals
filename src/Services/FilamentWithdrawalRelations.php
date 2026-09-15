<?php

namespace TomatoPHP\FilamentWithdrawals\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Relation fields of withdrawal forms may only read the models (or relationships) and columns listed in
 * `filament-withdrawals.relations`. Hidden model attributes (password, remember_token, ...) are never
 * exposed, and a class is only touched after it matched an allowlisted name.
 */
class FilamentWithdrawalRelations
{
    /**
     * @return array<string, array<int, string>>
     */
    public static function allowed(): array
    {
        $allowed = [];

        foreach ((array) config('filament-withdrawals.relations', []) as $relation => $columns) {
            if (is_string($relation) && $relation !== '') {
                $allowed[$relation] = array_values(array_filter((array) $columns, 'is_string'));
            }
        }

        return $allowed;
    }

    /**
     * Allowlisted columns of an allowlisted relation, without the related model's hidden attributes.
     *
     * @return array<int, string>
     */
    public static function columnsFor(mixed $relation): array
    {
        $allowed = static::allowed();

        if (! is_string($relation) || ! array_key_exists($relation, $allowed)) {
            return [];
        }

        $columns = $allowed[$relation];

        if (static::isClassName($relation)) {
            if (! static::isModelClass($relation)) {
                return [];
            }

            $columns = array_values(array_diff($columns, (new $relation)->getHidden()));
        }

        return $columns;
    }

    public static function isAllowed(mixed $relation, mixed $column): bool
    {
        return is_string($column) && in_array($column, static::columnsFor($relation), true);
    }

    /**
     * An allowlisted model class name (relationship names do not contain a namespace separator).
     */
    public static function isModelClass(string $relation): bool
    {
        return static::isClassName($relation)
            && array_key_exists($relation, static::allowed())
            && class_exists($relation)
            && is_subclass_of($relation, Model::class);
    }

    /**
     * @return array<string, string>
     */
    public static function relationOptions(): array
    {
        $options = [];

        foreach (array_keys(static::allowed()) as $relation) {
            $options[$relation] = static::isClassName($relation)
                ? class_basename($relation) . " ({$relation})"
                : $relation;
        }

        return $options;
    }

    /**
     * @return array<string, string>
     */
    public static function columnOptions(mixed $relation): array
    {
        $columns = static::columnsFor($relation);

        return array_combine($columns, $columns);
    }

    /**
     * Options of an allowlisted model column, keyed by the model key.
     *
     * @return array<int | string, mixed>
     */
    public static function options(string $relation, string $column): array
    {
        if (! static::isAllowed($relation, $column) || ! static::isModelClass($relation)) {
            return [];
        }

        return $relation::query()->pluck($column, (new $relation)->getKeyName())->all();
    }

    /**
     * Relationship options never list a hidden attribute of the related model.
     */
    public static function withoutHiddenColumn(Builder $query, string $column): Builder
    {
        if (in_array($column, $query->getModel()->getHidden(), true)) {
            $query->whereRaw('1 = 0');
        }

        return $query;
    }

    protected static function isClassName(string $relation): bool
    {
        return str_contains($relation, '\\');
    }
}
