/**
 * File: gulpfile.js
 * Topic: DTRT WordPress Plugin Boilerplate
 *
 * Gulp build tasks.
 *
 * See package.json for scripts, which can be run with:
 * --- Text
 * yarn run scriptname
 * ---
 */

const gulp = require( 'gulp' );
const autoprefixer = require( 'autoprefixer' );
const babel = require( 'gulp-babel' );
const color = require( 'gulp-color' );
const download = require( 'gulp-download' );
const ghRateLimit = require( 'gh-rate-limit' );
const eslint = require( 'gulp-eslint' );
const log = require( 'fancy-log' );
const phpcs = require( 'gulp-phpcs' );
const postcss = require( 'gulp-postcss' );
const print = require( 'gulp-print' ).default;
const pxtorem = require( 'postcss-pxtorem' );
const rename = require( 'gulp-rename' );
const runSequence = require( 'run-sequence' );
const sass = require( 'gulp-sass' );
const sassLint = require( 'gulp-sass-lint' );
const shell = require( 'gulp-shell' );
const unzip = require( 'gulp-unzip' );
const wpdtrtPluginBump = require( 'gulp-wpdtrt-plugin-bump' );
const zip = require( 'gulp-zip' );

/**
 * Function: getPluginName
 *
 * Get the pluginName from package.json.
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
 * Function: isBoilerplate
 *
 * Determines whether we're in the boilerplate, or using it as a dependency.
 *
 * Returns:
 *  (boolean) - True if we're in the boilerplate
 */
function isBoilerplate() {
  const pluginName = getPluginName();

  return ( pluginName === 'wpdtrt-plugin-boilerplate' );
}

/**
 * Function: isTravis
 *
 * Determines whether the current Gulp process is running on Travis CI.
 *
 * See: <Default Environment Variables: https://docs.travis-ci.com/user/environment-variables/#Default-Environment-Variables>.
 *
 * Returns:
 *   (boolean)
 */
function isTravis() {
  return ( typeof process.env.TRAVIS !== 'undefined' );
}

/**
 * Function: getGhToken
 *
 * Get the value of the Github API access token used by Travis.
 *
 * Returns:
 *   (string)
 */
function getGhToken() {
  let token = '';

  if ( isTravis() ) {
    token = ( process.env.GH_TOKEN ? process.env.GH_TOKEN : '' );
  }

  return ( token );
}

/**
 * Function: getBoilerplatePath
 *
 * Get the path to the boilerplate.
 *
 * Returns:
 *   (string) path
 */
function getBoilerplatePath() {
  let path = '';

  if ( !isBoilerplate() ) {
    path = 'vendor/dotherightthing/wpdtrt-plugin-boilerplate/';
  }

  return path;
}

/**
 * Function: getJsFiles
 *
 * Get list of JavaScript files to transpile from ES6 to ES5.
 *
 * See: <http://usejsdoc.org/about-including-package.html>.
 *
 * Returns:
 *   (array) jsFiles - Files
 */
function getJsFiles() {
  let boilerplatePath = getBoilerplatePath();

  if ( boilerplatePath !== '' ) {
    boilerplatePath += '/';
  }

  // note: es6 orignals only
  const jsFiles = [
    './js/frontend.js',
    './js/backend.js',
    `${boilerplatePath}js/frontend.js`,
    `${boilerplatePath}js/backend.js`
  ];

  return jsFiles;
}

/**
 * Function: getJsFilesToLint
 *
 * Get list of JavaScript files to lint.
 *
 * See: <http://usejsdoc.org/about-including-package.html>.
 *
 * Returns:
 *   (array) jsFiles - Files
 */
function getJsFilesToLint() {
  let boilerplatePath = getBoilerplatePath();

  if ( boilerplatePath !== '' ) {
    boilerplatePath += '/';
  }

  // note: es6 orignals only
  const jsFilesToLint = [
    './composer.json',
    './gulpfile.js',
    './package.json',
    './README.md',
    './cypress/**/*.js',
    './js/frontend.js',
    './js/backend.js',
    `${boilerplatePath}js/frontend.js`,
    `${boilerplatePath}js/backend.js`
  ];

  return jsFilesToLint;
}

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

