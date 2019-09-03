/**
 * File: gulpfile.babel.js
 *
 * Functions exported from this file
 * can be run as Gulp tasks.
 *
 * Note:
 * - See package.json for scripts, which can be run with:
 *   --- Text
 *   yarn run scriptname
 *   ---
 *
 * See:
 * - <https://gulpjs.com/docs/en/getting-started/creating-tasks>
 */

// TODO
// this needs better documentation
// but it works for now
import 'core-js/stable';
import 'regenerator-runtime/runtime';

import { series } from 'gulp';

// import internal task modules
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
const buildTravis = series(
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

const buildDev = series(
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

/**
 * Fix #1 for: "Task never defined: lint"
 *
 * Expose the public tasks to gulpfile-loader.js
 *
 * See:
 * - Fix #2 in ./gulpfile-loader.js
 * - <Gulp - Creating tasks: https://gulpjs.com/docs/en/getting-started/creating-tasks>
 */
export {
  buildDev,
  buildTravis,
  compile,
  dependencies,
  documentation,
  lint,
  release,
  test,
  TRAVIS,
  version,
  watch
};
