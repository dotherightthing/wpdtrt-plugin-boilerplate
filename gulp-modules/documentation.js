/**
 * File: gulp-modules/documentation.js
 *
 * Gulp tasks to generate documentation.
 */

import { series, src } from 'gulp';
import shell from 'gulp-shell';

// internal modules
import taskHeader from './task-header';

// constants
const dummyFile = './README.md';

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
function naturalDocs() {
  taskHeader(
    '5a',
    'Documentation',
    'Documentation',
    'Natural Docs (JS & PHP)'
  );

  // Quotes escape space better than backslash on Travis
  const naturalDocsPath = 'Natural Docs/NaturalDocs.exe';

  // note: src files are not used,
  // this structure is only used
  // to include the preceding log()
  return src( dummyFile, { read: false } )
    .pipe( shell( [
      `mono "${naturalDocsPath}" ./config/naturaldocs`
    ] ) );
}

export default series(
  naturalDocs
);
