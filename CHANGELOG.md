# CHANGELOG


v1.1.0 under development
------------------------
- Add `SourceInterface $source` property to the `MapperEvent`

v1.0.0 (22.12.2021)
-------------------
- Add behaviors:
  - `CreatedAt`
  - `OptimisticLock`
  - `SoftDelete`
  - `UpdatedAt`
  - `EventListener`
  - `Hook`
- Add `EventDrivenCommandGenerator` with custom event dispatcher.
- Supported events: `OnCreate`, `OnDelete` and `OnUpdate`
