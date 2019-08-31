/**
 * File: gulp-modules/watch.js
 *
 * Gulp tasks to watch files for changes.
 */

import { series, watch } from 'gulp';

// internal modules
import boilerplatePath from './boilerplate-path';
import compile from './compile';
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

/**
 * Group: Tasks
 *
 * Order:
 * 1. - devWatch (1/1)
 * _____________________________________
 */

/**
 * Function: devWatch
 *
 * Watch for changes to files.
 */
function devWatch() {
  taskHeader(
    '1/1',
    'Watch',
    'Compile',
    'JS & SCSS'
  );

  watch( sources.scss, series( compile ) );
  watch( sources.js, series( compile ) );
}

export default series( devWatch );
