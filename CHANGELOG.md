## 1.1.0 - 2021-03-16

- BC Break: Removed support for PHP 5
- Fixed: Updated base object's name to not trigger a Fatal error due to `object` being a reserved keyword.

## 1.0.7 - 2019-01-24

- Fixed:  Test for the end of IPv4 address space in Inetnum::convertCIDR() now works properly on 64 bit systems.

## 1.0.6 - 2018-11-26

- Updated RPSL definitions to RIPE DB version 1.92

## 1.0.5 - 2017-10-17

- Updated RPSL definitions to RIPE DB version 1.90

## 1.0.4 - 2017-03-30

- Fixed: Setting `sponsoring-org` in `Inetnum` and `Inet6num` as a regular (not auto-generated) attribute as it must not be omitted from the request if it was previously set

## 1.0.3 - 2016-11-17

- Fixed: Using the RPSL composite primary key (`route` + `origin`) for `Route` and `Route6` objects

## 1.0.2 - 2016-09-19

- Fixed: The `source` attribute will now retain its set value when passed to the web service (previously it always got assigned either TEST or RIPE, depending on the web service’s environment)

## 1.0.1 - 2016-08-26

- Fixed: Iterating over an attribute having an `AttributeValue` object created additional loop cycles.

## 1.0.0 - 2016-07-30

- public release
