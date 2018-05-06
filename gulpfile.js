/**
 * Gulp Task Runner
 * Compile front-end resources
 *
 * @example usage from parent plugin:
 *    gulp
 *    gulp dev
 *    gulp dist
 *
 * @example usage from child plugin:
 *    gulp --gulpfile ./vendor/dotherightthing/wpdtrt-plugin/gulpfile.js --cwd ./
 *    gulp dev --gulpfile ./vendor/dotherightthing/wpdtrt-plugin/gulpfile.js --cwd ./
 *    gulp dist --gulpfile ./vendor/dotherightthing/wpdtrt-plugin/gulpfile.js --cwd ./
 *
 * @package     WPPlugin
 * @version     1.3.6
 */

/* jshint node: true */
/* global require, process */

// dependencies

var gulp = require('gulp');
var autoprefixer = require('autoprefixer');
var del = require('del');
var jsdoc = require('gulp-jsdoc3');
var jshint = require('gulp-jshint');
var log = require('fancy-log');
var phplint = require('gulp-phplint');
var postcss = require('gulp-postcss');
var print = require('gulp-print').default;
var pxtorem = require('postcss-pxtorem');
var replace = require('gulp-replace');
var runSequence = require('run-sequence');
var sass = require('gulp-sass');
var shell = require('gulp-shell');
var zip = require('gulp-zip');

// paths

// pop() - remove the last element from the path array and return it
var pluginName = process.cwd().split('/').pop();

var cssDir = 'css';
var distDir = pluginName;
var dummyFile = 'README.md';
var jsFiles = './js/*.js';
var phpFiles = [
  './**/*.php',
  '!node_modules/**/*',
  '!vendor/**/*',
  '!' + pluginName + '/**/*' // release folder
];
var scssFiles = './scss/*.scss';

// helpers

// @see: https://stackoverflow.com/a/27535245/6850747
gulp.Gulp.prototype.__runTask = gulp.Gulp.prototype._runTask;
gulp.Gulp.prototype._runTask = function(task) {
  this.currentTask = task;
  this.__runTask(task);
};

function taskheader(task, message) {

  if ( typeof(message) === 'undefined' ) {
    message = '';
  }
  else {
    message += ' ';
  }

  log(' ');
  log('========== ' + task.currentTask.name + ' ' + message + '==========');
  log(' ');
}

// @see: https://stackoverflow.com/a/48616491/6850747
function moduleIsAvailable(path) {
  try {
    require.resolve(path);
    return true;
  } catch (e) {
    return false;
  }
}

// tasks

gulp.task('yarn', function () {

  taskheader(this);

  // return stream or promise for run-sequence
  return gulp.src(dummyFile, {read: false})
    .pipe(shell([
      'yarn install --non-interactive'
    ])
  );
});

gulp.task('yarn_dist', function () {

  taskheader(this);

  // return stream or promise for run-sequence
  return gulp.src(dummyFile, {read: false})
    .pipe(shell([
      'yarn install --non-interactive --production'
    ])
  );
});

gulp.task('composer', function () {

  taskheader(this);

  // return stream or promise for run-sequence
  return gulp.src(dummyFile, {read: false})
    .pipe(shell([
      'composer install --prefer-dist --no-interaction'
    ])
  );
});

gulp.task('css', function () {

  taskheader(this);

  var processors = [
      autoprefixer({
        cascade: false
      }),
      pxtorem({
        rootValue: 16,
        unitPrecision: 5,
        propList: [
          'font',
          'font-size',
          'padding',
          'padding-top',
          'padding-right',
          'padding-bottom',
          'padding-left',
          'margin',
          'margin-top',
          'margin-right',
          'margin-bottom',
          'margin-left',
          'bottom',
          'top',
          'max-width'
        ],
        selectorBlackList: [],
        replace: false,
        mediaQuery: true,
        minPixelValue: 0
      })
  ];

  // return stream or promise for run-sequence
  return gulp.src(scssFiles)
    .pipe(sass({outputStyle: 'expanded'}))
    .pipe(postcss(processors))
    .pipe(gulp.dest(cssDir));
});

