/**
 * File: gulp-modules/release.js
 *
 * Gulp tasks to zip a release.
 *
 * See:
 * - <Globtester: http://www.globtester.com/>
 */

import { dest, series, src } from 'gulp';
import del from 'del';
import print from 'gulp-print';
import zip from 'gulp-zip';

// internal modules
import exec from './exec';
import taskHeader from './task-header';

// constants
const pluginName = process.cwd().split( '/' ).pop();
const sources = {
  // note: paths are relative to gulpfile, not this file
  release: [
    // Composer file, contains TGMPA dependencies object
    './composer.json',
    // Compiled CSS
    './css/**/*',
    // Cypress.io
    './cypress.json',
    './cypress/**/*',
    // Any icons
    './icons/**/*',
    // Any images
    './images/**/*',
    // Not the project logo
    '!./images/**/*.pxm',
    // Transpiled ES5 JS incl backend-es5.js from boilerplate
    './js/**/*-es5.js',
    // WP i18n .pot files
    './languages/**/*',
    // Yarn front-end dependencies
    './node_modules/**/*',
    // Yarn environment symlink
    '!./node_modules/wpdtrt-plugin-boilerplate',
    // Yarn environment symlink contents
    '!./node_modules/wpdtrt-plugin-boilerplate/**/*',
    // Plugin logic
    './src/**/*',
    // Plugin template partials
    './template-parts/**/*',
    // Any PHP dependencies
    './vendor/**/*',
    // Not binary executables
    '!./vendor/bin/**/*',
    // Not JSON files
    '!./node_modules/**/*.json',
    '!./vendor/**/*.json',
    // Not Less files
    '!./node_modules/**/*.less',
    '!./vendor/**/*.less',
    // Not License files
    '!./node_modules/**/LICENSE',
    '!./vendor/**/LICENSE',
    // Not Markdown files
    '!./node_modules/**/*.md',
    '!./vendor/**/*.md',
    // Not PHP sample files
    '!./node_modules/**/*example*.php',
    '!./vendor/**/*example*.php',
    // Not Sass files
    '!./node_modules/**/*.scss',
    '!./vendor/**/*.scss',
    // Not SCSS folders
    '!./node_modules/**/*/scss',
    '!./vendor/**/*/scss',
    // Not test files
    '!./node_modules/**/test/**/*',
    '!./vendor/**/test/**/*',
    // Not tests files
    '!./node_modules/**/tests/**/*',
    '!./vendor/**/tests/**/*',
    // Not XML files
    '!./node_modules/**/*.xml',
    '!./vendor/**/*.xml',
    // Not Zip files
    '!./node_modules/**/*.zip',
    '!./vendor/**/*.zip',
    // Plugin WP Read Me
    './readme.txt',
    // Plugin WP Uninstaller
    './uninstall.php',
    // Plugin root config
    `./${pluginName}.php`,
    // Not CSS source maps
    '!./css/maps/**/*',
    // Not demo files
    '!./icons/icomoon/demo-files/**/*',
    // Not docs
    '!./docs/**/*'
  ]
};
const targets = {
  zipSource: pluginName
};

/**
 * Group: Tasks
 * _____________________________________
 *
 * Order:
 * 1. - composer (1/5)
 * 2. - yarn (2/5)
 * 3. - copy (3/5)
 * 4. - zipFiles (4/5)
 * 5. - cleanUp (5/5)
 */

/**
 * Function: cleanUp
 *
 * Clean up temporary files.
 *
 * Returns:
 *   A stream - to signal task completion
 */
function cleanUp() {
  taskHeader(
    '5/5',
    'Release',
    'Clean up'
  );

  return del( [ `./${targets.zipSource}` ] );
}

/**
 * Function: composer
 *
 * Uninstall PHP development dependencies.
 *
 * See:
 * - <Reduce vendor components required for deployment: https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/47>
 */
async function composer() {
  taskHeader(
    '1/5',
    'Release',
    'Uninstall dev dependencies',
    'Composer (PHP)'
  );

  const { stdout, stderr } = await exec( 'composer install --prefer-dist --no-interaction --no-dev --no-suggest' );
  console.log( stdout );
  console.error( stderr );
}

/**
 * Function: copy
 *
 * Copy release files to a temporary folder.
 *
 * Returns:
 *   A stream - to signal task completion
 */
function copy() {
  taskHeader(
    '3/5',
    'Release',
    'Copy files',
    'To temporary folder'
  );

  // return stream or promise for run-sequence
  // https://stackoverflow.com/a/32188928/6850747
  return src( sources.release, { allowEmpty: true, base: '.' } )
    .pipe( print() )
    .pipe( dest( targets.zipSource ) );
}

/**
 * Function: yarn
 *
 * Uninstall Yarn development dependencies.
 *
 * Returns:
 *   A stream - to signal task completion
 */
async function yarn() {
  taskHeader(
    '2/5',
    'Release',
    'Uninstall dev dependencies',
    'Yarn'
  );

  const { stdout, stderr } = await exec( 'yarn install --non-interactive --production' );
  console.log( stdout );
  console.error( stderr );
}

/**
 * Function: zipFiles
 *
 * Generate release.zip for deployment by Travis/Github.
 *
 * Returns:
 *   A stream - to signal task completion
 */
function zipFiles() {
  taskHeader(
    '4/5',
    'Release',
    'Generate',
    'Zip file'
  );

  return src( [ `./${targets.zipSource}/**/*` ], { base: '.' } )
    .pipe( zip( 'release.zip' ) )
    .pipe( dest( './' ) );
}

export default series(
  // 1/5
  composer,
  // 2/5
  yarn,
  // 3/5
  copy,
  // 4/5
  zipFiles,
  // 5/5
  cleanUp
);
