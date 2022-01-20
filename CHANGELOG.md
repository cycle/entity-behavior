# CHANGELOG

v1.1.0 (20.01.2022)
-------------------
- Add `SourceInterface $source` property to the `MapperEvent`by @msmakouz (#18)
- Add nullable parameter to the updatedAt behavior by @msmakouz (#23)

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
