CHANGELOG
=========

2.8.0
-----

The edge case of defining just one value for nodes of type Enum is now allowed:

```php
$rootNode
    ->children()
        ->enumNode('variable')
            ->values(array('value'))
        ->end()
    ->end()
;
```

Before: `InvalidArgumentException` (variable must contain at least two
distinct elements).
After: the code will work as expected and it will restrict the values of the
`variable` option to just `value`.

2.7.0
-----

 * added `ConfigCacheInterface`, `ConfigCacheFactoryInterface` and a basic `ConfigCacheFactory`
   implementation to delegate creation of ConfigCache instances
   
2.2.0
-----

 * added ArrayNodeDefinition::canBeEnabled() and ArrayNodeDefinition::canBeDisabled()
   to ease configuration when some sections are respectively disabled / enabled
   by default.
 * added a `normalizeKeys()` method for array nodes (to avoid key normalization)
 * added numerical type handling for config definitions
 * added convenience methods for optional configuration sections to ArrayNodeDefinition
 * added a utils class for XML manipulations

2.1.0
-----

 * added a way to add documentation on configuration
 * implemented `Serializable` on resources
 * LoaderResolverInterface is now used instead of LoaderResolver for type
   hinting
