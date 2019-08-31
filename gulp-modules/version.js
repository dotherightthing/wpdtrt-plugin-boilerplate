/**
 * File: gulp-modules/version.js
 *
 * Gulp tasks to version files prior to a release.
 */

import { series } from 'gulp';
import wpdtrtPluginBump from 'gulp-wpdtrt-plugin-bump';

// internal modules
import boilerplatePath from './boilerplate-path';
import exec from './exec';
import taskHeader from './task-header';
import { TRAVIS } from './env';

/**
 * Group: Tasks
 *
 * Order:
 * 1. - updateDependencies (1/3) - Dev only
 * 2. - replaceVersions (2/3)
 * 3. - autoloadUpdatedDependencies (3/3) - Dev only
 * _____________________________________
 */

/**
 * Function: autoloadUpdatedDependencies
 *
 * Regenerate the list of PHP classes to be autoloaded.
 *
 * Returns:
 *   A stream - to signal task completion
 */
async function autoloadUpdatedDependencies() {
  taskHeader(
    '3/3',
    'Version',
    'Generate',
    'List of classes to be autoloaded'
  );

  const { stdout, stderr } = await exec( 'composer dump-autoload --no-interaction' );
  console.log( stdout );
  console.error( stderr );
}

/**
 * Function: replaceVersions
 *
 * Replace version strings, using the version set in package.json.
 *
 * Returns:
 *   call to wpdtrtPluginBump (gulp-wpdtrt-plugin-bump)
 */
function replaceVersions( done ) {
  taskHeader(
    '2/3',
    'Version',
    'Bump',
    'Replace version strings'
  );

  wpdtrtPluginBump( {
    inputPathRoot: './',
    inputPathBoilerplate: `./${boilerplatePath()}`
  } );

  done();
}

/**
 * Function: updateDependencies
 *
 * Update the boilerplate dependency to the updateDependencies version.
 *
 * Note:
 * - If wpdtrt-plugin-boilerplate is loaded as a dependency,
 *   get the updateDependencies release of wpdtrt-plugin-boilerplate.
 * - This has to run before replaceVersions,
 *   so that the correct version information is available
 *
 * Returns:
 *   A stream - to signal task completion
 */
async function updateDependencies() {
  taskHeader(
    '1/3',
    'Version',
    'Bump',
    'Update Composer dependencies'
  );

  if ( boilerplatePath().length ) {
    const { stdout, stderr } = await exec( 'composer update dotherightthing/wpdtrt-plugin-boilerplate --no-interaction --no-suggest' );
    console.log( stdout );
    console.error( stderr );
  }
}

const versionDev = series(
  // 1/3
  updateDependencies,
  // 2/3
  replaceVersions,
  // 3/3
  autoloadUpdatedDependencies
);

const versionTravis = series(
  // 2/3
  replaceVersions
);

export default ( TRAVIS ? versionTravis : versionDev );
