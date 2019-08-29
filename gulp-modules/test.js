/**
 * File: gulp-modules/test.js
 *
 * Gulp tasks to run unit tests.
 */

import { series, src } from 'gulp';
import shell from 'gulp-shell';

// internal modules
import boilerplatePath from './boilerplate-path';
import taskHeader from './task-header';

// constants
const dummyFile = './README.md';

/**
 * Group: Tasks
 * _____________________________________
 */

/**
 * Function: wpUnit
 *
 * Run WPUnit tests
 *
 * See:
 * - <Trouble running PHPUnit in Travis Build: https://stackoverflow.com/a/42467775/6850747>
 *
 * Returns:
 *   A stream to signal task completion
 */
function wpUnit() {
  taskHeader(
    '6a',
    'QA',
    'Tests',
    'WPUnit'
  );

  return src( dummyFile, { read: false } )
    .pipe( shell( [
      `./vendor/bin/phpunit --configuration ${boilerplatePath()}phpunit.xml.dist`
    ] ) );
}

export default series(
  wpUnit
);
