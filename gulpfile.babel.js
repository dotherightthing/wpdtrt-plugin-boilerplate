/**
 * File: gulpfile.babel.js
 *
 * Gulp build tasks.
 *
 * Note:
 * - See package.json for scripts, which can be run with:
 *   --- Text
 *   yarn run scriptname
 *   ---
 */
import 'core-js/stable';
import 'regenerator-runtime/runtime';
import { series } from 'gulp';

// internal modules
import { TRAVIS } from './gulp-modules/env';
import compile from './gulp-modules/compile';
import dependencies from './gulp-modules/dependencies';
import documentation from './gulp-modules/documentation';
import lint from './gulp-modules/lint';
import release from './gulp-modules/release';
import test from './gulp-modules/test';
import version from './gulp-modules/version';
import watch from './gulp-modules/watch';

// export combo tasks
export const buildTravis = series(
  // 1
  dependencies,
  // 2
  lint,
  // 3
  compile,
  // 4
  version,
  // 5
  documentation,
  // 6
  test,
  // 7
  release
);

export const buildDev = series(
  // 1
  dependencies,
  // 2
  lint,
  // 3
  compile,
  // 4
  version,
  // 5
  documentation,
  // 6
  test,
  // 8
  watch
);

export { compile as compile };
export { dependencies as dependencies };
export { documentation as documentation };
export { lint as lint };
export { release as release };
export { test as test };
export { version as version };
export { watch as watch };

/*
 * Export the default task
 *
 * Example:
 * --- bash
 * gulp
 * ---
 */

export default ( TRAVIS ? buildTravis : buildDev );
