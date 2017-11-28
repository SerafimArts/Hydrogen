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

And includes additional support:
- [Repositories](#Repositories)
- [Collections](#Collections)
- TODO: Avoiding N+1 in OneToOne relations.
- TODO: Additional relationships loading control in runtime.
- TODO: Improved query builder.
- and others...


## Installation

**Requirements:**
- `PHP >= 7.1`
- `doctrine/orm: ~2.5`

**Installation:**
- `composer require serafim/hydrogen`

    
## Repositories

In all repositories, method signatures has been changed.

### ObjectRepository

```php
interface ObjectRepository
{
    public function find($id): ?object;
    public function findAll(): Collection;
    public function findOneBy(QueryInterface $query): ?object;
    public function findBy(QueryInterface $query): Collection;
}
```

### DatabaseRepository

Creating a simple database repository.

> Note that as a base class it is worth using `DatabaseRepository`.

```php
use Serafim\Hydrogen\Repository\DatabaseRepository;

class UsersRepositry extends DatabaseRepository 
{
}

///

$em->getRepository(User::class)
    ->findBy(Query::where('login', 'example'))
    ->toArray();
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

$em->getRepository(User::class)
    ->findBy(Query::where('login', 'example'))
    ->toArray();
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

$em->getRepository(User::class)
    ->findBy(Query::where('login', 'example'))
    ->toArray();
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
// 1) "some" casts to int 0, "23" casts to int 23
// 2) Applying `->filter()` to each element (Excluding an "empty" data)
```

## Roadmap

- Repositories:
    - [*] `Serafim\Hydrogen\Repository\DatabaseRepository` - Repository with work on the database.
    - [*] `Serafim\Hydrogen\Repository\MemoryRepository` - Repository with work on the iterable in-memory data.
    - [*] `Serafim\Hydrogen\Repository\JsonFileRepository` - Repository with work on the json file.
    - [*] `Serafim\Hydrogen\Repository\PhpFileRepository` - Repository with work on the php file.
    - Add "scopes" support
- Collections:
    - [*] `*::findAll(): array` updated to `*::findAll(): Collection` 
    - [*] `*::findBy(...): array` updated to `*::findBy(...): Collection`
    - [ ] Update `@OneToMany` relation from `ArrayCollection` to `Collection`
    - [ ] Update `@ManyToMany` relation from `ArrayCollection` to `Collection`
- Optimizations of N+1 and greedy (eager) loading:
    - [ ] Avoid `@OneToOne` relation N+1.
    - [ ] Avoid `@OneToOne` self-referencing relation N+1 (Required as an analog of Embeddable with Discriminator support).
    - [ ] Greedy control at runtime (Like `Repository::with('relation')->findAll()`).
    - [ ] Add Discriminator support into Embeddable type.
- Query Builder Enhancements:
    - [ ] Add `->where(string field, valueOrOperator[, value])` (Like `->where('field', 23)` or `->where('field', '>', 23)`)
    - [ ] Add `->whereBetween(string field, array [a, b])`
    - [ ] Add `->whereNotBetween(string field, array [a, b])`
    - [ ] Add `->whereIn(string field, array values)`
    - [ ] Add `->whereNotIn(string field, array values)`
    - [ ] Add `->whereNull(string field)`
    - [ ] Add `->whereNotNull(string field)`
    - [ ] Add `->orderBy(string field[, string sort = "ASC"])`
    - [ ] Add `->take(?int limit = null)`
    - [ ] Add `->skip(?int offset = null)`
    - [ ] Add `->groupBy(string ...fields)`
    - [ ] Add `->having(string field, valueOrOperator[, value])`
    - [ ] Add `->with(string ...relations)`
    - [ ] Add `->join(string relation, string field, valueOrOperator[, value])`
    - [ ] Add `->leftJoin(string relation, string field, valueOrOperator[, value])`
    - [ ] Add `->crossJoin(string relation)`
    - [ ] Add `->first(): ?object`
    - [ ] Add `->get(): Collection`
    - [ ] Add `->count(): int`
