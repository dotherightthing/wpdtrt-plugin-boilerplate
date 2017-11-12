/**
 * Gulp Task Runner
 * Compile front-end resources
 *
 * @example usage from child plugin:
 *    gulp --gulpfile ./vendor/dotherightthing/wpdtrt-plugin/gulpfile.js --cwd ./
 *
 * @package     WPPlugin
 * @since       1.0.0
 */

/* global require */

// dependencies
var gulp = require('gulp');
var autoprefixer = require('autoprefixer');
var composer = require('gulp-composer');
var phplint = require('gulp-phplint');
var postcss = require('gulp-postcss');
var pxtorem = require('postcss-pxtorem');
var sass = require('gulp-sass');
var shell = require('gulp-shell');

var scssDir = './scss/*.scss';
var cssDir = './css/';
var phpDir = [
  './**/*.php',
  '!vendor/**/*',
  '!node_modules/**/*'
];

// tasks

gulp.task('composer', function () {
  composer();
});

gulp.task('scss', function () {

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
    .src(scssDir)
    .pipe(sass({outputStyle: 'expanded'}))
    .pipe(postcss(processors))
    .pipe(gulp.dest(cssDir));
});

gulp.task('phplint', function () {
  return gulp
    .src(phpDir)

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

gulp.task('phpdoc', shell.task([
  /**
   * Generate PHP Documentation
   *
   * @example
   *  -d = the directory, or directories, of your project that you want to document.
   *  -f = a specific file, or files, in your project that you want to document.
   *  -t = the location where your documentation will be written (also called ‘target folder’).
   *  --ignore
   * @see https://docs.phpdoc.org/guides/running-phpdocumentor.html#quickstart
   * @todo https://github.com/dotherightthing/wpdtrt-plugin/issues/12
   */
  'vendor/bin/phpdoc -d . -t ./docs/phpdoc --ignore ./vendor/,./node_modules/'
]));

gulp.task('watch', function () {
  gulp.watch( './composer.json', ['composer'] );
  gulp.watch( phpDir, ['phplint'] );
  gulp.watch( scssDir, ['scss'] );
});

// run from child plugin folders
// gulp --gulpfile ./vendor/dotherightthing/wpdtrt-plugin/gulpfile.js --cwd ./
gulp.task( 'default', [
    'composer',
    'phplint',
    'scss',
    'watch'
  ]
);

// run from the parent plugin folder
// gulp maintenance
gulp.task ('maintenance', [
    'composer',
    'phplint',
    'scss',
    // 'phpdoc' // also fails here as DTRT Plugin also loads TGMPA
  ]
);
