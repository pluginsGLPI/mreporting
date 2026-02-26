# Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [UNRELEASED]

### Fixed

- Fix GLIP Stat injection

## [1.9.3] - 2026-01-08

### Fixed

- Update locales
- Fix SQL query

## [1.9.3] - 2026-01-08

### Fixed

- Fix SQL alias errors for reportVstackbarLifetime chart
- Fix empty `WHERE` clause
- Fix `limit` clause
- Fix dashboard error on display

## [1.9.2] - 2025-12-02

### Fixed

- Fix warning during CLI installation

## [1.9.1] - 2025-10-27

### Fixed

- Fix display right form


## [1.9.0] - 2025-10-01

### Added

- GLPI 11 compatibility

## [1.8.9] - 2025-09-30

 - Fix SQL alias errors and undefined array keys
 - Fix missing `delay` to where clause
 - Fix weekly backlog calculation using wrong week reference point (#294)
 - Fix the SQL query for the backlog graph (#290)

## [1.8.8] - 2025-08-07

### Fixed

 - Fix dynamic properties and png rendering deprecations (#279)
 - Issue #253 - Fixes deprecated dynamic property in baseclass.class.php (#272)

## [1.8.7] - 2025-01-29

### Fixed

- Remove the message about a deprecated php function in debug mode

## [1.8.6] - 2024-02-13

### Added

- Slovak locale and reports translations

## [1.8.5] - 2023-10-19

### Fixed

- No data displayed when using "week" period

## [1.8.4] - 2023-10-11

### Fixed

- Error during migration

## [1.8.3] - 2023-09-14

### Added

- Japanese (Japan) locale
- Spanish (Ecuador) locale
- Polish translations for reports

### Fixed

- Wrong entity filtering in `reportHbarComputersByEntity`
- Undefined index `f_name`

## [1.8.2] - 2022-09-29

### Fixed

- Display all windows distributions

## [1.8.1] - 2022-08-22

### Fixed

- Prevent useless DB queries on plugin state checks
- Missing title breaks menu

## [1.8.0] - 2022-04-20

### Added

- GLPI 10.0 compatibility

## [1.7.4] - 2022-03-01

### Added

- Spanish (Venezuela) locale

## [1.7.3] - 2021-04-27

### Fixed

- Deprecated usage of `$order` param in `getAllDataFromTable()`

## [1.7.2] - 2021-03-09

### Fixed

- Unable to install from marketplace

## [1.7.1] - 2020-12-18

### Fixed

- OpenSuse not included in Linux category for ComputerByOS report

## [1.7.0] - 2020-07-07

### Added

- GLPI 9.5 compatibility
- Croatian (Croatia) locale

### Fixed

- Load of JS/CSS resources only when needed
- Autocompletion fields whitelist

## [1.6.1] - 2019-02-15

### Fixed

- Icons not displayed

## [1.6.0] - 2018-12-10

### Added

- GLPI 9.4 compatibility

## [1.5.3] - 2018-10-26

### Fixed

- SQL error on update process on MySQL 5.7
- Useless rights management for simplified profiles
- Missing reports in select list
- Missing javascript loading

## [1.5.2] - 2018-09-04

### Added

- Chinese locale

### Changed

- Use InnoDB as DB table engine

### Fixed

- Display of `Windows 10` in inventory report
- Usage of methods deprecated in GLPI 9.3

## [1.5.1] - 2018-07-06

### Fixed

- Finnish (Finland) locale
- Undefined index in profile form

## [1.5.0] - 2018-06-28

### Added

- GLPI 9.3 compatibility

### Fixed

- Usage of deprecated `each` PHP function

## [1.4.1] - 2018-06-21

### Added

- Czech, German and Portuguese translations

### Fixed

- Error while using `GLPI` graphtype
- Deprecated calls to NotificationTarget class
- Deprecated usage of `fieldExists()`
- Notifications not working on GLPI 9.2
- Compatibility check for GLPI < 9.2

## [1.4.0] - 2017-09-22

### Added

- GLPI 9.2 compatibility

## [1.3.1] - 2016-11-18

### Fixed

- Bad comparaison in Nb tickets per SLA bug (thanks to awslgr)
- Review dashboard feature on helpdesk interface
- Remove require `GLPI_PHPMAILER_DIR`
- Selection from the dropdown itilcategory (thanks to myoaction)
- Don't use Templates and Deleted computers (thanks to johannsan)
- Error Ext on GLPI 0.90 (thanks to tsmr - Infotel Conseil)
- Queries filtered by status for inventory reporting (thanks to sebfun)

## [1.3.0] - 2016-10-17

### Added

- GLPI 9.1 compatibility

### Fixed

- Fix SLA graph
- Fix manufacturer graph
- Prevent not logged in users to display graphs
