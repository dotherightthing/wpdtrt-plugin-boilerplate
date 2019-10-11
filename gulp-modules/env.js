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

/**
 * Constant: TAGGED_RELEASE
 *
 * Checks whether we are deploying a release from the master branch.
 *
 * Note:
 * - if the current build is for a git tag, this variable is set to the tag’s name.
 *
 * See:
 * - <Default Environment Variables: https://docs.travis-ci.com/user/environment-variables/#Default-Environment-Variables>
 */
const TAGGED_RELEASE = process.env.TRAVIS_TAG || false;

export {
  GH_TOKEN,
  TRAVIS,
  TAGGED_RELEASE
};