/**
 * Function: taskHeader
 *
 * Displays a block comment for each task that runs.
 *
 * Parameters:
 *   (string) step - Step number
 *   (string) taskCategory - Task category
 *   (string) taskAction - Task action
 *   (string) taskDetail - Task detail
 *
 * Returns:
 *   (string) Task header
 */
function taskHeader(
  step = '0',
  taskCategory = '',
  taskAction = '',
  taskDetail = ''
) {
  log( ' ' );
  log( '========================================' );
  log( `${step} - ${taskCategory}:` );
  log( `=> ${taskAction}: ${taskDetail}` );
  log( '----------------------------------------' );
  log( ' ' );
}

const pluginName = getPluginName();
const pluginNameSafe = pluginName.replace( /-/g, '_' );
const cssDir = 'css';
const jsDir = 'js';
const distDir = pluginName;
const dummyFile = 'README.md';
const jsFiles = getJsFiles();
const phpFiles = [
  '**/*.php',
  '!docs/**/*.php',
  '!node_modules/**/*.php',
  '!vendor/**/*.php',
  '!wp-content/**/*.php'
];
const scssFiles = './scss/*.scss';

/**
 * Namespace: gulp
 *
 * Gulp tasks.
 */

/**
 * About: runSequenceCallback
 *
 * Tells runSequence that a task has finished..
 *
 * By returning a stream,
 * the task system is able to plan the execution of those streams.
 * But sometimes, especially when you're in callback hell
 * or calling some streamless plugin,
 * you aren't able to return a stream.
 * That's what the callback is for.
 * To let the task system know that you're finished
 * and to move on to the next call in the execution chain.
 *
 * See <Where is the gulp task callback function defined?: https://stackoverflow.com/a/29299107/6850747>
 */

/**
 * Method: dependencies
 *
 * Tasks which install dependencies.
 *
 * Parameters:
 *   callback - The runSequenceCallback that handles the response
 */
gulp.task( 'dependencies', ( callback ) => {
  taskHeader(
    '1',
    'Dependencies',
    'Install',
    ''
  );

  runSequence(
    'dependenciesYarn',
    'dependenciesGithub',
    'dependenciesComposer',
    'dependenciesDocs',
    'dependenciesWpUnit',
    callback
  );
} );

/**
 * Method: dependenciesYarn
 *
 * Install Yarn dependencies.
 *
 * Returns:
 *   Stream or promise for run-sequence.
 */
gulp.task( 'dependenciesYarn', () => {
  taskHeader(
    '1a',
    'Dependencies',
    'Install',
    'Yarn'
  );

  // return stream or promise for run-sequence
  return gulp.src( dummyFile, { read: false } )
    .pipe( shell( [
      'yarn install --non-interactive'
    ] ) );
} );

/**
 * Method: dependenciesGithub
 *
 * Expose the Github API rate limit to aid in debugging failed builds.
 *
 * Returns:
 *   (object) Rate Limit
 */
gulp.task( 'dependenciesGithub', () => {
  taskHeader(
    '1b',
    'Dependencies',
    'Install',
    'Check current Github API rate limit for automated installs'
  );

  if ( !isTravis() ) {
    return true;
  }

  const token = getGhToken();

  if ( token === '' ) {
    return true;
  }

  return ghRateLimit( {
    token: getGhToken()
  } ).then( ( status ) => {
    log( 'Github API rate limit:' );
    log( `API calls remaining: ${status.core.remaining}/${status.core.limit}` );
    log( ' ' );
  } );
} );

/**
 * Method: dependenciesComposer
 *
 * Install the Composer dependencies listed in composer.json.
 *
 * Returns:
 *   Stream or promise for run-sequence.
 */
