# DTRT WP Plugin

[![GitHub tags](https://img.shields.io/github/tag/dotherightthing/wpdtrt-plugin.svg)](https://github.com/dotherightthing/wpdtrt-plugin/tags) [![GitHub issues](https://img.shields.io/github/issues/dotherightthing/wpdtrt-plugin.svg)](https://github.com/dotherightthing/wpdtrt-plugin/issues)

Base classes for a WordPress plugin and associated shortcodes and widgets.

---

## Set up a plugin

### System requirements

1. [WordPress](https://wordpress.org/)
2. [Node.js & NPM](https://nodejs.org/en/)
3. [Composer](https://getcomposer.org/)
4. [Bower](https://bower.io/)
5. [Gulp](https://gulpjs.com/)

### Manually mirror the set up of the demo plugin

The demo plugin is [DTRT Blocks](https://github.com/dotherightthing/wpdtrt-blocks).

### Install PHP dependencies, including the WP Plugin base class:

```
composer install
```

### Install the Gulp dependencies required by WP Plugin:

```
npm --prefix ./vendor/dotherightthing/wpdtrt-plugin/ install ./vendor/dotherightthing/wpdtrt-plugin/
```

### Run WP Plugin's Gulp tasks on the plugin files:

1. Install (any new) PHP dependencies (Composer)
2. Install PHP and front-end dependencies which don't have `composer.json` files (Bower)
3. Lint PHP code
4. Generate PHP documentation
5. Convert SCSS into CSS
6. Watch for changes to files

```
gulp --gulpfile ./vendor/dotherightthing/wpdtrt-plugin/gulpfile.js --cwd ./
```

---

## Maintenance of WP Plugin

A) Run maintenance tasks from the WP Plugin folder

```
npm install
gulp maintenance
```

Update plugin version:

* at the top of any edited methods
* at the top of any edited files
* at the bottom of the edited README.md
* tag the release
* update the version constant in the root file of child plugins

---

## Background

### Goals

The goals of this plugin are:

* to consolidate best practice techniques
* to create a familiar, standardised interface for plugin development
* to allow the boilerplate code to be maintained independently of the plugin functionality
* to permit a focus on plugin functionality during development

### History

This is an evolution of several other approaches:

#### A) [Yeoman Plugin Generator](https://github.com/dotherightthing/generator-wp-plugin-boilerplate)

+ generates a WordPress plugin from a Yeoman template
- difficult to track evolving changes to boilerplate code

#### B) [Base theme](https://github.com/dotherightthing/wpdtrt)

+ WordPress parent theme
+ bundles common functionality
- functionality too tightly coupled

#### C) 3rd party class

- someone else's code
- not intuitive

---

DTRT WP Plugin | README version 1.0.1
