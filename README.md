# EntityChangeWatchBundle

[![Build Status](https://travis-ci.org/actiane/EntityChangeWatchBundle.svg?branch=v2.0)](https://travis-ci.org/actiane/EntityChangeWatchBundle)
[![Coverage Status](https://coveralls.io/repos/github/actiane/EntityChangeWatchBundle/badge.svg?branch=v2.0)](https://coveralls.io/github/actiane/EntityChangeWatchBundle?branch=v2.0)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/actiane/EntityChangeWatchBundle/badges/quality-score.png?b=v2.0)](https://scrutinizer-ci.com/g/actiane/EntityChangeWatchBundle/?branch=v2.0)

This bundle allow to watch changes made on specific properties of entities using the doctrine2 life cycles events

## INSTALLATION

AppKernel
```php
  new Actiane\EntityChangeWatchBundle\EntityChangeWatchBundle(),
```

## USAGE

```yaml
entity_change_watch:
    classes:
        Entity\MyEntity:
            create:
                - {name: 'MyEntityService', method: 'doSomething'}
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
    
    public function doSomethingElse(MyEntity $myEntity, array $changedProperties)
    {
        /*
        
        do something else
        */
    }
```

The arguments $changedProperties is optional and contains an array with all the changes applied to the entity.