gulp.task( 'dependenciesComposer', () => {
  // Travis already runs composer install
  if ( isTravis() ) {
    return true;
  }

  taskHeader(
    '1c',
    'Dependencies',
    'Install',
    'Composer (PHP)'
  );

  // return stream or promise for run-sequence
  return gulp.src( dummyFile, { read: false } )
    .pipe( shell( [
      'composer install --prefer-dist --no-interaction --no-suggest'
    ] ) );
} );

/**
 * Function: dependenciesDocs
 *
 * Install documentation dependencies.
 *
 * Natural Docs can't be installed via Yarn
 * as the Github release needs to be compiled,
 * and the download archive on the website
 * is in .zip rather than .tar format.
 *
 * Returns:
 *   Stream or promise for run-sequence.
 */
gulp.task( 'dependenciesDocs', () => {
  taskHeader(
    '1d',
    'Dependencies',
    'Install',
    'Docs'
  );

  const url = 'https://naturaldocs.org/download/natural_docs/'
    + '2.0.2/Natural_Docs_2.0.2.zip';

  // return stream or promise for run-sequence
  return download( url )
    .pipe( unzip() )
    .pipe( gulp.dest( './' ) );
} );

/**
 * Method: dependenciesWpUnit
 *
 * Install WPUnit test suite.
 *
 * See: <Testing & Debugging: https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/wiki/Testing-&-Debugging#environmental-variables>
 *
 * Returns:
 *   Stream or promise for run-sequence.
 */
gulp.task( 'dependenciesWpUnit', () => {
  taskHeader(
    '1e',
    'Dependencies',
    'Install',
    'WP Unit'
  );

  const boilerplate = isBoilerplate();
  const boilerplatePath = getBoilerplatePath();
  const dbName = `${pluginNameSafe}_wpunit_${Date.now()}`;
  const wpVersion = 'latest';
  let installerPath = 'bin/';

  if ( !boilerplate ) {
    installerPath = `${boilerplatePath}bin/`;
  }

  return gulp.src( dummyFile, { read: false } )
    .pipe( shell( [
      `bash ${installerPath}install-wp-tests.sh ${dbName} ${wpVersion}`
    ] ) );
} );

/**
 * Method: lint
 *
 * Tasks which lint files.
 *
 * Parameters:
 *   callback - The runSequenceCallback that handles the response
 */
gulp.task( 'lint', ( callback ) => {
  taskHeader(
    '2',
    'QA',
    'Lint',
    ''
  );

  runSequence(
    'lintCss',
    'lintJs',
    'lintComposer',
    'lintPhp',
    callback
  );
} );

/**
 * Method: lintCss
 *
 * Lint CSS files.
 *
 * Returns:
 *   Stream or promise for run-sequence.
 */
gulp.task( 'lintCss', () => {
  taskHeader(
    '2a',
    'QA',
    'Lint',
    'CSS'
  );

  return gulp.src( scssFiles )
    .pipe( sassLint() )
    .pipe( sassLint.format() );
  // .pipe(sassLint.failOnError())
} );

/**
 * Method: lintJs
 *
 * Lint JavaScript files.
 *
 * Returns:
 *   Stream or promise for run-sequence.
 */
gulp.task( 'lintJs', () => {
  taskHeader(
    '2b',
    'QA',
    'Lint',
    'JS'
  );

  const files = getJsFilesToLint();

  // return stream or promise for run-sequence
  return gulp.src( files )
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
} );

/**
 * Method: lintComposer
 *
 * Lint composer.json.
 *
 * Returns:
 *   Stream or promise for run-sequence.
 */
gulp.task( 'lintComposer', () => {
  taskHeader(
    '2c',
    'QA',
    'Lint',
    'composer.json'
  );

  // return stream or promise for run-sequence
  return gulp.src( 'composer.json' )
    .pipe( shell( [
      'composer validate'
    ] ) );
} );

