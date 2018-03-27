# DTRT WP Plugin

[![GitHub tags](https://img.shields.io/github/tag/dotherightthing/wpdtrt-plugin.svg)](https://github.com/dotherightthing/wpdtrt-plugin/tags) [![Build Status](https://travis-ci.org/dotherightthing/wpdtrt-plugin.svg?branch=wpplugin)](https://travis-ci.org/dotherightthing/wpdtrt-plugin) [![GitHub issues](https://img.shields.io/github/issues/dotherightthing/wpdtrt-plugin.svg)](https://github.com/dotherightthing/wpdtrt-plugin/issues)

Base classes for a WordPress plugin and associated shortcodes and widgets.

---

## Setup and Maintenance

### System requirements

1. [WordPress](https://wordpress.org/)
2. [Node.js & NPM](https://nodejs.org/en/)
3. [Composer](https://getcomposer.org/)
4. [Bower](https://bower.io/)
5. [Gulp](https://gulpjs.com/)

### Running Gulp tasks

### Set up a new plugin

[Install and run the WordPress Plugin Boilerplate Generator](https://github.com/dotherightthing/generator-wp-plugin-boilerplate#installation).

### Develop child plugins, or maintain this one

#### Run development build task

1. Install (any new) PHP dependencies (Composer)
2. Install PHP and front-end dependencies which don't have `composer.json` files (Bower)
3. Lint PHP and JavaScript code
4. Generate PHP documentation
5. Convert SCSS into CSS
6. Watch for changes to files

```
// from a generated 'child' plugin:
$ gulp dev --gulpfile ./vendor/dotherightthing/wpdtrt-plugin/gulpfile.js --cwd ./

// from this plugin
$ gulp dev
```

#### Run distribution build task

1. Install (any new) PHP dependencies (Composer)
2. Install PHP and front-end dependencies which don't have `composer.json` files (Bower)
3. Lint PHP and JavaScript code
4. Generate PHP documentation
5. Convert SCSS into CSS
6. Generate `./release.zip`

```
// from a generated 'child' plugin:
$ gulp dist --gulpfile ./vendor/dotherightthing/wpdtrt-plugin/gulpfile.js --cwd ./

// from this plugin
$ gulp dist
```

#### Run unit tests

WP_UnitTestCase tests create a dedicated WordPress environment at runtime.

```
# 1. Run default build task

# 2. Install WordPress Test Suite
$ bash bin/install-wp-tests.sh wpdtrt_plugin_test YOUR_DB_USERNAME YOUR_DB_PASSWORD 127.0.0.1 latest

# 3. Run ./tests
$ phpunit
```

*If you are having problems installing the WordPress Test Suite, please read [PHP Unit Testing, revisited](http://kb.dotherightthing.dan/php/wordpress/php-unit-testing-revisited/) for setup gotchas and solutions.*

---

## Background

### Goals

The goals of this plugin are:

* to support modular plugin-based WordPress development
* to consolidate best practice techniques
* to create a familiar, standardised interface for plugin development
* to allow the boilerplate code to be maintained independently of the plugin functionality
* to allow different versions of the boilerplate to exist, to reduce maintenance time
* to permit a focus on plugin functionality during development

### History

This is an evolution of several other approaches:

#### [Yeoman Plugin Generator](https://github.com/dotherightthing/generator-wp-plugin-boilerplate)

+ generates a WordPress plugin from a Yeoman template
- difficult to track evolving changes to boilerplate code

#### [Base theme](https://github.com/dotherightthing/wpdtrt)

+ WordPress parent theme
+ bundles common functionality
- functionality too tightly coupled

#### 3rd party class

- someone else's code
- not intuitive
