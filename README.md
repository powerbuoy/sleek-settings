# [Sleek Settings](https://github.com/powerbuoy/sleek-settings/)

[![Packagist](https://img.shields.io/packagist/vpre/powerbuoy/sleek-settings.svg?style=flat-square)](https://packagist.org/packages/powerbuoy/sleek-settings)
[![GitHub license](https://img.shields.io/github/license/powerbuoy/sleek-settings.svg?style=flat-square)](https://github.com/powerbuoy/sleek-settings/blob/master/LICENSE)
[![GitHub issues](https://img.shields.io/github/issues/powerbuoy/sleek-settings.svg?style=flat-square)](https://github.com/powerbuoy/sleek-settings/issues)
[![GitHub forks](https://img.shields.io/github/forks/powerbuoy/sleek-settings.svg?style=flat-square)](https://github.com/powerbuoy/sleek-settings/network)
[![GitHub stars](https://img.shields.io/github/stars/powerbuoy/sleek-settings.svg?style=flat-square)](https://github.com/powerbuoy/sleek-settings/stargazers)

Adds an options page to the admin (Settings -> Sleek) with two fields: `head_code` and `foot_code` which allows you to add arbitrary HTML to `<head>` and just before `</body>`. Also provides a simple API to add more settings fields to the options page.

## Theme Support

N/A

## Hooks

N/A

## Functions

### `Sleek\Settings\add_setting($name, $type, $label)`

Add a new settings field to the options page.

### `Sleek\Settings\get_setting($name)`

Get value of setting.

## Classes

N/A
