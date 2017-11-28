<p align="center">
    <img src="./resources/logo.png" alt="Hydrogen" />
</p>

<p align="center">
    <a href="https://packagist.org/packages/serafim/hydrogen"><img src="https://poser.pugx.org/serafim/hydrogen/version" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/serafim/hydrogen"><img src="https://poser.pugx.org/serafim/hydrogen/v/unstable" alt="Latest Unstable Version"></a>
    <a href="https://raw.githubusercontent.com/serafim/hydrogen/master/LICENSE"><img src="https://poser.pugx.org/serafim/hydrogen/license" alt="License MIT"></a>
</p>

## Introduction

This package contains a set of frequently used functions of Doctrine ORM 
that are optimized for more convenient usage.

## Installation

**Requirements:**
- `PHP >= 7.1`
- `doctrine/orm: ~2.5`

**Installation:**
- `composer require serafim/hydrogen`


## Roadmap (about)

- Repositories:
    - `Serafim\Hydrogen\Repository\DatabaseRepository` - Repository with work on the database.
    - `Serafim\Hydrogen\Repository\MemoryRepository` - Repository with work on the iterable in-memory data.
    - `Serafim\Hydrogen\Repository\JsonFileRepository` - Repository with work on the json file.
    - `Serafim\Hydrogen\Repository\PhpFileRepository` - Repository with work on the php file.
    - **TODO**: Add "scopes" support
- Collections:
    - `*::findAll(): array` updated to `*::findAll(): Collection` 
    - `*::findBy(...): array` updated to `*::findBy(...): Collection`
    - **TODO**: Update `@OneToMany` relation from `ArrayCollection` to `Collection`
    - **TODO**: Update `@ManyToMany` relation from `ArrayCollection` to `Collection`
- Optimizations of N+1 and greedy (eager) loading:
    - **TODO**: Added `@OneToOne` relation optimisation.
    - **TODO**: Added `@OneToMany` relation optimisation.
    - **TODO**: Added `@OneToOne` self-referencing relation optimisation (Required as an analog of Embeddable with Discriminator support).
    - **TODO**: Greedy control at runtime (Like `Repository::with('relation')->findAll()`)
- Query Builder Enhancements:
    - **TODO**: Added `->where(string field, valueOrOperator[, value])` (Like `->where('field', 23)` or `->where('field', '>', 23)`)
    - **TODO**: Added `->whereBetween(string field, array [a, b])`
    - **TODO**: Added `->whereNotBetween(string field, array [a, b])`
    - **TODO**: Added `->whereIn(string field, array values)`
    - **TODO**: Added `->whereNotIn(string field, array values)`
    - **TODO**: Added `->whereNull(string field)`
    - **TODO**: Added `->whereNotNull(string field)`
    - **TODO**: Added `->orderBy(string field[, string sort = "ASC"])`
    - **TODO**: Added `->take(?int limit = null)`
    - **TODO**: Added `->skip(?int offset = null)`
    - **TODO**: Added `->groupBy(string ...fields)`
    - **TODO**: Added `->having(string field, valueOrOperator[, value])`
    - **TODO**: Added `->with(string ...relations)`
    - **TODO**: Added `->join(string relation, string field, valueOrOperator[, value])`
    - **TODO**: Added `->leftJoin(string relation, string field, valueOrOperator[, value])`
    - **TODO**: Added `->crossJoin(string relation)`
    - **TODO**: Added `->first(): ?object`
    - **TODO**: Added `->get(): Collection`
    - **TODO**: Added `->count(): int`
    - etc...
    
## Repositories

In all repositories, method signatures have been changed:

- From `find($id)`
    - To `find($id): ?object`
- From `findOneBy(array $criteria)`
    - To `findBy(Query $query): ?object`
- From `findAll()`
    - To `findAll(): Collection`
- From `findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)`
    - To `findBy(Query $query): Collection`

### DatabaseRepository

Creating a simple database repository.

> Note that as a base class it is worth using `DatabaseRepository`.

```php
use Serafim\Hydrogen\Repository\DatabaseRepository;

class UsersRepositry extends DatabaseRepository 
{
}

///

$em->getRepository(User::class)->findBy(Query::where('login', 'any'))->toArray(); // [User, User, ...]
```

### MemoryRepository

Creating a simple in-memory repository.

> Note that as a base class it is worth using `MemoryRepository`.

```php
use Serafim\Hydrogen\Repository\MemoryRepository;

class UsersRepositry extends MemoryRepository 
{
    protected function getData(): iterable
    {
        yield new User(...);
        yield new User(...);
    }
}

///

$em->getRepository(User::class)->findBy(Query::where('login', 'any'))->toArray(); // [User, User, ...]
```

### JsonFileRepository and PhpFileRepository

Creating a simple file repository.

> Note that as a base class it is worth using `JsonFileRepository` or `PhpFileRepository`.

```php
use Serafim\Hydrogen\Repository\PhpFileRepository;

class UsersRepositry extends PhpFileRepository 
{
    public function getFilePath(): string
    {
        return __DIR__ . '/path/to/file.php'; // or json
    }
}

///

$em->getRepository(User::class)->findBy(Query::where('login', 'any'))->toArray(); // [User, User, ...]
```

## Collections

As the base kernel used a [Illuminate Collections](https://laravel.com/docs/5.5/collections) but 
some new features have been added:

- Add HOM proxy autocomplete.
- Added support for global function calls using the [Higher Order Messaging](https://en.wikipedia.org/wiki/Higher_order_message)
 and the [Pattern Matching](https://en.wikipedia.org/wiki/Pattern_matching).
 
### Higher Order Messaging improvements

Simple code example.

```php
use Serafim\Hydrogen\Collection;

Collection::make(['23', '42', 'some'])->map->intval(_)->toArray(); // [23, 42, 0]
``` 

This pattern "_" is used to specify the location of the delegate in
the function arguments in the higher-order messaging.

Example 1:

```php
$array = Collection::make(...)->map->intval(_, 10)->toArray();

// Is similar with:

$array = \array_map(function ($item): int {
     return \intval($item, 10);
     //             ^^^^^ - pattern "_" will replaced to each delegated item value.
}, ...);
```

Example 2:
```php
$monad = Collection::make([
    function($value): int { return (int)$value; }
]);

$monad->map->array_filter(["some", "23"], _)->filter()->toArray(); // [23]

// What's going on inside
1) "some" casts to int 0, "23" casts to int 23
2) Applying `->filter()` to each element (Excluding an "empty" data)
```
