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
 * Order:
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
async function cypressIo() {
  taskHeader(
    '1/2',
    'QA',
    'Tests',
    'Cypress'
  );

  // only child plugins have tests
  // child plugins run off the boilerplatePath
  if ( boilerplatePath().length ) {
    const { stdout, stderr } = await exec( './node_modules/bin/cypress run --record --config ./cypress.json' );
    console.log( stdout );
    console.error( stderr );
  }
}

async function wpUnit() {
  taskHeader(
    '2/2',
    'QA',
    'Tests',
    'WPUnit'
  );

  const { stdout, stderr } = await exec( `./vendor/bin/phpunit --configuration ${boilerplatePath()}phpunit.xml.dist` );
  console.log( stdout );
  console.error( stderr );
}

export default series(
  // 1/2
  cypressIo,
  // 2/2
  wpUnit
);
