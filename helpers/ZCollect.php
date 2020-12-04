<?php

namespace zetsoft\system\helpers;

use ArrayAccess;
use ArrayIterator;
use Closure;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Traits\EnumeratesValues;
use Illuminate\Support\Traits\Macroable;
use Spatie\CollectionMacros\Exceptions\CollectionItemNotFound;
use Spatie\CollectionMacros\Helpers\CatchableCollectionProxy;
use stdClass;

class ZCollect extends Collection
{
    use EnumeratesValues, Macroable;



#region  Macros functions

    /*https://github.com/spatie/laravel-collection-macros#macros*/
    #region Position
    /**
     *
     * Function  at
     * @param int $index
     * @return  mixed
     */
    public function at(int $index)
    {
        return $this->slice($index, 1)->first();
    }

    public function second()
    {
        return $this->skip(1)->first();
    }

    public function third()
    {
        return $this->skip(2)->first();
    }

    public function fourth()
    {
        return $this->skip(3)->first();
    }

    public function fifth()
    {
        return $this->skip(4)->first();
    }

    public function sixth()
    {
        return $this->skip(5)->first();
    }

    public function seventh()
    {
        return $this->skip(6)->first();
    }

    public function eighth()
    {
        return $this->skip(7)->first();
    }

    public function ninth()
    {
        return $this->skip(8)->first();
    }

    public function tenth()
    {
        return $this->skip(9)->first();
    }

    public function after($currentItem, $fallback = null)
    {
        $currentKey = $this->search($currentItem, true);

        if ($currentKey === false) {
            return $fallback;
        }

        $currentOffset = $this->keys()->search($currentKey, true);

        $next = $this->slice($currentOffset, 2);

        if ($next->count() < 2) {
            return $fallback;
        }

        return $next->last();
    }

    public function before($currentItem, $fallback = null)
    {
        return $this->reverse()->after($currentItem, $fallback);
    }

    #endregion
    public function extract($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();

        return array_reduce($keys, function ($extracted, $key) {
            return $extracted->push(
                data_get($this->items, $key)
            );
        }, new static());
    }

    public function chunkBy(Closure $callback, bool $preserveKeys = false)
    {
        return $this->sliceBefore(function ($item, $prevItem) use ($callback) {
            return $callback($item) !== $callback($prevItem);
        }, $preserveKeys);
    }

    public function collectBy($key, $default = null)
    {
        return new static($this->get($key, $default));
    }

    public function eachCons(int $chunkSize, bool $preserveKeys = false)
    {
        $size = $this->count() - $chunkSize + 1;
        $result = collect(range(0, $size))->reduce(function ($result, $index) use ($chunkSize, $preserveKeys) {
            $next = $this->slice($index, $chunkSize);

            return $next->count() === $chunkSize ? $result->push($preserveKeys ? $next : $next->values()) : $result;
        }, new static([]));

        return $preserveKeys ? $result : $result->values();
    }

    public function filterMap(callable $callback)
    {
        return $this->map($callback)->filter();
    }

    public function firstOrFail()
    {
        if (!is_null($item = $this->first())) {
            return $item;
        }

        throw new CollectionItemNotFound('No items found in collection.');
    }

    public function fromPairs()
    {
        return function (): Collection {
            return $this->reduce(function ($assoc, array $keyValuePair): Collection {
                [$key, $value] = $keyValuePair;
                $assoc[$key] = $value;

                return $assoc;
            }, new static);
        };
    }

    public static function glob(string $pattern, int $flags = 0)
    {
        return self::make(glob($pattern, $flags));
    }

    public function groupByModel($callback, bool $preserveKeys = false, $modelKey = 0, $itemsKey = 1)
    {
        $callback = $this->valueRetriever($callback);

        return $this->groupBy(function ($item) use ($callback) {
            return $callback($item)->getKey();
        }, $preserveKeys)->map(function (Collection $items) use ($callback, $modelKey, $itemsKey) {
            return [
                $modelKey => $callback($items->first()),
                $itemsKey => $items,
            ];
        })->values();
    }

    public function head()
    {
        return $this->first();
    }

    public function ifAny(callable $callback)
    {
        if (!$this->isEmpty()) {
            $callback($this);
        }

        return $this;
    }

    public function ifEmpty(callable $callback)
    {
        if ($this->isEmpty()) {
            $callback($this);
        }

        return $this;
    }

    public function none($key, $value = null)
    {
        if (func_num_args() === 2) {
            return !$this->contains($key, $value);
        }

        return !$this->contains($key);
    }

    /**
     * Paginate the given collection.
     *
     * @param int $perPage
     * @param int $total
     * @param int $page
     * @param string $pageName
     *
     */

    public function paginate(int $perPage = 15, string $pageName = 'page', int $page = null, int $total = null, array $options = [])
    {
        $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);

        $results = $this->forPage($page, $perPage)->values();

        $total = $total ?: $this->count();

        $options += [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ];

