/**
 * File: gulp-modules/decorate-log.js
 *
 * Functions relating to styling logged messages.
 */

// Ignore missing declaration files
// @ts-ignore
import color from 'gulp-color';

/**
 * Group: Helpers
 * _____________________________________
 */

/**
 * Function: decorateLog
 *
 * Log a Gulp task result with emoji and colour.
 *
 * Parameters:
 *   (object) filePath, messageCount, warningCount, errorCount
 */
function decorateLog( {
  textstring = '',
  messageCount = 0,
  warningCount = 0,
  errorCount = 0
} = {} ) {
  const colors = {
    pass: 'GREEN', message: 'WHITE', warning: 'YELLOW', error: 'RED'
  };
  const emojis = {
    pass: '✔', message: '✖', warning: '✖', error: '✖'
  };
  let state;

  if ( errorCount > 0 ) {
    state = 'error';
  } else if ( warningCount > 0 ) {
    state = 'warning';
  } else if ( messageCount > 0 ) {
    state = 'message';
  } else {
    state = 'pass';
  }

  console.log( color( `${emojis[ state ]} ${textstring}`, `${colors[ state ]}` ) );
}

export default decorateLog;
