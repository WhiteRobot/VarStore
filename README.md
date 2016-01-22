# VarStore
Robust variable storage for PHP via xml and xpath.

VarStore is a simple lightweight PHP class for basic NoSQL style storage using the getter/setter method. 

It works well for rapid prototyping very small web projects. The storage mechansim uses PHPs serialization and XML files.

# Getting Started

To get started all you need to do is obtain a copy of the VarStore.php file, include it in a PHP project, and set a directory path for the storage to use.

Once the file has been downloaded it only takes 2 lines of code to set up the system and start using it:

```php
include_once("[path to VarStore.php goes here]");

$storage = new VarStore("[path to where VarStore should store the data goes here]");
```
$storage is, of course, just an example name. Any name can be used.

It's recommended that the data storage path be somewhere outside the accessible HTTP document root. For instance it could be stored in a directory one level above the htdocs folder. This is simply a security measure to protect the data from prying eyes or accidental exposure.

To store data use:

```php
$storage->setVar("[name to use for identifying the data goes here]", [the data to store goes here]);
```

VarStore can store any serializable data. Because nearly all variables in PHP are serializable, VarStore can store nearly any PHP variable. The data will be upserted (https://en.wiktionary.org/wiki/upsert). The name does not have to be a string and can be any value.

To retrieve data use:

```php
$storage->getVar("[name to use for identifying the data goes here]", [default value goes here]);
```

An important concept in VarStore is that a default value must always be provided when retrieving data. This default value will be used in cases where the value is not yet set or cannot be retrieved.