        return new LengthAwarePaginator($results, $total, $perPage, $page, $options);
    }

    /**
     *
     * Function  parallelMap
     * @param callable $callback
     * @param null $workers
     * @return  $this
     * @throws \Throwable
     */
    public function parallelMap(callable $callback, $workers = null)
    {
        $pool = null;

        if ($workers instanceof Pool) {
            $pool = $workers;
        }

        if (is_int($workers)) {
            $pool = new DefaultPool($workers);
        }

        $promises = parallelMap($this->items, $callback, $pool);

        return new static(wait($promises));
    }

    public function pluckToArray($value, $key = null)
    {
        return $this->pluck($value, $key)->toArray();
    }

    public function prioritize(callable $callable)
    {
        $nonPrioritized = $this->reject($callable);

        return $this
            ->filter($callable)
            ->union($nonPrioritized);
    }

    public function rotate(callable $callable)
    {
        $nonPrioritized = $this->reject($callable);

        return $this->filter($callable)->union($nonPrioritized);
    }

    public function sectionBy($key, bool $preserveKeys = false, $sectionKey = 0, $itemsKey = 1)
    {
        $sectionNameRetriever = $this->valueRetriever($key);

        $results = new Collection();

        foreach ($this->items as $key => $value) {
            $sectionName = $sectionNameRetriever($value);

            if (!$results->last() || $results->last()->get($sectionKey) !== $sectionName) {
                $results->push(new Collection([
                    $sectionKey => $sectionName,
                    $itemsKey => new Collection(),
                ]));
            }

            $results->last()->get($itemsKey)->offsetSet($preserveKeys ? $key : null, $value);
        }

        return $results;
    }

    public function simplePaginate(int $perPage = 15, string $pageName = 'page', int $page = null, array $options = [])
    {
        $page = $page ?: Paginator::resolveCurrentPage($pageName);

        $results = $this->slice(($page - 1) * $perPage)->take($perPage + 1);

        $options += [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ];

        return new Paginator($results, $perPage, $page, $options);
    }

    public function sliceBefore($callback, bool $preserveKeys = false)
    {
        if ($this->isEmpty()) {
            return new static();
        }

        if (!$preserveKeys) {
            $sliced = new static([
                new static([$this->first()]),
            ]);

            return $this->eachCons(2)->reduce(function ($sliced, $previousAndCurrent) use ($callback) {
                [$previousItem, $item] = $previousAndCurrent;

                $callback($item, $previousItem)
                    ? $sliced->push(new static([$item]))
                    : $sliced->last()->push($item);

                return $sliced;
            }, $sliced);
        }

        $sliced = new static([$this->take(1)]);

        return $this->eachCons(2, $preserveKeys)->reduce(function ($sliced, $previousAndCurrent) use ($callback) {
            $previousItem = $previousAndCurrent->take(1);
            $item = $previousAndCurrent->take(-1);

            $itemKey = $item->keys()->first();
            $valuesItem = $item->first();
            $valuesPreviousItem = $previousItem->first();

            $callback($valuesItem, $valuesPreviousItem)
                ? $sliced->push($item)
                : $sliced->last()->put($itemKey, $valuesItem);

            return $sliced;
        }, $sliced);
    }

    public function tail(bool $preserveKeys = false)
    {
        return !$preserveKeys ? $this->slice(1)->values() : $this->slice(1);
    }

    public function toPairs()
    {
        return $this->keys()->map(function ($key) {
            return [$key, $this->items[$key]];
        });
    }

    public function transpose()
    {
        if ($this->isEmpty()) {
            return new static();
        }

        $firstItem = $this->first();

        $expectedLength = is_array($firstItem) || $firstItem instanceof Countable ? count($firstItem) : 0;

        array_walk($this->items, function ($row) use ($expectedLength) {
            if ((is_array($row) || $row instanceof Countable) && count($row) !== $expectedLength) {
                throw new \LengthException("Element's length must be equal.");
            }
        });

        $items = array_map(function (...$items) {
            return new static($items);
        }, ...array_map(function ($items) {
            return $this->getArrayableItems($items);
        }, array_values($this->items)));

        return new static($items);
    }

    public function tryCatch()
    {
        return new CatchableCollectionProxy($this);
    }

    public function validate($callback)
    {
        if (is_string($callback) || is_array($callback)) {
            $validationRule = $callback;

            $callback = function ($item) use ($validationRule) {
                if (!is_array($item)) {
                    $item = ['default' => $item];
                }

                if (!is_array($validationRule)) {
                    $validationRule = ['default' => $validationRule];
                }

                return app('validator')->make($item, $validationRule)->passes();
            };
        }

        foreach ($this->items as $item) {
            if (!$callback($item)) {
                return false;
            }
        }

        return true;
    }

    public function withSize(int $size)
    {
        if ($size < 1) {
            return new ZCollect();
        }

        return new ZCollect(range(1, $size));
    }
    #endregion
    public static function range($from, $to)
    {

    }

    public function skipUntil($value)
    {

    }

    public function skipWhile($value)
    {

    }

    public function chunkWhile(callable $callback)
    {

    }

    public function sortDesc($options = SORT_REGULAR)
    {

    }

    public function takeUntil($value)
    {

    }

    public function takeWhile($value)
    {

    }
}
