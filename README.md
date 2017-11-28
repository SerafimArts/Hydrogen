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

- Repositories:
    - `Serafim\Hydrogen\Repository\DatabaseRepository` - Repository with work on the database.
    - `Serafim\Hydrogen\Repository\MemoryRepository` - Repository with work on the iterable in-memory data.
    - `Serafim\Hydrogen\Repository\JsonFileRepository` - Repository with work on the json file.
    - `Serafim\Hydrogen\Repository\PhpFileRepository` - Repository with work on the php file.
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
    