gulp.task('finish', function () {

  taskheader(this);
});

gulp.task('js', function() {

  taskheader(this);

  var jsdocConfig = require('./jsdocConfig');

  // return stream or promise for run-sequence
  return gulp.src(jsFiles)
    .pipe(jshint())
    .pipe(jshint.reporter('default', { verbose: true }))
    .pipe(jshint.reporter('fail'))
    // note: output cannot be piped on from jsdoc
    .pipe(jsdoc(jsdocConfig));
});

gulp.task('phpdoc_delete', function () {

  taskheader(this);

  // return stream or promise for run-sequence
  return del([
    'docs/phpdoc'
  ]);
});

gulp.task('phpdoc_remove_before', function() {

  taskheader(this);

  // Read the extra data from the parent's composer.json
  // The require function is relative to this gulpfile || node_modules
  // @see https://stackoverflow.com/a/23643087/6850747
  var composer_json = require('./composer.json'),
      phpdoc_remove_before = composer_json['extra'][0]['require-after-phpdoc'],
      phpdoc_remove_before_no_version = phpdoc_remove_before.split(':')[0];

  // return stream or promise for run-sequence
  // note: src files are not used,
  // this structure is only used
  // to include the preceding log()
  return gulp.src(dummyFile, {read: false})
    .pipe(shell([
      // install plugin which generates Fatal Error (#12)
      // if previously installed via package.json
      'composer remove ' + phpdoc_remove_before_no_version
    ])
  );
});

gulp.task('phpdoc_doc', function() {

  taskheader(this);

  // return stream or promise for run-sequence
  // note: src files are not used,
  // this structure is only used
  // to include the preceding log()
  return gulp.src(dummyFile, {read: false})
    .pipe(shell([
      'vendor/bin/phpdoc -d . -t ./docs/phpdoc'
    ])
  );
});

gulp.task('phpdoc_require_after', function() {

  taskheader(this);

  // Read the extra data from the parent's composer.json
  // The require function is relative to this gulpfile || node_modules
  // @see https://stackoverflow.com/a/23643087/6850747
  var composer_json = require('./composer.json'),
      phpdoc_require_after = composer_json['extra'][0]['require-after-phpdoc'];

  // return stream or promise for run-sequence
  // note: src files are not used,
  // this structure is only used
  // to include the preceding log()
  return gulp.src(dummyFile, {read: false})
    .pipe(shell([
      // install plugin which generates Fatal Error (#12)
      // if previously installed via package.json
      'composer require ' + phpdoc_require_after
    ])
  );
});

gulp.task('phplint', function () {

  taskheader(this);

  // return stream or promise for run-sequence
  return gulp.src(phpFiles)

    // validate PHP
    // The linter ships with PHP
    .pipe(phplint())
    .pipe(phplint.reporter(function(file) {
      var report = file.phplintReport || {};

      if (report.error) {
        log.error(report.message+' on line '+report.line+' of '+report.filename);
      }
    }));
});

gulp.task('phpunit', function() {

  taskheader(this);

  return gulp.src(dummyFile, {read: false})
    .pipe(shell([
      'phpunit'
    ])
  );
});

gulp.task('release_delete_pre', function () {

  taskheader(this);

  // return stream or promise for run-sequence
  return del([
    'release.zip'
  ]);
});

/**
 * The parent plugin has various dev dependencies
 * which need to be made available to the child plugin for install tasks.
 * Composer projects only install dev dependencies listed in their own require-dev,
 * so we copy in the parent dev dependencies so that these are available to the child too.
 * This approach allows us to easily remove all dev dependencies,
 * before zipping project files,
 * by re-running the composer install with the --no-dev flag.
 *
 * See also 'Command Line Configuration', above.
 *
 * @see https://github.com/dotherightthing/wpdtrt-plugin/issues/47
 * @see https://github.com/dotherightthing/wpdtrt-plugin/issues/51
 */
