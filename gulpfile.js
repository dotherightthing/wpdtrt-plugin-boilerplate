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
 * @version     1.2.15
 */

/* jshint node: true */
/* global require, process */

// dependencies

var gulp = require('gulp');
var autoprefixer = require('autoprefixer');
var del = require('del');
var jshint = require('gulp-jshint');
var log = require('fancy-log');
var phplint = require('gulp-phplint');
var postcss = require('gulp-postcss');
var print = require('gulp-print').default;
var pxtorem = require('postcss-pxtorem');
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

// tasks

gulp.task('bower', function () {

  log(' ');
  log('========== 1. bower ==========');
  log(' ');

  // return stream or promise for run-sequence
  return gulp.src(dummyFile, {read: false})
    .pipe(shell([
      'bower install'
    ])
  );
});

gulp.task('composer', function () {

  log(' ');
  log('========== 2. composer ==========');
  log(' ');

  // return stream or promise for run-sequence
  return gulp.src(dummyFile, {read: false})
    .pipe(shell([
      'composer install --prefer-dist --no-interaction'
    ])
  );
});

gulp.task('css', function () {

  log(' ');
  log('========== 3. css ==========');
  log(' ');

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

  log(' ');
  log('========== All Tasks Complete ==========');
  log(' ');
});

gulp.task('js', function() {

  log(' ');
  log('========== 4. js =========='); // validate JS
  log(' ');

  // return stream or promise for run-sequence
  return gulp.src(jsFiles)
    .pipe(jshint())
    .pipe(jshint.reporter('default', { verbose: true }))
    .pipe(jshint.reporter('fail'));
});

gulp.task('list_files', function() {

  log(' ');
  log('========== 8. list_files ==========');
  log(' ');

  // return stream or promise for run-sequence
  return gulp.src('./*')
    .pipe(print());
});

gulp.task('phpdoc_delete', function () {

  log(' ');
  log('========== 6a. phpdoc_delete ==========');
  log(' ');

  // return stream or promise for run-sequence
  return del([
    'docs/phpdoc'
  ]);
});

gulp.task('phpdoc_remove_before', function() {

  log(' ');
  log('========== 6b. phpdoc_remove_before ==========');
  log(' ');

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

  log(' ');
  log('========== 6c. phpdoc_doc ==========');
  log(' ');

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

  log(' ');
  log('========== 6d. phpdoc_require_after ==========');
  log(' ');

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

gulp.task('phpdoc', function(callback) {

  log(' ');
  log('========== 6. phpdoc ==========');
  log(' ');

  // return?
  runSequence(
    'phpdoc_delete',
    'phpdoc_remove_before',
    'phpdoc_doc',
    'phpdoc_require_after',
    callback
  );
});

gulp.task('phplint', function () {

  log(' ');
  log('========== 5. phplint ==========');
  log(' ');

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

gulp.task('release_delete_pre', function () {

  log(' ');
  log('========== 7a. release_delete_pre ==========');
  log(' ');

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

  log(' ');
  log('========== add_dev_dependencies ==========');
  log(' ');

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

  log(' ');
  log('========== remove_dev_dependencies ==========');
  log(' ');

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

  log(' ');
  log('========== 7d. release_delete_post ==========');
  log(' ');

  // return stream or promise for run-sequence
  return del([
    cssDir,
    distDir
  ]);
});

gulp.task('release_copy', function() {

  log(' ');
  log('========== 7b. release_copy ==========');
  log(' ');

  // @see http://www.globtester.com/
  var releaseFiles = [
    './config/**/*',
    './css/**/*',
    './docs/**/*',
    './js/**/*',
    './languages/**/*',
    './src/**/*',
    './template-parts/**/*',
    './vendor/**/*',
    './views/**/*',
    './index.php',
    './readme.txt',
    './uninstall.php',
    './' + pluginName + '.php',
    '!**/node_modules/**/*'
  ];

  // return stream or promise for run-sequence
  // https://stackoverflow.com/a/32188928/6850747
  return gulp.src(releaseFiles, { base: '.' })
    .pipe(print())
    .pipe(gulp.dest(distDir));
});

gulp.task('release_zip', function() {

  log(' ');
  log('========== 7c. release_zip ==========');
  log(' ');

  // return stream or promise for run-sequence
  // https://stackoverflow.com/a/32188928/6850747
  return gulp.src([
    './' + distDir + '/**/*'
  ], { base: '.' })
  .pipe(zip('release.zip'))
  .pipe(gulp.dest('./'));
});

gulp.task('release', function(callback) {

  log(' ');
  log('========== 7. release ==========');
  log(' ');

  runSequence(
    'release_delete_pre',
    'release_copy',
    'release_zip',
    'release_delete_post',
    callback
  );
});

gulp.task('start', function () {

  log(' ');
  log('========== Start Gulp Tasks for ' + pluginName + ' ==========');
  log(' ');
});

gulp.task('watch', function () {

  log(' ');
  log('========== watch ==========');
  log(' ');

  gulp.watch( scssFiles, ['css'] );
  gulp.watch( jsFiles, ['js'] );
  gulp.watch( phpFiles, ['phplint'] );
});

gulp.task('default', [
    'start',
    'bower',
    'composer',
    'add_dev_dependencies',
    'css',
    'js',
    'phplint', // 5
    'phpdoc', // 6
    'finish'
  ]
);

gulp.task('dev', [
    'start',
    'bower',
    'composer',
    'add_dev_dependencies',
    'css',
    'js',
    'phplint', // 5
    'phpdoc', // 6
    'finish',
    'watch'
  ]
);

gulp.task ('dist', function(callback) {
  runSequence(
    'start',
    'bower', // 1
    'composer', // 2
    'add_dev_dependencies',
    'css', // 3
    'js', // 4
    'phplint', // 5
    'phpdoc', // 6
    'remove_dev_dependencies',
    'release', // 7
    'list_files', // 8
    'finish'
  );

  callback();
});
