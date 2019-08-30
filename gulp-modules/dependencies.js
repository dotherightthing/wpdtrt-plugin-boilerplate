/**
 * File: gulp-modules/dependencies.js
 *
 * Gulp tasks to download dependencies.
 */

import { dest, series, src } from 'gulp';
import log from 'fancy-log';
import shell from 'gulp-shell';

// Ignore missing declaration files
// @ts-ignore
import download from 'gulp-download';
// @ts-ignore
import ghRateLimit from 'gh-rate-limit';
// @ts-ignore
import unzip from 'gulp-unzip';

// internal modules
import boilerplatePath from './boilerplate-path';
import taskHeader from './task-header';
import { GH_TOKEN, TRAVIS } from './env';

// constants
const dummyFile = './README.md';
const pluginName = process.cwd().split( '/' ).pop();
const pluginNameSafe = pluginName.replace( /-/g, '_' );

/**
 * Group: Helpers
 * _____________________________________
 */

/**
 * Function: getGhToken
 *
 * Get the value of the Github API access token used by Travis.
 *
 * See:
 * - <Default Environment Variables: https://docs.travis-ci.com/user/environment-variables/#Default-Environment-Variables>
 *
 * Returns:
 *   (string) - token
 */
function getGhToken() {
  let token = '';

  if ( typeof TRAVIS !== 'undefined' ) {
    token = ( GH_TOKEN || '' );
  }

  return ( token );
}

/**
 * Group: Tasks
 * _____________________________________
 */

/**
 * Function: composer
 *
 * Install the Composer dependencies listed in composer.json.
 *
 * Returns:
 *   A stream to signal task completion
 */
function composer() {
  taskHeader(
    '1c',
    'Dependencies',
    'Install',
    'Composer (PHP)'
  );

  return src( dummyFile, { read: false } )
    .pipe( shell( [
      'composer install --prefer-dist --no-interaction --no-suggest'
    ] ) );
}

/**
 * Function: github
 *
 * Logs the Github API rate limit, to aid in debugging failed builds.
 *
 * Note:
 * - done() calls the gulp callback, to signal task completion
 */
function github( done ) {
  taskHeader(
    '1b',
    'Dependencies',
    'Install',
    'Check current Github API rate limit for automated installs'
  );

  ghRateLimit( {
    token: getGhToken()
  } ).then( ( status ) => {
    log( 'Github API rate limit:' );
    log( `API calls remaining: ${status.core.remaining}/${status.core.limit}` );
    log( ' ' );
  } );

  done();
}

/**
 * Function: naturalDocs
 *
 * Install documentation dependencies.
 *
 * Note:
 * - Natural Docs can't be installed via Yarn
 *   as the Github release needs to be compiled,
 *   and the download archive on the website
 *   is in .zip rather than .tar format.
 *
 * Returns:
 *   A stream to signal task completion
 */
function naturalDocs() {
  taskHeader(
    '1d',
    'Dependencies',
    'Install',
    'Docs'
  );

  const url = 'https://naturaldocs.org/download/natural_docs/'
    + '2.0.2/Natural_Docs_2.0.2.zip';

  return (
    download( url )
      .pipe( unzip() )
      .pipe( dest( './' ) )
  );
}

/**
 * Function: wpUnit
 *
 * Install WPUnit test suite.
 *
 * See:
 * - <Testing & Debugging: https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/wiki/Testing-&-Debugging#environmental-variables>
 *
 * Returns:
 *   A stream to signal task completion
 */
function wpUnit() {
  taskHeader(
    '1e',
    'Dependencies',
    'Install',
    'WP Unit'
  );

  const dbName = `${pluginNameSafe}_wpunit_${Date.now()}`;
  const wpVersion = 'latest';
  let installerPath = 'bin/';

  if ( !boilerplatePath().length ) {
    installerPath = `${boilerplatePath()}bin/`;
  }

  return src( dummyFile, { read: false } )
    .pipe( shell( [
      `bash ${installerPath}install-wp-tests.sh ${dbName} ${wpVersion}`
    ] ) );
}

/**
 * Function: yarn
 *
 * Install Yarn dependencies.
 *
 * Returns:
 *   A stream to signal task completion
 */
function yarn() {
  taskHeader(
    '1a',
    'Dependencies',
    'Install',
    'Yarn'
  );

  return src( dummyFile, { read: false } )
    .pipe( shell( [
      'yarn install --non-interactive'
    ] ) );
}

const dependenciesDev = series(
  yarn,
  composer,
  naturalDocs,
  wpUnit
);

const dependenciesTravis = series(
  yarn,
  github,
  naturalDocs,
  wpUnit
);

export default ( TRAVIS ? dependenciesTravis : dependenciesDev );
