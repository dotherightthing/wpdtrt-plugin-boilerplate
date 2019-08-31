/**
 * File: gulp-modules/test.js
 *
 * Gulp tasks to run unit tests.
 */

import { series } from 'gulp';

// internal modules
import boilerplatePath from './boilerplate-path';
import exec from './exec';
import taskHeader from './task-header';

/**
 * Group: Tasks
 *
 * Steps:
 * 1. - wpUnit (1/1)
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
 *   A stream - to signal task completion
 */
async function wpUnit() {
  taskHeader(
    '1/1',
    'QA',
    'Tests',
    'WPUnit'
  );

  const { stdout, stderr } = await exec( `./vendor/bin/phpunit --configuration ${boilerplatePath()}phpunit.xml.dist` );
  console.log( stdout );
  console.error( stderr );
}

export default series( wpUnit );
