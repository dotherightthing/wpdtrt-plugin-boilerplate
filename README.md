# DTRT Plugin Boilerplate

[![GitHub issues](https://img.shields.io/github/issues/dotherightthing/wpdtrt-plugin.svg)](https://github.com/dotherightthing/wpdtrt-plugin/issues)

Base classes for a WordPress plugin and associated shortcodes and widgets.

## Demo usage

[DTRT Blocks](https://github.com/dotherightthing/wpdtrt-blocks) is the demo plugin.

## Set up

The included Gulp task,

1. installs Composer dependencies
2. performs PHP linting
3. generates PHP documentation
4. converts SCSS into CSS

```
// 1. Install Node dependencies
npm --prefix ./vendor/dotherightthing/wpdtrt-plugin/ install ./vendor/dotherightthing/wpdtrt-plugin/

// 2. Run the default Gulp task set
// 3. Watch for changes
gulp --gulpfile ./vendor/dotherightthing/wpdtrt-plugin/gulpfile.js --cwd ./
```

## Background

### Goals

The goals of this plugin are:

* to consolidate best practice techniques
* to create a familiar, standardised interface for plugin development
* to allow the boilerplate code to be maintained independently of the plugin functionality
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
