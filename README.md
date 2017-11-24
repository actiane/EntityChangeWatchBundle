# EntityChangeWatchBundle

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


Services must implement Actiane\EntityChangeWatchBundle\Interfaces\InterfaceHelper

```php
class MyEntityService implements InterfaceHelper
{


    public function computeSignature(array $callable, array $parameters)
    {
        return 'MyEntity:' . $callable[1] . ':' . $parameters['entity']->getId();
    }

    public function doSomething(MyEntity $myEntity)
    {
        /*
        
        do something
        */

        return 'MyEntity:doSomething:' . $myEntity->getId();
    }
    
    public function doSomethingElse(MyEntity $myEntity)
    {
        /*
        
        do something else
        */

        return 'MyEntity:doSomethingElse:' . $myEntity->getId();
    }



```
