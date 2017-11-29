# Change Log for ACF Field Group Values

## Unreleased

N/A

## 2.1.2 - 2017-11-29
### Added
- Unit tests.

## 2.1.1 - 2017-11-20
### Fixed
- Post meta values not being retrieved.

## 2.1.0 - 2017-11-18
### Added
- Support for custom fields stored as term meta.

## 2.0.0 - 2017-11-17
### Changed
- The second argument passed to `get_all_custom_field_meta()` must include the field key; pass `$config` instead of 
`$config['fields']`.
- Bumped PHP requirement to 7.0+.

## 1.4.1 - 2017-11-09
### Fixed
- Error with type hinting.

## 1.4.0 - 2017-11-08
## Added
- Support for group fields.
- Partial support for clone fields.

## 1.3.0 - 2017-11-06

### Added
- Refactored into a class.
- Support for custom fields stored as options. 
- Published as a composer package and WordPress plugin.

## 1.2.5 - 2017-10-19

### Fixed
- Error when a flexible content layout type is deleted from the field group.

## 1.2.4 - 2017-10-04

### Fixed
- Empty field values not being stored in results array as of version 1.2.3.

## 1.2.3 - 2017-10-03

### Changed
- Skip processing if post meta value is empty.

## 1.2.2 - 2017-06-12

### Changed
- Array key for storing flexible content field values.

## 1.2.1 - 2017-04-26

### Changed
- Method for building an array of flexible content layout types.

## 1.2.0 - 2017-04-25

### Added
- Better handling of meta key prefix.

## 1.1.1 - 2017-03-02

### Fixed
- Incorrect meta key prefixing.

## 1.1.0 - 2017-02-21

### Added
- Support for flexible layout fields.

## 1.0.0 - 2017-02-20

- Initial release as a [standalone function](https://gist.github.com/timothyjensen/eec64d73f2a44d8b38a078e05abfad4b).
- Support for basic and repeater field types.
