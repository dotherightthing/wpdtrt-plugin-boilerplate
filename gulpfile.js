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
 * @version     1.1.9
 */

/* global require */

// dependencies

var gulp = require('gulp');
var autoprefixer = require('autoprefixer');
var bower = require('gulp-bower');
var composer = require('gulp-composer');
var del = require('del');
var jshint = require('gulp-jshint');
var phplint = require('gulp-phplint');
var postcss = require('gulp-postcss');
var pxtorem = require('postcss-pxtorem');
var runSequence = require('run-sequence');
var sass = require('gulp-sass');
var shell = require('gulp-shell');
var zip = require('gulp-zip');

// paths

var cssDir = 'css';
var distDir = 'wpdtrt-plugin';
var jsFiles = './js/*.js';
var phpFiles = [
  './**/*.php',
  '!node_modules/**/*',
  '!vendor/**/*'
];
var scssFiles = './scss/*.scss';

// tasks

gulp.task('bower', function () {
  return bower();
});

gulp.task('composer', function () {
  composer();
});

gulp.task('css', function () {

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

  return gulp
    .src(scssFiles)
    .pipe(sass({outputStyle: 'expanded'}))
    .pipe(postcss(processors))
    .pipe(gulp.dest(cssDir));
});

gulp.task('js', function() {
  return gulp
    .src(jsFiles)

    // validate JS
    .pipe(jshint())
    .pipe(jshint.reporter('default', { verbose: true }))
    .pipe(jshint.reporter('fail'));
});

gulp.task('phpdoc_delete', function () {
  return del([
    'docs/phpdoc/**/*'
  ]);
});

gulp.task('phpdoc_pre', shell.task([
  // remove plugin which generates Fatal Error (#12)
  'composer remove tgmpa/tgm-plugin-activation'
]));

gulp.task('phpdoc_doc', shell.task([
  /**
   * Generate PHP Documentation
   *
   * @example
   *  -d = the directory, or directories, of your project that you want to document.
   *  -f = a specific file, or files, in your project that you want to document.
   *  -t = the location where your documentation will be written (also called ‘target folder’).
   *  --ignore
   * @see https://docs.phpdoc.org/guides/running-phpdocumentor.html#quickstart
   * @see https://github.com/dotherightthing/wpdtrt-plugin/issues/12
   */
  'vendor/bin/phpdoc -d . -t ./docs/phpdoc',

  // view the generated documentation
  //'open docs/phpdoc/index.html'
]));

gulp.task('phpdoc_post', shell.task([
  // reinstall plugin which generates Fatal Error (#12)
  'composer require tgmpa/tgm-plugin-activation'
]));

gulp.task('phpdoc', function(callback) {
  runSequence(
    'phpdoc_delete',
    'phpdoc_pre',
    'phpdoc_doc',
    'phpdoc_post'
  )
});

gulp.task('phplint', function () {
  return gulp
    .src(phpFiles)

    // validate PHP
    // The linter ships with PHP
    .pipe(phplint())
    .pipe(phplint.reporter(function(file){
      var report = file.phplintReport || {};

      if (report.error) {
        console.log(report.message+' on line '+report.line+' of '+report.filename);
      }
    }));
});

gulp.task('release_delete', function () {
  return del([
    cssDir,
    distDir,
    'release.zip'
  ]);
});

gulp.task('release_copy', function() {
  // Return the event stream to gulp.task to inform the task of when the stream ends
  // https://stackoverflow.com/a/32188928/6850747
  return gulp.src([
    './app/**/*',
    './config/**/*',
    './css/**/*',
    './js/**/*',
    './languages/**/*',
    './templates/**/*',
    './vendor/**/*',
    './index.php',
    './readme.txt',
    './uninstall.php',
    './wpdtrt-plugin.php'
  ], { base: '.' })
  .pipe(gulp.dest(distDir))
});

gulp.task('release_zip', function() {
  // Return the event stream to gulp.task to inform the task of when the stream ends
  // https://stackoverflow.com/a/32188928/6850747
  return gulp.src([
    './' + distDir + '/**/*'
  ], { base: '.' })
  .pipe(zip('release.zip'))
  .pipe(gulp.dest('./'))
});

gulp.task('release', function(callback) {
  runSequence(
    'release_delete',
    'release_copy',
    'release_zip'
  )
});

gulp.task('watch', function () {
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
    ['composer', 'bower'],
    ['css', 'js'],
    'phplint',
    'phpdoc',
    'release'
  )
});

gulp.task ('dist', [
    'maintenance'
  ]
);
