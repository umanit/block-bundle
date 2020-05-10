# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Added
- Add a `Block` entity with `JOINED` inheritance to handle end user blocks
- Add an optional parameter `parameters` to `umanit_block_render` which will be passed to the block manager `render`
method
- Add a `Block` entity on which all block should rely
- Add an `AbstractBlockType` form type on which all managers form type should rely

### Changed
- Entities table are prefixed with `umanit_`
- Make the Twig extension lazy loaded
- A block manager is no longer a subclass of Symfony form `AbstractType` but it should define the form type used to
manage his block with `getManagedFormType`

### Removed
- Drop support for PHP < 7.1
- Drop support for Symfony < 4.4
- Remove `PanelEventSubscriber` as it becomes useless with the new entity inheritance
- Remove useless `umanit_block` configuration class
- The `AbstractBlockManager` no longer own an `Engine` attribute; If your block rendering needs a template, you must
inject the `@twig` service yourself

## [1.1.6] - 2019-10-30
Initial version for the CHANGELOG. Last version of the 1.x branch.

[Unreleased]: https://github.com/umanit/block-bundle/compare/1.1.6...HEAD
[1.1.6]: https://github.com/umanit/block-bundle/compare/0.1...1.1.6