/**
 * Method: lintPhp
 *
 * Lint PHP files.
 *
 * See:
 * * <PHP_CodeSniffer: https://packagist.org/packages/squizlabs/php_codesniffer>
 * * <WordPress Coding Standards for PHP_CodeSniffer: https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards>
 * * <Add PHP linting (PSR-2): https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/89>
 * * <Support for phpcs.xml: https://github.com/JustBlackBird/gulp-phpcs/issues/39>
 *
 * Returns:
 *   Stream or promise for run-sequence.
 */
gulp.task( 'lintPhp', () => {
  taskHeader(
    '2e',
    'QA',
    'Lint',
    'PHP'
  );

  return gulp.src( phpFiles )
  // Validate files using PHP Code Sniffer
    .pipe( phpcs( {
      bin: 'vendor/bin/phpcs',
      // standard must be included and cannot reference phpcs.xml, which is ignored
      // The WordPress ruleset cherry picks sniffs from Generic, PEAR, PSR-2, Squiz etc
      standard: 'WordPress', // -Core + -Docs + -Extra + -VIP
      warningSeverity: 0, // minimum severity required to display an error or warning.
      showSniffCode: true,
      // phpcs.xml exclusions are duplicated here,
      // but only 3 levels of specificity are tolerated by gulp-phpcs:
      exclude: [
        'WordPress.Files.FileName',
        'WordPress.Functions.DontExtract',
        'WordPress.CSRF.NonceVerification',
        'WordPress.XSS.EscapeOutput',
        'WordPress.VIP.RestrictedFunctions', // term_exists_term_exists - wpdtrt-tourdates
        'WordPress.VIP.ValidatedSanitizedInput',
        'Generic.Strings.UnnecessaryStringConcat'
      ]
    } ) )
  // Log all problems that were found
    .pipe( phpcs.reporter( 'log' ) );
} );

/**
 * Method: compile
 *
 * Tasks which compile.
 *
 * Parameters:
 *   callback - The runSequenceCallback that handles the response
 */
gulp.task( 'compile', ( callback ) => {
  taskHeader(
    '3',
    'Assets',
    'Compile',
    ''
  );

  runSequence(
    'compileCss',
    'transpileJs',
    callback
  );
} );

/**
 * Method: compileCss
 *
 * Compile CSS.
 *
 * Returns:
 *   Stream or promise for run-sequence.
 */
gulp.task( 'compileCss', () => {
  taskHeader(
    '3a',
    'Assets',
    'Compile',
    'SCSS -> CSS'
  );

  const processors = [
    autoprefixer( {
      cascade: false
    } ),
    pxtorem( {
      rootValue: 16,
      unitPrecision: 5,
      propList: [
        'font',
        'font-size',
        'padding',
        'padding-top',
        'padding-right',
        'padding-bottom',
        'padding-left',
        'margin',
        'margin-top',
        'margin-right',
        'margin-bottom',
        'margin-left',
        'bottom',
        'top',
        'max-width'
      ],
      selectorBlackList: [],
      replace: false,
      mediaQuery: true,
      minPixelValue: 0
    } )
  ];

  // return stream or promise for run-sequence
  return gulp.src( scssFiles )
    .pipe( sass( { outputStyle: 'expanded' } ) )
    .pipe( postcss( processors ) )
    .pipe( gulp.dest( cssDir ) );
} );

/**
 * Method: transpileJs
 *
 * Transpile ES6 to ES5, so that modern code runs in old browsers.
 *
 * Returns:
 *   Stream or promise for run-sequence.
 */
gulp.task( 'transpileJs', () => {
  taskHeader(
    '3a',
    'Assets',
    'Transpile',
    'ES6 JS -> ES5 JS'
  );

  // return stream or promise for run-sequence
  return gulp.src( jsFiles )
    .pipe( babel( {
      presets: [ 'env' ]
    } ) )
    .pipe( rename( {
      suffix: '-es5'
    } ) )
    .pipe( gulp.dest( jsDir ) );
} );

/**
 * Method: version
 *
 * Tasks which version the plugin.
 *
 * Parameters:
 *   callback - The runSequenceCallback that handles the response
 */
