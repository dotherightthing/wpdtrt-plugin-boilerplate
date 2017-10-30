# DTRT Plugin Boilerplate

[![GitHub issues](https://img.shields.io/github/issues/dotherightthing/wpdtrt-plugin.svg)](https://github.com/dotherightthing/wpdtrt-plugin/issues)

Base classes for a WordPress plugin and associated shortcodes and widgets.

## Demo usage

[wpdtrt-blocks](https://github.com/dotherightthing/wpdtrt-blocks)

## Goals

The goals of this plugin are:

* to consolidate best practice techniques
* to move boilerplate code into reusable classes
* to create a familiar, standardised interface for plugin development
* to allow the plugin authors to focus on the plugin functionality during development
* to allow the boilerplate code to be maintained independently of the plugin functionality

## History

This is an evolution of several other approaches:

### [generator-wp-plugin-boilerplate](https://github.com/dotherightthing)

+ generates a WordPress plugin from a Yeoman template
- difficult to track evolving changes to boilerplate code

### wpdtrt

+ WordPress parent theme
+ bundles common functionality
- functionality too tightly coupled

### 3rd party class

- someone else's code
- not intuitive
