# Change Log for ACF Field Group Values

## 3.3.0 - 2020-10-21
### Added
- Added support for ACF blocks.

## 3.2.0 - 2020-10-14
### Added
- Added support for returning field labels along with values.

## 3.1.0 - 2018-12-26
### Added
- Added support for user meta - props @Boldairdev.

## 3.0.0 - 2018-07-21
### Changed
- Changed array keys used for storing clone fields to be more consistent with how ACF stores the meta in the database. **This is a breaking change for those using clone fields.**
- Update tests to reflect the change related to storing clone field values
- Renamed test data directory

### Added
- Added support for prefixing clone fields

### Fixed
- Fixed issue with nested clone fields not returning correct values

## 2.3.0 - 2018-05-06
### Added
- Mutation testing using Infection
- Restructured tests as Unit, Integration, and System
- Test for get_clone_field_config method
- Support for 'option' or 'options' key

### Updated
- Travis CI script

### Fixed
- Fatal error in rare cases when the package is installed as both a Composer dependency and a WordPress plugin, AND the site does not meet the PHP requirement.

## 2.2.2 - 2018-01-02
### Fixed
- Escaped HTML in the admin notice that displays when PHP requirement is not met.

### Added
- Autoload the helper function file to improve functionality when installed as a composer package.

## 2.2.1 - 2017-12-08
### Fixed
- Fatal error when PHP requirement is not met.

## 2.2.0 - 2017-11-30
### Added
- Unit tests.
- Getter method in `Field_Group_Values()` class.
- Deactivate plugin when PHP requirement is not met.

### Changed
- Do not run `get_all_field_group_values()` when class is instantiated.

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