gulp.task( 'version', ( callback ) => {
  taskHeader(
    '4',
    'Version',
    'Bump',
    ''
  );

  runSequence(
    'versionLatest',
    'versionReplace',
    'versionLatestAutoload',
    callback
  );
} );

/**
 * Method: versionLatest
 *
 * Update the boilerplate dependency to the latest version
 *
 * If wpdtrt-plugin-boilerplate is loaded as a dependency
 * get the latest release of wpdtrt-plugin-boilerplate.
 * This has to run before versionReplace
 * so that the correct version information is available
 *
 * Returns:
 *   Stream or promise for run-sequence.
 */
gulp.task( 'versionLatest', () => {
  if ( isBoilerplate() || isTravis() ) {
    return true;
  }

  taskHeader(
    '4a',
    'Version',
    'Bump',
    'Update Composer dependencies'
  );

  // return stream or promise for run-sequence
  return gulp.src( dummyFile, { read: false } )
    .pipe( shell( [
      'composer update dotherightthing/wpdtrt-plugin-boilerplate --no-interaction --no-suggest'
    ] ) );
} );

/**
 * Method: versionReplace
 *
 * Replace version strings, using the version set in package.json.
 *
 * Returns:
 *   call to wpdtrtPluginBump (gulp-wpdtrt-plugin-bump)
 */
gulp.task( 'versionReplace', () => {
  taskHeader(
    '4b',
    'Version',
    'Bump',
    'Replace version strings'
  );

  return wpdtrtPluginBump( {
    inputPathRoot: '',
    inputPathBoilerplate: getBoilerplatePath()
  } );
} );

/**
 * Method: versionLatestAutoload
 *
 * Regenerate the list of PHP classes to be autoloaded.
 *
 * Returns:
 *   Stream or promise for run-sequence.
 */
gulp.task( 'versionLatestAutoload', () => {
  if ( isTravis() ) {
    return true;
  }

  taskHeader(
    '4c',
    'Version',
    'Generate',
    'List of classes to be autoloaded'
  );

  // regenerate autoload files
  return gulp.src( dummyFile, { read: false } )
    .pipe( shell( [
      'composer dump-autoload --no-interaction'
    ] ) );
} );

/**
 * Function: docs
 *
 * Generate JS & PHP documentation.
 *
 * Returns:
 *   Stream or promise for run-sequence.
 */
gulp.task( 'docs', () => {
  taskHeader(
    '4',
    'Documentation',
    'Generate',
    'All (PHP & JavaScript)'
  );

  // Quotes escape space better than backslash on Travis
  const naturalDocsPath = 'Natural Docs/NaturalDocs.exe';

  // note: src files are not used,
  // this structure is only used
  // to include the preceding log()
  return gulp.src( dummyFile, { read: false } )
    .pipe( shell( [
      `mono "${naturalDocsPath}" ./config/naturaldocs`
    ] ) );
} );

/**
 * Method: tests
 *
 * Tasks which run unit tests.
 *
 * Parameters:
 *   callback - The runSequenceCallback that handles the response
 */
gulp.task( 'tests', ( callback ) => {
  taskHeader(
    '6',
    'QA',
    'Tests'
  );

  runSequence(
    'testsWpUnit',
    callback
  );
} );

/**
 * Method: testsWpUnit
 *
 * Run WPUnit tests
 *
 * See: <Trouble running PHPUnit in Travis Build: https://stackoverflow.com/a/42467775/6850747>
 *
 * Returns:
 *   Stream or promise for run-sequence.
 */
gulp.task( 'testsWpUnit', () => {
  taskHeader(
    '6a',
    'QA',
    'Tests',
    'WPUnit'
  );

  const boilerplatePath = getBoilerplatePath();

  return gulp.src( dummyFile, { read: false } )
    .pipe( shell( [
      `./vendor/bin/phpunit --configuration ${boilerplatePath}phpunit.xml.dist`
    ] ) );
} );

/**
 * Method: release
 *
 * Tasks which package a release.
 *
 * Parameters:
 *   callback - The runSequenceCallback that handles the response
 */
