PokPoolDBMBundle
===================

**Requires** at least *PHP 5.3.3* with Symfony 2 library. Compatible PHP 5.4 too.

[![Build Status](https://travis-ci.org/pokap/PokPoolDBMBundle.png?branch=master)](https://travis-ci.org/pokap/PokPoolDBMBundle)

Usage
-------------

By default, the object is initialized with values defined in the configuration:

``` yaml

pok_pool_dbm:
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

<multi-model model="MultiModel\User" repository-class="Repository\UserRepository">
    <model-reference manager="odm" field="id" />

    <model manager="orm" name="Entity\User" repository-method="findByIds">
        <field name="name" />
    </model>

    <model manager="odm" name="Document\User">
        <field name="profileContent" />
    </model>
</multi-model>
```
