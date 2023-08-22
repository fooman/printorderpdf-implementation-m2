# Change Log
## [105.2.0] - 2023-02-08
### Added
- Support for Php 8.2

## [105.1.1] - 2022-03-29
### Added
- pt_BR translation thanks to Vitor Martins

## [105.1.0] - 2022-02-28
### Added
- Support for Php 8.0
- Support for Php 8.1

## [105.0.2] - 2020-10-07
### Added
- Compatibility with Magento 2.3.6

## [105.0.1] - 2020-09-25
### Changed
- Don't keep temporary files in var folder

## [105.0.0] - 2020-09-24
### Changed
- Compatibility with Magento 2.4.1, for Magento 2.4.0 and below use earlier versions

## [104.0.1] - 2020-07-28
### Changed
- Updated copyright notice

## [104.0.0] - 2020-07-21
### Changed
- Compatibility with Magento 2.4, for Magento 2.1 and below use earlier versions
### Added
- Support for Php 7.4

## [103.2.0] - 2019-10-03
### Added
- Support for Php 7.3

## [103.1.3] - 2019-06-25
### Added
- PHPStan to development tools
### Fixed
- Order item sku is required in tests on 2.3.1

## [103.1.2] - 2019-05-02
### Changed
- Adopt latest Magento Coding Standards

## [103.1.1] - 2019-03-27
### Added
- Initial MFTF acceptance test

## [103.1.0] - 2018-11-27
### Added
- Support for Magento 2.3

## [103.0.3] - 2018-07-23
### Changed
- Reorganise unit and integration tests

## [103.0.2] - 2018-07-19
### Fixed
- Adjust integration test for 2.2.5

## [103.0.1] - 2018-05-08
### Added
- Ability to translate more terms (thanks @gediminaskv)
### Fixed
- Minor code style issue

## [103.0.0] - 2018-05-07
### Changed
- Package name renamed to fooman/printorderpdf-implementation-m2, installation should be via metapackage fooman/printorderpdf-m2
- Increased version number by 100 to differentiate from metapackage
### Fixed
- Change setTemplate workaround, use area emulation instead
Constructor change in Plugin\PaymentInfoBlockPlugin, removes copied template files

## [2.2.2] - 2017-09-11
### Changed
- Allow for different pdf versions in test

## [2.2.1] - 2017-08-30
### Changed
- Added preprocessing of tests to run across 2.1/2.2

## [2.2.0] - 2017-08-25
### Fixed
- Empty payment details by providing frontend pdf template for known template files
### Added
- Added support for PHP 7.1

## [2.1.0] - 2017-03-01
### Added
- Support for bundled products

## [2.0.3] - 2016-06-29
### Changed
- Compatibility with Magento 2.1, for Magento 2.0 use earlier versions

## [2.0.2] - 2016-03-30
### Changed
- Test improvements

## [2.0.0] - 2015-12-09
### Changed
- Change folder structure to src/ and tests/

## [1.1.0] - 2015-11-29
### Added
- Provide a Pdf Renderer so that Fooman Email Attachments M2 (separate extension) can attach a pdf to outgoing order confirmation emails
- Translations

## [1.0.2] - 2015-11-15
### Changed
- PSR-2
- Use Magento repositories and factory
- Use Magento massaction
- Update code to stay compatible with latest Magento development branch

## [1.0.1] - 2015-09-07
### Changed
- Update code to stay compatible with latest Magento development branch

## [1.0.0] - 2015-08-02
### Added
- Initial release for Magento 2
