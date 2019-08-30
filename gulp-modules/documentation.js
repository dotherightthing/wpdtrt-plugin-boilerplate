/**
 * File: gulp-modules/documentation.js
 *
 * Gulp tasks to generate documentation.
 */

import exec from './exec';

// internal modules
import taskHeader from './task-header';

/**
 * Group: Tasks
 * _____________________________________
 */

/**
 * Function: naturalDocs
 *
 * Generate JS & PHP documentation.
 *
 * Returns:
 *   A stream to signal task completion
 */
async function naturalDocs() {
  taskHeader(
    '5a',
    'Documentation',
    'Documentation',
    'Natural Docs (JS & PHP)'
  );

  // Quotes escape space better than backslash on Travis
  const naturalDocsPath = 'Natural Docs/NaturalDocs.exe';

  await exec( `mono "${naturalDocsPath}" ./config/naturaldocs` );
}

export default naturalDocs;
