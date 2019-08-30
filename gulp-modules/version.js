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
 * _____________________________________
 */

/**
 * Function: autoloadUpdatedDependencies
 *
 * Regenerate the list of PHP classes to be autoloaded.
 *
 * Returns:
 *   A stream to signal task completion
 */
async function autoloadUpdatedDependencies() {
  taskHeader(
    '4c',
    'Version',
    'Generate',
    'List of classes to be autoloaded'
  );

  // regenerate autoload files
  await exec( 'composer dump-autoload --no-interaction' );
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
    '4b',
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
 *   A stream to signal task completion
 */
async function updateDependencies() {
  taskHeader(
    '4a',
    'Version',
    'Bump',
    'Update Composer dependencies'
  );

  if ( boilerplatePath().length ) {
    return true;
  }

  await exec( 'composer update dotherightthing/wpdtrt-plugin-boilerplate --no-interaction --no-suggest' );
}

const versionDev = series(
  updateDependencies,
  replaceVersions,
  autoloadUpdatedDependencies
);

const versionTravis = series(
  replaceVersions
);

export default ( TRAVIS ? versionTravis : versionDev );
