/**
 * File: gulp-modules/env.js
 *
 * Environment Variables.
 *
 * See:
 * - <Set up environmental variables: https://github.com/dotherightthing/generator-wpdtrt-plugin-boilerplate/wiki/Set-up-environmental-variables>
 */

/**
 * Group: Constants
 * _____________________________________
 */

/**
 * Constant: CYPRESS_RECORD_KEY
 *
 * Key for recording headless CI tests.
 *
 * Note:
 * - This is in addition to the projectId in cypress.json.
 */
const CYPRESS_RECORD_KEY = process.env.CYPRESS_RECORD_KEY || '';

/**
 * Constant: GH_TOKEN
 *
 * Github API token (string).
 */
const GH_TOKEN = process.env.GH_TOKEN || '';

/**
 * Constant: TRAVIS
 *
 * Travis CI flag (boolean).
 *
 * See:
 * - <Default Environment Variables: https://docs.travis-ci.com/user/environment-variables/#Default-Environment-Variables>
 */
const TRAVIS = ( typeof process.env.TRAVIS !== 'undefined' );

export { CYPRESS_RECORD_KEY, GH_TOKEN, TRAVIS };
