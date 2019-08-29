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
import { TRAVIS } from './env';

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
 * _____________________________________
 */

/**
 * Function: devWatch
 *
 * Watch for changes to files.
 */
function devWatch() {
  if ( !TRAVIS ) {
    taskHeader(
      '*',
      'Watch',
      'Compile',
      'JS & SCSS'
    );

    watch( sources.scss, series( compile ) );
    watch( sources.js, series( compile ) );
  }
}

export default series( devWatch );
