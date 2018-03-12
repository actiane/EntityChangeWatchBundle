# EntityChangeWatchBundle

This bundle allow to watch changes made on specific properties of entities using the doctrine2 life cycles events

## INSTALLATION

Create a yaml file inside the packages directory

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