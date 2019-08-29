/**
 * File: gulpfile.js
 *
 * Gulp build tasks.
 *
 * Note:
 * - See package.json for scripts, which can be run with:
 *   --- Text
 *   yarn run scriptname
 *   ---
 */

import { series } from 'gulp';

// internal modules
import compile from './gulp-modules/compile';
import dependencies from './gulp-modules/dependencies';
import documentation from './gulp-modules/documentation';
import lint from './gulp-modules/lint';
import release from './gulp-modules/release';
import tests from './gulp-modules/test';
import version from './gulp-modules/version';
import watch from './gulp-modules/watch';

// export combo tasks
export const build = series(
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
  tests,
  // 7 - TODO only if TRAVIS
  release
);
export { compile as compile };
export { dependencies as dependencies };
export { documentation as documentation };
export { lint as lint };
export { release as release };
export { tests as tests };
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
export default series( build, watch );
