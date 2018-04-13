# EntityChangeWatchBundle

[![travis build](https://travis-ci.org/actiane/EntityChangeWatchBundle.svg?branch=master)](https://travis-ci.org/actiane/EntityChangeWatchBundle)
[![Coverage Status](https://coveralls.io/repos/github/actiane/EntityChangeWatchBundle/badge.svg?branch=master)](https://coveralls.io/github/actiane/EntityChangeWatchBundle?branch=master)

This bundle allow to watch changes made on specific properties of entities using the doctrine2 life cycles events

## INSTALLATION

Create a yaml file inside the packages directory

## USAGE

```yaml
entity_change_watch:
    classes:
        Entity\MyEntity:
            create:
                - {name: 'MyEntityService', method: 'doSomethingBeforeFlush', flush: false}
            delete:
                - {name: 'MyEntityService', method: 'doSomething'}
            update:
                all:
                    - {name: 'MyEntityService', method: 'doSomething'}
                properties:
                    property1:
                        - {name: 'MyEntityService', method: 'doSomething'}
                    property2:
                        - {name: 'MyEntityService', method: 'doSomethingElse'}
```


```php
class MyEntityService
{
    public function doSomething(MyEntity $myEntity)
    {
        /*
        
        do something
        */
    }
    
    public function doSomethingBeforeFlush(MyEntity $myEntity)
        {
            /*
            
            do something before the flush
            */
        }
    
    public function doSomethingElse(MyEntity $myEntity, array $changedProperties)
    {
        /*
        
        do something else
        */
    }
```

The arguments $changedProperties is optional and contains an array with all the changes applied to the entity.

A callback is called after the flush, you can not execute another flush in this method.

If you whish to add or modify entities, you need to set the flush parameter to false

```YAML
 - {name: 'MyEntityService', method: 'doSomethingBeforeFlush', flush: false}
```

If you create and persist a new entity in this callback, then calling EntityManager#persist() is not enough. You have to execute an additional call to `$unitOfWork->computeChangeSet($classMetadata, $entity).`

Changing primitive fields or associations requires you to explicitly trigger a re-computation of the changeset of the affected entity. This can be done by calling `$unitOfWork->recomputeSingleEntityChangeSet($classMetadata, $entity).`
