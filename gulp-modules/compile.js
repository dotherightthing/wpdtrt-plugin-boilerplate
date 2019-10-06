/**
 * File: gulp-modules/compile.js
 *
 * Gulp tasks to compile code.
 */

import { dest, series, src } from 'gulp';
import autoprefixer from 'autoprefixer';
import babel from 'gulp-babel';
import postcss from 'gulp-postcss';
import pxtorem from 'postcss-pxtorem';
import rename from 'gulp-rename';
import sass from 'gulp-sass';

// internal modules
import boilerplatePath from './boilerplate-path';
import taskHeader from './task-header';

// constants
const sources = {
  // note: paths are relative to gulpfile, not this file
  js: [
    './js/frontend.js',
    './js/backend.js',
    `./${boilerplatePath()}js/frontend.js`,
    `./${boilerplatePath()}js/backend.js`
  ],
  scss: './scss/*.scss'
};

const targets = {
  // note: paths are relative to gulpfile, not this file
  css: './css',
  js: './js'
};

/**
 * Group: Tasks
 *
 * Order:
 * 1. - css (1/2)
 * 2. - js (2/2)
 * _____________________________________
 */

/**
 * Function: css
 *
 * Compile CSS.
 *
 * Returns:
 *   A stream - to signal task completion
 */
function css() {
  taskHeader(
    '1/2',
    'Assets',
    'Compile',
    'SCSS -> CSS'
  );

  const processors = [
    autoprefixer( {
      cascade: false
    } ),
    pxtorem( {
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
    } )
  ];

  return src( sources.scss, { allowEmpty: true } )
    .pipe( sass( { outputStyle: 'expanded' } ) )
    .pipe( postcss( processors ) )
    .pipe( dest( targets.css ) );
}

/**
 * Function: js
 *
 * Transpile ES6+ to ES5, so that modern code runs in old browsers.
 *
 * Returns:
 *   A stream - to signal task completion
 */
function js() {
  taskHeader(
    '2/2',
    'Assets',
    'Transpile',
    'ES6+ JS -> ES5 JS'
  );

  return src( sources.js, { allowEmpty: true } )
    .pipe( babel( {
      presets: [ '@babel/env' ]
    } ) )
    .pipe( rename( {
      suffix: '-es5'
    } ) )
    .pipe( dest( targets.js ) );
}

export default series(
  // 1/2
  css,
  // 2/2
  js
);
