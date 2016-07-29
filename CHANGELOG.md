1.0.0-beta19

- ClassMetadata implements DoctrineCommon ClassMetadata
- `getClassMetada` in EntityManager now handle node and relationship entity classes
- `@Lazy` on a non-collection relationship doesn't have a lazy effect

1.0.0-beta17

- fixed a bug where fetched RE entities were not marked as managed
- fixed a bug where fetched lazy simple relationships were not marked as managed
- lazy loaded simple relationships have their non-lazy associations marked as lazy

1.0.0-bet13,14,15, 16

- multiple bug fixes

1.0.0-beta12

- Order By on Lazy Loaded Relationship Entities
- Order By on Relationship Entities

1.0.0-beta11

- Some bug fixes with relationship entities
- Real world usage test

1.0.0-beta10

- Lazy loading RelationshipEntities

1.0.0-beta9

- `OrderBy` working with Lazy and findAll()

1.0.0-beta8

- Added `OrderBy` annotations

1.0.0-beta7

- Added proxy implementations

1.0.0-beta6

- Added lazy loading first implementation

1.0.0-beta4

- Added the possibility to define relationship direction as BOTH

1.0.0-beta3

- BC : Renamed `Manager` to `EntityManager`
- Fixed an issue with entities having multiple properties with the same relationship type

1.0.0-beta2

-  Fixed a bug where a related entity was not set on the inversed side
-  Refactored metadata reflection https://github.com/graphaware/neo4j-php-ogm/pull/2

1.0.0-beta1

- First release