gulp.task('add_dev_dependencies', function() {

  taskheader(this);

  if ( pluginName !== 'wpdtrt-plugin' ) {

    // Read the require-dev list from the parent's composer.json
    // The require function is relative to this gulpfile || node_modules
    // @see https://stackoverflow.com/a/23643087/6850747
    var composer_json = require('./composer.json'),
        dev_packages = composer_json['require-dev'],
        dev_packages_str = '';

    // convert the require-dev list into a space-separated string
    // foo/bar:1.2.3
    // @see https://stackoverflow.com/a/1963179/6850747
    for (var pkg in dev_packages) {
      if (dev_packages.hasOwnProperty(pkg)) {
        dev_packages_str += (' ' + pkg + ':' + dev_packages[pkg]);
      }
    }

    // add each dependency from the parent's require-dev
    // to the child's require-dev
    return gulp.src(dummyFile, {read: false})
      .pipe(shell([
        'composer require' + dev_packages_str + ' --dev'
      ]));

    }
    else {
      return;
    }
});

gulp.task('remove_dev_dependencies', function() {

  taskheader(this);

  /**
   * Remove dev packages once we've used them
   * @see https://github.com/dotherightthing/wpdtrt-plugin/issues/47
   */
  return gulp.src(dummyFile, {read: false})
    .pipe(shell([
      'composer install --prefer-dist --no-interaction --no-dev'
    ])
  );
});

gulp.task('release_delete_post', function () {

  taskheader(this);

  // return stream or promise for run-sequence
  return del([
    distDir
  ]);
});

gulp.task('release_copy', function() {

  taskheader(this);

  // @see http://www.globtester.com/
  var releaseFiles = [
    './config/**/*',
    './css/**/*',
    './docs/**/*',
    './images/**/*',
    '!./images/**/*.pxm',
    './js/**/*',
    './languages/**/*',
    './node_modules/**/*',
    '!./node_modules/wpdtrt-plugin', // Yarn environment symlink
    '!./node_modules/wpdtrt-plugin/**/*', // Yarn environment symlink contents
    './src/**/*',
    './template-parts/**/*',
    './vendor/**/*',
    './views/**/*',
    './index.php',
    './readme.txt',
    './uninstall.php',
    './' + pluginName + '.php'
  ];

  // return stream or promise for run-sequence
  // https://stackoverflow.com/a/32188928/6850747
  return gulp.src(releaseFiles, { base: '.' })
    .pipe(print())
    .pipe(gulp.dest(distDir));
});

gulp.task('release_zip', function() {

  taskheader(this);

  // return stream or promise for run-sequence
  // https://stackoverflow.com/a/32188928/6850747
  return gulp.src([
    './' + distDir + '/**/*'
  ], { base: '.' })
  .pipe(zip('release.zip'))
  .pipe(gulp.dest('./'));
});

gulp.task('release', function(callback) {

  taskheader(this);

  runSequence(
    'release_delete_pre',
    'release_copy',
    'release_zip',
    'release_delete_post',
    callback
  );
});

gulp.task('start', function () {

  taskheader(this);
});

/**
 * Tasks
 * @todo https://github.com/dotherightthing/wpdtrt-plugin/issues/60
 */ 

gulp.task('watch', function () {

  taskheader(this);

  gulp.watch( scssFiles, ['css'] );
  gulp.watch( jsFiles, ['js'] );
  gulp.watch( phpFiles, ['phplint'] );
});

