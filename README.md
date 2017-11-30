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

## Queries

The Query object is created in the format `Query::method()->method()->method()->...` 
and has a set of the following methods. 

```php
Query::new()
    ->where('field', 23)                // WHERE field = 23
    ->where('field', '>', 42)           // WHERE field > 42
    ->whereIn('field', [1, 2, 3])       // WHERE field IN (1, 2, 3)
    ->where('field', [1, 2, 3])         // (alias) WHERE field IN (1, 2, 3)
    ->whereNotIn('field', [1, 2, 3])    // WHERE field NOT IN (1, 2, 3)
    ->whereBetween('field', 1, 2)       // WHERE field BETWEEN 1 AND 2
    ->whereNotBetween('field', 1, 2)    // WHERE field NOT BETWEEN 1 AND 2
    ->whereNull('field')                // WHERE field IS NULL
    ->whereNotNull('field')             // WHERE field IS NOT NULL
    ->orderBy('field', 'asc')           // ORDER BY field ASC
    ->asc('field')                      // (alias) ORDER BY field ASC
    ->desc('field')                     // (alias) ORDER BY field DESC
    ->latest('createdAt')               // (alias) ORDER BY createdAt DESC
    ->oldest('createdAt')               // (alias) ORDER BY createdAt ASC
    ->groupBy('field')                  // GROUP BY field
    ->limit(10)                         // LIMIT 10
    ->take(10)                          // (alias) LIMIT 10
    ->skip(10)                          // OFFSET 10
    ->offset(10)                        // (alias) OFFSET 10
    ->range(100, 150)                   // LIMIT 50 OFFSET 100
    ->with('relation')                  // Relation "relation" eager loading
    
```

Another example:

```php
Query::new()
    ->where('id', '=', 23)
    ->orWhere('id', '>', 42)
    ->orWhereIn('id', [1, 3, 5])
    ->orderBy('createdAt')
    ->orderBy('updatedAt');
/**
 * Result:
 * "SELECT ... FROM ... WHERE id = 23 OR id > 42 OR id IN (1, 3, 5) ORDER BY created_at ASC, updated_at ASC"
 */
 
// Alternatively, you can use the following variant:

Query::where('id', 23)
    ->or->where('id', '>', 42)
    ->or->where('id', [1, 3, 5])
    ->asc('createdAt', 'updatedAt');
 
```

## Eager loading

Suppose we have the following OneToOne relationship between the parent and child.

```php
/** @ORM\Entity */
class Child
{
    /**
     * @var Parent
     * @ORM\OneToOne(targetEntity=Parent::class, inversedBy="child")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;
}

/** @ORM\Entity */
class Parent
{
    /**
     * @var Child
     * @ORM\OneToOne(targetEntity=Child::class, mappedBy="parent")
     */
    private $child;
}
```

Regardless of how you indicate your relationship, it will hit `N+1`, like this:

```php
$query = Query::whereIn('id', [1, 2]);

$children->findAll($query);

/**
 * SELECT ... FROM children d0 WHERE d0.id IN ("1", "2");
 * SELECT ... FROM parents t0 LEFT JOIN children t3 ON t3.parent_id = t0.id WHERE t0.id = "1";
 * SELECT ... FROM parents t0 LEFT JOIN children t3 ON t3.parent_id = t0.id WHERE t0.id = "2";
 */
```

Now let's try to force this relationship and see what happens:

```php
$query = Query::whereIn('id', [1, 2])
    ->with('parent'); // Just add "->with(relationName)" method.

$children->findAll($query);

/**
 * SELECT ... FROM children d0_ LEFT JOIN parents s1_ ON d0_.parent_id = s1_.id WHERE d0_.id IN ("1", "2");
 */
```

Beethooven approves =)

