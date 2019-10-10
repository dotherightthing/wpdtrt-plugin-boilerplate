/**
 * File: gulp-modules/documentation.js
 *
 * Gulp tasks to generate documentation.
 */

import exec from './exec';

// internal modules
import taskHeader from './task-header';
import { TAGGED_RELEASE } from './env';

/**
 * Group: Tasks
 *
 * Order:
 * 1. - naturalDocs (1/1)
 * _____________________________________
 */

/**
 * Function: naturalDocs
 *
 * Generate JS & PHP documentation.
 */
async function naturalDocs() {
  taskHeader(
    '1/1',
    'Documentation',
    'Documentation',
    'Natural Docs (JS & PHP)'
  );

  if ( TAGGED_RELEASE ) {
    // Quotes escape space better than backslash on Travis
    const naturalDocsPath = 'Natural Docs/NaturalDocs.exe';

    const { stdout, stderr } = await exec( `mono "${naturalDocsPath}" ./config/naturaldocs` );
    console.log( stdout );
    console.error( stderr );
  }
}

export default naturalDocs;