gulp.task('bump', function() {

  var root_pkg,
      wpdtrt_plugin_pkg,
      wpdtrt_plugin_is_dependency,
      wpdtrt_plugin_pkg_version_escaped,
      wpdtrt_plugin_pkg_version_namespaced;

  // get the root package.json
  // require() is relative to the active gulpfile not to the CWD
  // as it is wpdtrt-plugin/gulpfile.js which is always run
  // ./package.json will always be wpdtrt-plugin/package.json

  if ( moduleIsAvailable( '../../../package.json' ) ) {
    wpdtrt_plugin_is_dependency = true;
  }
  else {
    wpdtrt_plugin_is_dependency = false;
  }

  // wpdtrt-foo
  if ( wpdtrt_plugin_is_dependency ) {

    // get the latest release of wpdtrt-plugin
    gulp.src(dummyFile, {read: false})
      .pipe(shell([
        'composer update dotherightthing/wpdtrt-plugin --no-interaction'
      ])
    );    

    // after getting the latest version
    // get the latest release number
    root_pkg = require('../../../package.json');
    wpdtrt_plugin_pkg = require('./package.json');
    wpdtrt_plugin_pkg_version_escaped = wpdtrt_plugin_pkg.version.split('.').join('\\.');
    wpdtrt_plugin_pkg_version_namespaced = wpdtrt_plugin_pkg.version.split('.').join('_');

    // bump wpdtrt-foo to 0.1.2 and wpdtrt-plugin 1.2.3 using package.json
    taskheader(this, root_pkg.name + ' to ' + root_pkg.version + ' and ' + wpdtrt_plugin_pkg.name + ' ' + wpdtrt_plugin_pkg.version + ' using package.json' );

    // DoTheRightThing\WPPlugin\r_1_2_3
    gulp.src([
        './src/class-' + root_pkg.name + '-plugin.php',
        './src/class-' + root_pkg.name + '-widgets.php'
      ])
      .pipe(replace(
        /(DoTheRightThing\\WPPlugin\\r_)([0-9]_[0-9]_[0-9])/,
        '$1' + wpdtrt_plugin_pkg_version_namespaced
      ))
      .pipe(gulp.dest('./src/'));

    // * @version 1.2.3
    gulp.src('./gulpfile.js')
      .pipe(replace(
        /(\* @version\s+)([0-9]\.[0-9]\.[0-9])/,
        '$1' + root_pkg.version
      ))
      .pipe(gulp.dest('./'));

    gulp.src('./readme.txt')
      .pipe(replace(
        // Stable tag: 1.2.3
        /(Stable tag:.)([0-9]\.[0-9]\.[0-9])/,
        '$1' + root_pkg.version
      ))
      .pipe(replace(
        // == Changelog ==
        //
        // = 1.2.3 =
        //
        // @see https://github.com/dotherightthing/wpdtrt-plugin/issues/101
        /(== Changelog ==\n\n= )([0-9]\.[0-9]\.[0-9])+( =\n)/,
        "$1" + root_pkg.version + " =\r\r= $2$3"
      ))
      .pipe(gulp.dest('./'));

    gulp.src([
        './' + root_pkg.name + '.php'
      ])
      // DoTheRightThing\WPPlugin\r_1_2_3
      .pipe(replace(
        /(DoTheRightThing\\WPPlugin\\r_)([0-9]_[0-9]_[0-9])/,
        '$1' + wpdtrt_plugin_pkg_version_namespaced
      ))
      // * Version: 1.2.3
      .pipe(replace(
        /(\* Version:\s+)([0-9]\.[0-9]\.[0-9])/,
        '$1' + root_pkg.version
      ))
      // define( 'WPDTRT_TEST_VERSION', '1.2.3' );
      .pipe(replace(
        /(define\( 'WPDTRT_TEST_VERSION', ')([0-9]\.[0-9]\.[0-9])(.+;)/,
        '$1' + root_pkg.version + '$3'
      ))
      .pipe(gulp.dest('./'));
  }
  // wpdtrt-plugin
  else {

    // get the latest release number
    wpdtrt_plugin_pkg = require('./package.json');
    wpdtrt_plugin_pkg_version_escaped = wpdtrt_plugin_pkg.version.split('.').join('\\.');
    wpdtrt_plugin_pkg_version_namespaced = wpdtrt_plugin_pkg.version.split('.').join('_');

    taskheader(this, wpdtrt_plugin_pkg.name + ' to ' + wpdtrt_plugin_pkg.version + ' using package.json' );

    // DoTheRightThing\WPPlugin\r_1_2_3
    gulp.src([
        './src/class-wpdtrt-test-plugin.php',
        './src/class-wpdtrt-test-widgets.php',
        './src/Shortcode.php',
        './src/Taxonomy.php',
        './src/TemplateLoader.php',
        './src/Widget.php'
      ])
      .pipe(replace(
        /(DoTheRightThing\\WPPlugin\\r_)([0-9]_[0-9]_[0-9])/,
        '$1' + wpdtrt_plugin_pkg_version_namespaced
      ))
      .pipe(gulp.dest('./src/'));

    gulp.src('./src/Plugin.php')
      // DoTheRightThing\WPPlugin\r_1_2_3
      .pipe(replace(
        /(DoTheRightThing\\WPPlugin\\r_)([0-9]_[0-9]_[0-9])/,
        '$1' + wpdtrt_plugin_pkg_version_namespaced
      ))
      // const WPPLUGIN_VERSION = '1.2.3';
      .pipe(replace(
        /(const WPPLUGIN_VERSION = ')([0-9]\.[0-9]\.[0-9])(';)/,
        '$1' + wpdtrt_plugin_pkg.version + '$3'
      ))
      .pipe(gulp.dest('./src/'));

    // "DoTheRightThing\\WPPlugin\\r_1_2_3\\": "src"
    gulp.src('./composer.json')
      .pipe(replace(
        /("DoTheRightThing\\\\WPPlugin\\\\r_)([0-9]_[0-9]_[0-9])(\\\\")/,
        '$1' + wpdtrt_plugin_pkg_version_namespaced + '$3'
      ))
      .pipe(gulp.dest('./'));

    // * @version 1.2.3
    gulp.src('./index.php')
      .pipe(replace(
        /(\* @version\s+)([0-9]\.[0-9]\.[0-9])/,
        '$1' + wpdtrt_plugin_pkg.version
      ))
      .pipe(gulp.dest('./'));

    gulp.src('./readme.txt')
      .pipe(replace(
        // Stable tag: 1.2.3
        /(Stable tag:.)([0-9]\.[0-9]\.[0-9])/,
        '$1' + wpdtrt_plugin_pkg.version
      ))
      .pipe(replace(
        // == Changelog ==
        //
        // = 1.2.3 =
        //
        // @see https://github.com/dotherightthing/wpdtrt-plugin/issues/101
        /(== Changelog ==\n\n= )([0-9]\.[0-9]\.[0-9])+( =\n)/,
        "$1" + wpdtrt_plugin_pkg.version + " =\r\r= $2$3"
      ))
      .pipe(gulp.dest('./'));

    gulp.src('./wpdtrt-plugin.php')
      // * Version: 1.2.3
      .pipe(replace(
        /(\* Version:\s+)([0-9]\.[0-9]\.[0-9])/,
        '$1' + wpdtrt_plugin_pkg.version
      ))
      // define( 'WPDTRT_TEST_VERSION', '1.2.3' );
      .pipe(replace(
        /(define\( 'WPDTRT_TEST_VERSION', ')([0-9]\.[0-9]\.[0-9])(.+;)/,
        '$1' + wpdtrt_plugin_pkg.version + '$3'
      ))
      .pipe(gulp.dest('./'));

    // regenerate autoload files
    gulp.src(dummyFile, {read: false})
      .pipe(shell([
        'composer dump-autoload --no-interaction'
      ])
    );
  }
});

gulp.task('install', function(callback) {
  runSequence(
    'start',
    'yarn',
    'composer',
    'add_dev_dependencies',
    'css',
    'js',
    'phplint',
    'phpdoc_doc',
    'phpdoc_require_after',
    'phpunit',
    'finish'
  );

  callback();
});

gulp.task('dev', function(callback) {
  runSequence(
    'start',
    'yarn',
    'composer',
    'add_dev_dependencies',
    'css',
    'js',
    'phplint',
    'phpdoc_delete',
    'phpdoc_remove_before',
    'phpdoc_doc',
    'phpdoc_require_after',
    'phpunit',
    'finish',
    'watch'
  );

  callback();
});

gulp.task('dist', function(callback) {
  runSequence(
    'start',
    'yarn',
    'composer',
    'add_dev_dependencies',
    'css',
    'js',
    'phplint',
    'phpdoc_delete',
    'phpdoc_remove_before',
    'phpdoc_doc',
    'phpdoc_require_after',
    'phpunit',
    // package release
    'remove_dev_dependencies',
    'yarn_dist',
    'release',
    // reinstall dev tools
    'yarn',
    'add_dev_dependencies',
    'finish'
  );

  callback();
});

gulp.task('default', [
  'install'
]);
