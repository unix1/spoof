Changelog
=========

This document will list any non-backward compatible breaking changes between
versions, and notable new features.

0.9
---

potentially breaking changes:
- removed `spoof\lib360\net` namespace

0.8.4
-----

new features:
- added `NotFoundException` interface
- added `ForbiddenException` class

0.8.3
-----

new features:
- added support for `LIKE` and `NOT LIKE` operators
- added `ModelList::toArray()` method
- added `Model::getByCondition` method

0.8.2
-----

new features:
- added `Model::toArray()` and `Record::toArray()` methods

0.8.1
-----

potentially breaking bug fix:
- `ModelList::offsetGet` now always returns an instance of a `Model`

0.8
---

new features:
- `Model` and `ModelList` classes added

0.7
---

new features:
- `ITable::deleteRecord` added

0.6
---

potentially breaking changes:
- `ITable::insert` now returns last inserted ID of the record instead of the
  number of rows affected

0.5
---

This is a relatively major upgrade:

- top namepsace change to `/spoof`
- autoloader change to PSR-4
- composer is now a preferred installation method

0.1
---

This is the original release.
