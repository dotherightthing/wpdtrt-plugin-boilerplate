/**
 * File: gulp-modules/boilerplate-path.js
 *
 * Gets the path to wpdtrt-plugin-boilerplate.
 */

/**
 * Group: Helpers
 * _____________________________________
 */

/**
 * Function: getPluginName
 *
 * Get the pluginName from the directory path.
 *
 * Returns:
 *   (string) pluginName
 */
function getPluginName() {
  // pop() - remove the last element from the path array and return it
  const pluginName = process.cwd().split( '/' ).pop();

  return pluginName;
}

/**
 * Function: boilerplateIs
 *
 * Determines whether we're in the boilerplate, or using it as a dependency.
 *
 * Returns:
 *  (boolean) - True if we're in the boilerplate
 */
function boilerplateIs() {
  const pluginName = getPluginName();

  return ( pluginName === 'wpdtrt-plugin-boilerplate' );
}

/**
 * Function: boilerplatePath
 *
 * Get the path to the boilerplate.
 *
 * Returns:
 *   (string) path
 *
 * Example:
 * --- js
 * import boilerplatePath from './boilerplate-path';
 * phpCsXml: `./${boilerplatePath()}phpcs.xml`;
 * ---
 */
function boilerplatePath() {
  let path = '';

  if ( !boilerplateIs() ) {
    path = 'vendor/dotherightthing/wpdtrt-plugin-boilerplate/';
  }

  return path;
}

export default boilerplatePath;
