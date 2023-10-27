<?php

namespace Support\Data;

use Domain\Organization\Models\Employee;
use Domain\Users\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Arr;
use Mockery\Exception;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Casts\Castable;
use Spatie\LaravelData\Support\DataProperty;

class Creator implements Castable
{
    public function __construct(
        public string|int $creator_id,
        public string $creator_type,
        public readonly User|Employee|null $instance = null
    ) {

    }

    public function getInstance(): User|Employee
    {
        return $this->instance ?? $this->creator_type::find($this->creator_id);
    }

    public static function dataCastUsing(...$arguments): Cast
    {
        return new class(...$arguments) implements Cast
        {
            private bool $lazyLoadInstance;

            public function __construct(...$arguments)
            {
                $this->lazyLoadInstance = $arguments['lazy_load_instance'] ?? $arguments['lazyLoadInstance'] ?? false;
            }

            public function cast(DataProperty $property, mixed $value, array $context): mixed
            {
                if ($value instanceof Authenticatable) {
                    return new Creator($value->getKey(), $value::class, $value);
                }

                if (is_array($value) && ! Arr::has($value, ['id', 'type'])) {
                    throw new Exception('array $value must have `id` and `type` keys');
                }

                [$creator_id, $creator_type] = [$value['id'], $value['type']];

                $instance = $this->lazyLoadInstance ? $creator_type::find($creator_id) : null;

                return new Creator($creator_id, $creator_type, $instance);
            }
        };
    }
}