gulp.task( 'release', ( callback ) => {
  const travis = isTravis();

  if ( travis ) {
    taskHeader(
      '7',
      'Release',
      'Generate',
      ''
    );

    runSequence(
      'release_composer_dist',
      'release_yarn_dist',
      'release_copy',
      'release_zip',
      callback
    );
  } else {
    callback();
  }
} );

/**
 * Method: release_composer_dist
 *
 * Uninstall PHP development dependencies.
 */
gulp.task( 'release_composer_dist', () => {
  taskHeader(
    '7a',
    'Release',
    'Uninstall dev dependencies',
    'Composer (PHP)'
  );

  /**
    * Remove dev packages once we've used them
    *
    * See: <Reduce vendor components required for deployment: https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/47>
    *
    * Returns:
    *   Stream or promise for run-sequence.
    */
  return gulp.src( dummyFile, { read: false } )
    .pipe( shell( [
      'composer install --prefer-dist --no-interaction --no-dev --no-suggest'
    ] ) );
} );

/**
 * Method: release_yarn_dist
 *
 * Uninstall Yarn development dependencies.
 *
 * Returns:
 *   Stream or promise for run-sequence.
 */
gulp.task( 'release_yarn_dist', () => {
  taskHeader(
    '7b',
    'Release',
    'Uninstall dev dependencies',
    'Yarn'
  );

  // return stream or promise for run-sequence
  return gulp.src( dummyFile, { read: false } )
    .pipe( shell( [
      'yarn install --non-interactive --production'
    ] ) );
} );

/**
 * Method: release_copy
 *
 * Copy release files to a temporary folder
 *
 * See: <globtester: http://www.globtester.com/>
 *
 * Returns:
 *   Stream or promise for run-sequence.
 */
gulp.task( 'release_copy', () => {
  taskHeader(
    '7c',
    'Release',
    'Copy files',
    'To temporary folder'
  );

  // Release files are those that are required
  // to use the package as a WP Plugin
  const releaseFiles = [
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
    '!./docs/**/*',
    // Not node module src files
    '!./node_modules/**/src/**/*'
  ];

  // return stream or promise for run-sequence
  // https://stackoverflow.com/a/32188928/6850747
  return gulp.src( releaseFiles, { base: '.' } )
    .pipe( print() )
    .pipe( gulp.dest( distDir ) );
} );

/**
 * Method: release_zip
 *
 * Generate release.zip for deployment by Travis/Github.
 *
 * Returns:
 *   Stream or promise for run-sequence.
 */
gulp.task( 'release_zip', () => {
  taskHeader(
    '7d',
    'Release',
    'Generate',
    'ZIP file'
  );

  // return stream or promise for run-sequence
  // https://stackoverflow.com/a/32188928/6850747
  return gulp.src( [
    `./${distDir}/**/*`
  ], { base: '.' } )
    .pipe( zip( 'release.zip' ) )
    .pipe( gulp.dest( './' ) );
} );

/**
 * Method: watch
 *
 * Watch for changes to `.scss` files.
 */
gulp.task( 'watch', () => {
  if ( !isTravis() ) {
    taskHeader(
      '*',
      'Watch',
      'Lint + Compile',
      'JS, SCSS + PHP'
    );

    gulp.watch( scssFiles, [ 'lintCss', 'compileCss' ] );
    gulp.watch( jsFiles, [ 'lintJs', 'transpileJs' ] );
  }
} );

/**
 * Method: default
 *
 * Default task
 *
 * Parameters:
 *   callback - The callback that handles the response
 *
 * --- javascript
 * gulp
 * ---
 */
gulp.task( 'default', ( callback ) => {
  const travis = isTravis();

  taskHeader(
    '0',
    'Installation',
    'Gulp',
    `Install${ travis ? ' and package for release' : ''}`
  );

  runSequence(
    // 1
    'dependencies',
    // 2
    'lint',
    // 3
    'compile',
    // 4
    'version',
    // 5
    'docs',
    // 6
    'tests',
    // 7
    'release'
  );

  callback();
} );
