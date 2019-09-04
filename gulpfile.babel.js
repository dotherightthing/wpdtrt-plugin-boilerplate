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

/**
 * Load Async/Await polyfill
 *
 * As of Babel 7.4.0,
 * @babel/polyfill has been deprecated
 * in favor of directly including
 * core-js/stable
 * (to polyfill ECMAScript features)
 * and
 * regenerator-runtime/runtime
 * (needed to use transpiled generator functions)
 *
 * See:
 * - <Babel 7 - ReferenceError: regeneratorRuntime is not defined: https://stackoverflow.com/a/53559063>
 * - <Migrating a Gulpfile from Gulp 3.9.1 to 4.0.2: https://gist.github.com/dotherightthing/e0639c0c5102993b86362ebe2a651ccc>
 */
import 'core-js/stable';
import 'regenerator-runtime/runtime';

/**
 * Import gulp methods
 */
import { series } from 'gulp';

/**
 * Import internal task modules
 */
import { TRAVIS } from './gulp-modules/env';
import compile from './gulp-modules/compile';
import dependencies from './gulp-modules/dependencies';
import documentation from './gulp-modules/documentation';
import lint from './gulp-modules/lint';
import release from './gulp-modules/release';
import test from './gulp-modules/test';
import version from './gulp-modules/version';
import watch from './gulp-modules/watch';

/**
 * Define combination build tasks
 */

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
