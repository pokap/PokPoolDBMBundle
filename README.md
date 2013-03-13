DoctrineMultiBundle
===================

[![Build Status](https://travis-ci.org/pokap/DoctrineMultiBundle.png?branch=master)](https://travis-ci.org/pokap/DoctrineMultiBundle)

Usage
-------------

By default, the object is initialized with values defined in the configuration:

``` yaml

doctrine_multi:
    managers:
        orm:
            id: doctrine.orm.entity_manager

        odm:
            id: doctrine_mongodb.odm.document_manager

    auto_mapping: true
    mappings:
        AcmeDemoBundle: ~
```

Mapping:

``` xml

<multi-model name="user" model="MultiModel\User" repository-class="Repository\UserRepository">
    <model-reference manager="odm" field="id" />

    <model manager="orm" class="Entity\User">
        <field name="name" />
    </model>

    <model manager="odm" class="Document\User">
        <field name="profileContent" />
    </model>
</multi-model>
```
