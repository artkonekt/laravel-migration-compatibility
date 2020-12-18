# Changelog
#### Laravel Migration Compatibility

## 1.x Series

### 1.4.1
##### 2020-12-18

- Fixed SQLite detector data type string case mismatch (integer vs INTEGER)
- Changed CI from Travis to Github Actions 🥺

### 1.4.0
##### 2020-12-07

- Dropped PHP 7.2 support
- Dropped Laravel 5 support
- Added PHP 8 support

### 1.3.0
##### 2020-09-12

- Added Laravel 8 support

### 1.2.0
##### 2020-03-13

- Added Laravel 7 support
- Added PHP 7.4 support
- Dropped PHP 7.1 support

### 1.1.0
##### 2019-11-23

- Added Laravel 6 support
- Removed Laravel 5.4 support

### 1.0.1
##### 2019-08-25

- Fixed the missing Laravel auto package discovery entry in composer.json

### 1.0.0
##### 2019-08-18

- Very first release
- Supports
  - detection from mysql, postgres and sqlite databases,
  - detection from config,
  - guess from Laravel version (heuristic)
- Works with:
  - PHP 7.1 - 7.3
  - Laravel 5.4 - 5.8