![https://habrastorage.org/webt/lf/hw/dn/lfhwdnvjxlt9vrsbrd_ajpitubc.png](https://habrastorage.org/webt/lf/hw/dn/lfhwdnvjxlt9vrsbrd_ajpitubc.png)

## Repositories

The interface signature has been improved and now contains the following methods.

```php
use Serafim\Hydrogen\Collection;
use Serafim\Hydrogen\Qeury\Builder;

interface ObjectRepository
{
    public function find($id): ?object;
    
    public function findAll(): Collection;
    
    public function findOneBy(Builder $query): ?object;
    
    public function findBy(Builder $query): Collection;
    
    public function count(Builder $query): int;
    
    public function query(): Builder;
}
```

In addition, basic repositories for different types 
of data sources have been added:

- [DatabaseRepository](#DatabaseRepository)
- [MemoryRepository](#MemoryRepository)
- [JsonFileRepository](#JsonFileRepository)
- [PhpFileRepository](#PhpFileRepository)

### DatabaseRepository

```php
use Serafim\Hydrogen\DatabaseRepository;

class Example extends DatabaseRepository {}
```

### MemoryRepository

```php
use Serafim\Hydrogen\MemoryRepository;

class Examples extends MemoryRepository 
{
    protected function getData(): iterable
    {
        yield ['id' => 23, 'created_at' => '2017-11-29 00:03:22'];
        yield ['id' => 42, 'created_at' => '2017-11-29 01:23:22'];
    }
}


/**
 * @ORM\Entity(repositoryClass=Examples::class)
 */
class Example
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     */
    private $id;
    
    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;
}
```

### JsonFileRepository

```php
use Serafim\Hydrogen\JsonFileRepository;

class Examples extends JsonFileRepository 
{
    protected function getFilePath(): string
    {
        return __DIR__ . '/path/to/file.json';
    }
}

// ...
```

### PhpFileRepository

```php
use Serafim\Hydrogen\JsonFileRepository;

class Examples extends PhpFileRepository 
{
    protected function getFilePath(): string
    {
        return __DIR__ . '/path/to/file.php';
    }
}

// ...
```

### Selection 

```php
use Serafim\Hydrogen\Query;

$query = Query::where('id', '>=', 42)->orderBy('id');

$result = $repository->findAll($query);

\var_dump($result->toArray());
```

### "In-place" queries

You can make queries on the spot using these repositories as a data source =)

```php
$repository = $em->getRepository(EntityClass::class);

Query::from($repository)
    ->where('id', 23)
    ->get(); // Collection { EntityClass, EntityClass }
``` 

## Collections

As the base kernel used a [Illuminate Collections](https://laravel.com/docs/5.5/collections) but 
some new features have been added:

- Add HOM proxy autocomplete.
- Added support for global function calls using the [Higher Order Messaging](https://en.wikipedia.org/wiki/Higher_order_message)
 and the [Pattern Matching](https://en.wikipedia.org/wiki/Pattern_matching).
 
### Higher Order Messaging

Pattern "`_`" is used to specify the location of the delegate in
the function arguments in the higher-order messaging while using global functions.

```php
use Serafim\Hydrogen\Collection;

$data = [
    ['value' => '23'],
    ['value' => '42'],
    ['value' => 'Hello!'],
];


$example1 = Collection::make($data)
    ->map->value // ['23', '42', 'Hello!']
    ->toArray();
    
//
// $example1 = \array_map(function (array $item): string {
//      return $item['value']; 
// }, $data);
//

$example2 = Collection::make($data)
    ->map->value     // ['23', '42', 'Hello!']
    ->map->intval(_) // [23, 42, 0]
    ->filter()       // [23, 42]
    ->toArray();
    
//
//
// $example2 = \array_map(function (array $item): string {
//      return $item['value']; 
// }, $data);
//
// $example2 = \array_map(function (string $value): int {
//      return \intval($value);
//                      ^^^^^ - pattern "_" will replaced to each delegated item value. 
// }, $example1);
//
// $example2 = \array_filter($example2, function(int $value): bool {
//      return (bool)$value;
// });
//
//

$example3 = Collection::make($data)
    ->map->value            // ['23', '42', 'Hello!']
    ->map->mbSubstr(_, 1)   // Using "mb_substr(_, 1)" -> ['3', '2', 'ello!']
    ->toArray();
```

### Static constructors

You can create collections (and queries) without using the keyword "new". 
Why do I need it - I do not know, but it looks cool =)

```php
use Serafim\Hydrogen\Collection;

$collection = Collection(...);
// Is alias of "new Collection(...)"
```

### Destructuring

```php
use Serafim\Hydrogen\Collection;

$collection = Collection::make([
    ['a' => 'A1', 'b' => 'B1' 'value' => '23'],
    ['a' => 'A2', 'b' => 'B2' 'value' => '42'],
    ['a' => 'A3', 'b' => 'B3' 'value' => 'Hello!'],
]);

// Displays all data
foreach($collection as $item) {
    \var_dump($item); // [a => 'A*', b => 'B*', value => '***'] 
}

// Displays only "a" field
foreach ($collection as ['a' => $a]) {
    \var_dump($a); // 'A'
}
```
