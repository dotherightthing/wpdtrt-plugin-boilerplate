/**
 * File: gulp-modules/lint.js
 *
 * Gulp tasks to lint code.
 */

import { series, src } from 'gulp';
import { xmltojson } from 'gulp-xmltojson';

// Ignore missing declaration files
// @ts-ignore
import eslint from 'gulp-eslint';
// @ts-ignore
import phpcs from 'gulp-phpcs';
// @ts-ignore
import sassLint from 'gulp-sass-lint';
// @ts-ignore
import tap from 'gulp-tap';

// internal modules
import boilerplatePath from './boilerplate-path';
import decorateLog from './decorate-log';
import exec from './exec';
import taskHeader from './task-header';

// constants
let phpCsXmlRule = '';
const sources = {
  // note: paths are relative to gulpfile, not this file
  js: [
    './composer.json',
    './gulpfile.babel.js',
    './gulpfile-loader.js',
    './gulp-modules/*.js',
    './package.json',
    './README.md',
    './cypress/**/*.js',
    './js/frontend.js',
    './js/backend.js',
    `./${boilerplatePath()}js/frontend.js`,
    `./${boilerplatePath()}js/backend.js`
  ],
  php: [
    './**/*.php',
    '!./docs/**/*.php',
    '!./node_modules/**/*.php',
    '!./vendor/**/*.php',
    '!./wp-content/**/*.php'
  ],
  phpCsXml: `./${boilerplatePath()}phpcs.xml`,
  scss: './scss/*.scss'
};

/**
 * Group: Tasks
 *
 * Order:
 * 1. - css (1/5)
 * 2. - js (2/5)
 * 3. - composer (3/5)
 * 4. - phpCsExclusions (4/5)
 * 5. - php (5/5)
 * _____________________________________
 */

/**
 * Function: composer
 *
 * Lint composer.json.
 */
async function composer() {
  taskHeader(
    '3/5',
    'QA',
    'Lint',
    'composer.json'
  );

  const { stdout, stderr } = await exec( 'composer validate' );
  console.log( stdout );
  console.error( stderr );
}

/**
 * Function: css
 *
 * Lint CSS files.
 *
 * Returns:
 *   A stream - to signal task completion
 */
function css() {
  taskHeader(
    '1/5',
    'QA',
    'Lint',
    'CSS'
  );

  return src( sources.scss, { allowEmpty: true } )
    .pipe( sassLint() )
    .pipe( sassLint.format() );
// .pipe(sassLint.failOnError())
}

/**
 * Function: js
 *
 * Lint JavaScript files.
 *
 * Returns:
 *   A stream - to signal task completion
 */
function js() {
  taskHeader(
    '2/5',
    'QA',
    'Lint',
    'JavaScript'
  );

  // return stream or promise for run-sequence
  return src( sources.js, { allowEmpty: true } )
    .pipe( eslint() )
    .pipe( eslint.result( result => {
      const {
        filePath: textstring, messages, warningCount, errorCount
      } = result;
      const { length: messageCount } = messages;

      decorateLog( {
        textstring,
        messageCount,
        warningCount,
        errorCount
      } );
    } ) )
    .pipe( eslint.format() );
  // .pipe(eslint.failAfterError());
}

/**
 * Function: php
 *
 * Lint PHP files.
 *
 * See:
 * - <PHP_CodeSniffer: https://packagist.org/packages/squizlabs/php_codesniffer>
 * - <WordPress Coding Standards for PHP_CodeSniffer: https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards>
 * - <Add PHP linting (PSR-2): https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/89>
 * - <Support for phpcs.xml: https://github.com/JustBlackBird/gulp-phpcs/issues/39>
 *
 * Returns:
 *   A stream - to signal task completion
 */
function php() {
  taskHeader(
    '5/5',
    'QA',
    'Lint',
    'PHP'
  );

  const { ref, exclusions, error } = phpCsXmlRule;

  if ( error ) {
    console.warning( error );
    return src( sources.php );
  }

  return src( sources.php, { allowEmpty: true } )
    // Validate files using PHP Code Sniffer
    .pipe( phpcs( {
      bin: './vendor/bin/phpcs',
      // standard must be included and cannot reference phpcs.xml, which is ignored
      // so we load the exclusions using phpCsExclusions
      standard: ref,
      // minimum severity required to display an error or warning.
      warningSeverity: 0,
      showSniffCode: true,
      // note: only 3 levels of specificity are tolerated by gulp-phpcs
      exclude: exclusions
    } ) )
    // Log all problems that were found
    .pipe( phpcs.reporter( 'log' ) );
}

/**
 * Function: phpCsExclusions
 *
 * Load config from phpcs.xml.
 *
 * See:
 * - <https://github.com/nashwaan/xml-js#convert-xml--js-object--json>
 *
 * Returns:
 *   A stream - to signal task completion
 */
function phpCsExclusions() {
  taskHeader(
    '4/5',
    'QA',
    'Lint',
    'Load PHPCS ruleset'
  );

  let errorMessage = false;

  // load phpcs.xml
  return src( sources.phpCsXml )
    // convert the XML to JSON
    .pipe( xmltojson( {
      compact: true,
      ignoreComment: true
    } ) )
    // tap into the stream
    .pipe( tap( ( file ) => {
      // read the JSON
      const phpCsJson = JSON.parse( file.contents.toString() );

      // drill down to the PHPCS config
      const body = phpCsJson.ruleset;

      // extract the keys
      const { rule } = body;

      // ref is the standard to lint against
      const { _attributes } = rule;
      const { ref } = _attributes;

      // verify in phpcs.xml format
      // console.log( `<rule ref="${ref}">` );

      // exclusions are the sniffs that we want to disable
      const { exclude: exclusions = [] } = rule;

      // generate error message
      if ( ( typeof ref === 'undefined' ) || ( exclusions.length === 0 ) ) {
        errorMessage = 'No PHPCS rule found, skipping linting..';
      }

      // loop over exclusions
      const exclusionsSafe = exclusions.map( exclude => {
        // get attributes from exclude object
        const { _attributes: excludeAttributes } = exclude;

        // get the name of the sniff that we're excluding
        const { name } = excludeAttributes;

        // limit the specificity to 3 levels, to avoid a gulp-phpcs error
        const nameParts = name.split( '.' );
        const namePartsThreeLevels = nameParts.slice( 0, 3, nameParts.length ).join( '.' );

        // verify in phpcs.xml format
        // console.log( `<exclude name="${namePartsThreeLevels}"/>` );

        return namePartsThreeLevels;
      } );

      // populate global
      phpCsXmlRule = { ref, exclusions: exclusionsSafe, error: errorMessage };
    } ) );
}

export default series(
  // 1/5
  css,
  // 2/5
  js,
  // 3/5
  composer,
  // 4/5
  phpCsExclusions,
  // 5/5
  php
);
