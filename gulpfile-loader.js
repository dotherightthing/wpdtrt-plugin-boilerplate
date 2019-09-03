/**
 * File: gulpfile-loader.js
 *
 * Use ES5 to load transpiler preset.
 * ES6 can then be used in the required module.
 * 
 * See:
 * - <https://stackoverflow.com/a/53904273/6850747>
 */

require( '@babel/register' ) ( {
  presets: [
    [
      '@babel/preset-env',
      {
        targets: {
          node: 'current'
        }
      }
    ]
  ]
} );

/**
 * Fix #2 for "Task never defined: lint"
 * 
 * Expose the public tasks to gulp-cli.
 * 
 * See:
 * - Fix #1 in ./gulpfile.babel.js
 * - <Gulp - Creating tasks: https://gulpjs.com/docs/en/getting-started/creating-tasks>
 */
const {
  buildDev,
  buildTravis,
  compile,
  dependencies,
  documentation,
  lint,
  release,
  test,
  TRAVIS,
  version,
  watch
} = require( './gulpfile.babel.js' );

module.exports = {
  dependencies,
  compile,
  documentation,
  lint,
  release,
  test,
  version,
  watch
};

/**
 * Export the default task
 *
 * Example:
 * --- bash
 * gulp
 * ---
 */
module.exports.default = ( TRAVIS ? buildTravis : buildDev );
