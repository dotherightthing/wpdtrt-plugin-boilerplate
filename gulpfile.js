/**
 * Gulp Task Runner
 * Compile front-end resources
 *
 * @example usage from parent plugin:
 *    gulp dist
 *
 * @example usage from child plugin:
 *    gulp dist --gulpfile ./vendor/dotherightthing/wpdtrt-plugin/gulpfile.js --cwd ./
 *
 * @package     WPPlugin
 * @since       1.0.0
 * @version     1.2.1
 */

/* global require */

// dependencies

var gulp = require('gulp');
var autoprefixer = require('autoprefixer');
var bower = require('gulp-bower');
var composer = require('gulp-composer');
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

var cssDir = 'css';
var distDir = 'wpdtrt-plugin';
var dummyFile = 'README.md';
var jsFiles = './js/*.js';
var phpFiles = [
  './**/*.php',
  '!node_modules/**/*',
  '!vendor/**/*',
  '!wpdtrt-plugin/**/*' // release folder
];
var scssFiles = './scss/*.scss';

// tasks

gulp.task('bower', function () {

  log(' ');
  log('========== 1. bower ==========');
  log(' ');

  // return stream or promise for run-sequence
  return bower();
});

gulp.task('composer', function () {

  log(' ');
  log('========== 2. composer ==========');
  log(' ');

  // return stream or promise for run-sequence
  return composer();
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

gulp.task('phpdoc_pre', function() {

  log(' ');
  log('========== 6b. phpdoc_pre ==========');
  log(' ');

  // return stream or promise for run-sequence
  // note: src files are not used,
  // this structure is only used
  // to include the preceding log()
  return gulp.src(dummyFile, {read: false})
    .pipe(shell([
      // remove plugin which generates Fatal Error (#12)
      'composer remove tgmpa/tgm-plugin-activation'
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

gulp.task('phpdoc_post', function() {

  log(' ');
  log('========== 6d. phpdoc_post ==========');
  log(' ');

  // return stream or promise for run-sequence
  // note: src files are not used,
  // this structure is only used
  // to include the preceding log()
  return gulp.src(dummyFile, {read: false})
    .pipe(shell([
      // reinstall plugin which generates Fatal Error (#12)
      'composer require tgmpa/tgm-plugin-activation'
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
    'phpdoc_pre',
    'phpdoc_doc',
    'phpdoc_post',
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

gulp.task('release_delete_post', function () {

  log(' ');
  log('========== 7d. release_delete_post ==========');
  log(' ');

  // return stream or promise for run-sequence
  return del([
    cssDir,
    distDir // wpdtrt-plugin
  ]);
});

gulp.task('release_copy', function() {

  log(' ');
  log('========== 7b. release_copy ==========');
  log(' ');

  // return stream or promise for run-sequence
  // https://stackoverflow.com/a/32188928/6850747
  return gulp.src([
    './app/**/*',
    './config/**/*',
    './css/**/*',
    './docs/**/*',
    './js/**/*',
    './languages/**/*',
    './templates/**/*',
    './vendor/**/*',
    './views/**/*',
    './index.php',
    './readme.txt',
    './uninstall.php',
    './wpdtrt-plugin.php'
  ], { base: '.' })
  .pipe(gulp.dest(distDir))
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
  .pipe(gulp.dest('./'))
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
  )
});

gulp.task('start', function () {

  log(' ');
  log('========== Tasks Started ==========');
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
    'composer',
    'bower',
    'css',
    'js',
    'phplint',
    'watch'
  ]
);

gulp.task ('maintenance', function(callback) {
  runSequence(
    'start',
    'bower', // 1
    'composer', // 2
    'css', // 3
    'js', // 4
    'phplint', // 5
    'phpdoc', // 6
    'release', // 7
    'list_files', // 8
    'finish'
  );

  callback();
});

gulp.task ('dist', [
    'maintenance'
  ]
);
