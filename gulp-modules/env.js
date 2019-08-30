/**
 * File: gulp-modules/env.js
 *
 * Environment Variables.
 */

/**
 * Group: Constants
 * _____________________________________
 */

/**
 * Constant: GH_TOKEN
 *
 * Github API token (string).
 */
const GH_TOKEN = process.env.GH_TOKEN;

/**
 * Constant: TRAVIS
 *
 * Travis CI flag (boolean).
 *
 * See:
 * - <Default Environment Variables: https://docs.travis-ci.com/user/environment-variables/#Default-Environment-Variables>
 */
const TRAVIS = typeof process.env.TRAVIS !== 'undefined';

export default { GH_TOKEN, TRAVIS };
