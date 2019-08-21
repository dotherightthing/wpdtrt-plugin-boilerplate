/**
 * @file DTRT WordPress Plugin Boilerplate gulpfile.js
 * @summary
 *     Gulp build tasks
 *
 * @example usage:
 *    yarn run build
 *    yarn run install_deps
 *    yarn run package
 *    yarn run test
 *    yarn run version
 *    yarn run watch
 *
 * @version     1.5.13
 */

/* globals require, process */

const gulp = require("gulp");
const autoprefixer = require("autoprefixer");
const babel = require("gulp-babel");
const del = require("del");
const ghRateLimit = require("gh-rate-limit");
const eslint = require("gulp-eslint");
const log = require("fancy-log");
const phpcs = require("gulp-phpcs");
const postcss = require("gulp-postcss");
const print = require("gulp-print").default;
const pxtorem = require("postcss-pxtorem");
const rename = require("gulp-rename");
const runSequence = require("run-sequence");
const sass = require("gulp-sass");
const sassLint = require("gulp-sass-lint");
const shell = require("gulp-shell");
const validate = require("gulp-nice-package");
const wpdtrtPluginBump = require("gulp-wpdtrt-plugin-bump");
const zip = require("gulp-zip");

/**
 * Function: get_pluginName
 * 
 * Get the pluginName from package.json.
 *
 * Returns:
 *   pluginName (string)
 */
function get_pluginName() {
    // pop() - remove the last element from the path array and return it
    const pluginName = process.cwd().split("/").pop();

    return pluginName;
}

/**
 * Function: is_boilerplate
 * 
 * Determines whether we're in the boilerplate, or using it as a dependency.
 *
 * Returns:
 *  Boolean - True if we're in the boilerplate
 */
function is_boilerplate() {
    const pluginName = get_pluginName();

    return (pluginName === "wpdtrt-plugin-boilerplate");
}

/**
 * Function: is_travis
 * 
 * Determines whether the current Gulp process is running on Travis CI.
 *
 * See: <Default Environment Variables: https://docs.travis-ci.com/user/environment-variables/#Default-Environment-Variables>.
 *
 * Returns:
 *   Boolean
 */
function is_travis() {
    return (typeof process.env.TRAVIS !== "undefined");
}

/**
 * Function: get_gh_token
 * 
 * Get the value of the Github API access token used by Travis.
 *
 * Returns:
 *   String
 */
function get_gh_token() {
    let token = "";

    if ( is_travis() ) {
        token = (process.env.GH_TOKEN ? process.env.GH_TOKEN : "");
    }

    return (token);
}

/**
 * Function 
 * 
 * Get the path to the boilerplate.
 *
 * Returns:
 *   path (string)
 */
function get_boilerplate_path() {
    let path = "";

    if (! is_boilerplate() ) {
        path = "vendor/dotherightthing/wpdtrt-plugin-boilerplate/";
    }

    return path;
}

/**
 * Get list of JavaScript files to transpile from ES6 to ES5.
 *
 * See: <http://usejsdoc.org/about-including-package.html>.
 * 
 * Returns:
 *   jsFiles - Array of files
 */
function get_js_files() {

    let boilerplate_path = get_boilerplate_path();

    if ( boilerplate_path !== "" ) {
        boilerplate_path += "/";
    }

    // note: es6 orignals only
    const jsFiles = [
        "./js/frontend.js",
        "./js/backend.js",
        `${boilerplate_path}js/frontend.js`,
        `${boilerplate_path}js/backend.js`
    ];

    return jsFiles;
}

/**
 * Get list of JavaScript files to lint.
 *
 * See: <http://usejsdoc.org/about-including-package.html>.
 * 
 * Returns:
 *   jsFiles - Array of files
 */
function get_js_files_to_lint() {

    let boilerplate_path = get_boilerplate_path();

    if ( boilerplate_path !== "" ) {
        boilerplate_path += "/";
    }

    // note: es6 orignals only
    const jsFilesToLint = [
        "./cypress/**/*.js",
        "./js/frontend.js",
        "./js/backend.js",
        `${boilerplate_path}js/frontend.js`,
        `${boilerplate_path}js/backend.js`
    ];

    return jsFilesToLint;
}

/**
 * Displays a block comment for each task that runs.
 *
 * Parameters:
 *   step - Step number (string)
 *   task_category - Task category (string)
 *   task_action - Task action (string)
 *   task_detail - Task detail (string)
 * 
 * Returns:
 *   Task header (string)
 */
function gulp_helper_taskheader(step, task_category, task_action, task_detail) {

    log(" ");
    log("========================================");
    log(`${step} - ${task_category}:`);
    log(`=> ${task_action}: ${task_detail}`);
    log("----------------------------------------");
    log(" ");
}

const pluginName = get_pluginName();
const pluginNameSafe = pluginName.replace(/-/g, "_");
const cssDir = "css";
const jsDir = "js";
const distDir = pluginName;
const dummyFile = "README.md";
const jsFiles = get_js_files();
const phpFiles = [
    "**/*.php",
    "!docs/**/*.php",
    "!node_modules/**/*.php",
    "!vendor/**/*.php",
    "!wp-content/**/*.php"
];
const scssFiles = "./scss/*.scss";

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
 * Method: install_dependencies
 * 
 * Tasks which install dependencies.
 *
 * Parameters:
 *   callback - The runSequenceCallback that handles the response
 */
gulp.task("install_dependencies", (callback) => {

    gulp_helper_taskheader(
        "1",
        "Dependencies",
        "Install",
        ""
    );

    runSequence(
        "install_dependencies_yarn",
        "preinstall_dependencies_github",
        "install_dependencies_composer",
        callback
    );
});

/**
 * Function: install_dependencies_yarn
 * 
 * Install Yarn dependencies.
 */
gulp.task("install_dependencies_yarn", () => {

    gulp_helper_taskheader(
        "1a",
        "Dependencies",
        "Install",
        "Yarn"
    );

    // return stream or promise for run-sequence
    return gulp.src(dummyFile, {read: false})
        .pipe(shell([
            "yarn install --non-interactive"
        ]));
});

/**
 * Function: preinstall_dependencies_github
 * 
 * Expose the Github API rate limit to aid in debugging failed builds.
 * 
 * Returns:
 *   Rate Limit (object)
 */
gulp.task("preinstall_dependencies_github", () => {

    gulp_helper_taskheader(
        "1b",
        "Dependencies",
        "Pre-Install",
        "Check current Github API rate limit for automated installs"
    );

    if ( ! is_travis() ) {
        return true;
    }

    const token = get_gh_token();

    if ( token === "" ) {
        return true;
    }

    return ghRateLimit({
      token: get_gh_token()
    }).then( (status) => {
        log("Github API rate limit:");
        log(`API calls remaining: ${status.core.remaining}/${status.core.limit}`);
        log(" ");
    });
});

/**
 * Function: install_dependencies_composer
 * 
 * Install Composer dependencies.
 */
gulp.task("install_dependencies_composer", () => {

    // Travis already runs composer install
    if ( is_travis() ) {
        return true;
    }

    gulp_helper_taskheader(
        "1b",
        "Dependencies",
        "Install",
        "Composer (PHP)"
    );

    // return stream or promise for run-sequence
    return gulp.src(dummyFile, {read: false})
        .pipe(shell([
            "composer install --prefer-dist --no-interaction --no-suggest"
        ]));
});

/**
 * Function: lint
 * 
 * Tasks which lint files.
 *
 * Parameters:
 *   callback - The runSequenceCallback that handles the response
 */
gulp.task("lint", (callback) => {

    gulp_helper_taskheader(
        "2",
        "QA",
        "Lint",
        ""
    );

    runSequence(
        "lint_sass",
        "lint_js",
        "lint_composer_json",
        "lint_package_json",
        "lint_php",
        callback
    );
});

/**
 * Function: lint_sass
 * 
 * Lint Sass files.
 */
gulp.task("lint_sass", () => {

    gulp_helper_taskheader(
        "2a",
        "QA",
        "Lint",
        "Sass"
    );

    return gulp.src(scssFiles)
        .pipe(sassLint())
        .pipe(sassLint.format());
        // .pipe(sassLint.failOnError())
});

/**
 * Function: lint_js
 * 
 * Lint JavaScript files.
 */
gulp.task("lint_js", () => {

    gulp_helper_taskheader(
        "2b",
        "QA",
        "Lint",
        "JS"
    );

    const files = get_js_files_to_lint();

    // return stream or promise for run-sequence
    return gulp.src(files)
        .pipe(eslint())
        .pipe(eslint.format());
        // .pipe(eslint.failAfterError());
});

/**
 * Function: lint_composer_json
 * 
 * Lint composer.json.
 */
gulp.task("lint_composer_json", () => {

    gulp_helper_taskheader(
        "2c",
        "QA",
        "Lint",
        "composer.json"
    );

    // return stream or promise for run-sequence
    return gulp.src("composer.json")
        .pipe(shell([
            "composer validate"
        ]));
});

/**
 * Function: lint_package_json
 * 
 * Lint package.json.
 */
gulp.task("lint_package_json", () => {

    gulp_helper_taskheader(
        "2d",
        "QA",
        "Lint",
        "package.json"
    );

    // return stream or promise for run-sequence
    return gulp.src("package.json")
        .pipe(validate({
            recommendations: false
        }));
});

/**
 * Function: lint_php
 * 
 * Lint PHP files.
 * 
 * See:
 * * <PHP_CodeSniffer: https://packagist.org/packages/squizlabs/php_codesniffer>
 * * <WordPress Coding Standards for PHP_CodeSniffer: https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards>
 * * <Add PHP linting (PSR-2): https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/89>
 * * <Support for phpcs.xml: https://github.com/JustBlackBird/gulp-phpcs/issues/39>
 */
gulp.task("lint_php", () => {

    gulp_helper_taskheader(
        "2e",
        "QA",
        "Lint",
        "PHP"
    );

    return gulp.src(phpFiles)
        // Validate files using PHP Code Sniffer
        .pipe(phpcs({
            bin: "vendor/bin/phpcs",
            // standard must be included and cannot reference phpcs.xml, which is ignored
            // The WordPress ruleset cherry picks sniffs from Generic, PEAR, PSR-2, Squiz etc
            standard: "WordPress", // -Core + -Docs + -Extra + -VIP
            warningSeverity: 0, // minimum severity required to display an error or warning.
            showSniffCode: true,
            // phpcs.xml exclusions are duplicated here,
            // but only 3 levels of specificity are tolerated by gulp-phpcs:
            exclude: [
                "WordPress.Files.FileName",
                "WordPress.Functions.DontExtract",
                "WordPress.CSRF.NonceVerification",
                "WordPress.XSS.EscapeOutput",
                "WordPress.VIP.RestrictedFunctions", // term_exists_term_exists - wpdtrt-tourdates
                "WordPress.VIP.ValidatedSanitizedInput",
                "Generic.Strings.UnnecessaryStringConcat"
            ]
        }))
        // Log all problems that were found
        .pipe(phpcs.reporter("log"));
});

/**
 * Function: compile
 * 
 * Tasks which compile.
 *
 * Parameters:
 *   callback - The runSequenceCallback that handles the response
 */
gulp.task("compile", (callback) => {

    gulp_helper_taskheader(
        "3",
        "Assets",
        "Compile",
        ""
    );

    runSequence(
        "compile_css",
        "transpile_js",
        callback
    );
});

/**
 * Function: compile_css
 * 
 * Compile CSS.
 */
gulp.task("compile_css", () => {

    gulp_helper_taskheader(
        "3a",
        "Assets",
        "Compile",
        "SCSS -> CSS"
    );

    const processors = [
        autoprefixer({
            cascade: false
        }),
        pxtorem({
            rootValue: 16,
            unitPrecision: 5,
            propList: [
                "font",
                "font-size",
                "padding",
                "padding-top",
                "padding-right",
                "padding-bottom",
                "padding-left",
                "margin",
                "margin-top",
                "margin-right",
                "margin-bottom",
                "margin-left",
                "bottom",
                "top",
                "max-width"
            ],
            selectorBlackList: [],
            replace: false,
            mediaQuery: true,
            minPixelValue: 0
        })
    ];

    // return stream or promise for run-sequence
    return gulp.src(scssFiles)
        .pipe(sass({outputStyle: "expanded"}))
        .pipe(postcss(processors))
        .pipe(gulp.dest(cssDir));
});

/**
 * Function: transpile_js

 * Transpile JS.
 */
gulp.task("transpile_js", () => {

    gulp_helper_taskheader(
        "3a",
        "Assets",
        "Transpile",
        "ES6 JS -> ES5 JS"
    );

    // return stream or promise for run-sequence
    return gulp.src(jsFiles)
        .pipe(babel({
            presets: ["env"]
        }))
        .pipe(rename({
            suffix: "-es5"
        }))
        .pipe(gulp.dest(jsDir));
});

/**
 * Function: version
 * 
 * Tasks which version the plugin.
 *
 * Parameters:
 *   callback - The runSequenceCallback that handles the response
 */
gulp.task("version", (callback) => {

    gulp_helper_taskheader(
        "4",
        "Version",
        "Bump",
        ""
    );

    runSequence(
        "version_update",
        "version_replace",
        "version_update_autoload",
        callback
    );
});

/**
 * Function: version_update
 * 
 * Update the boilerplate dependency to the latest version
 * 
 * If wpdtrt-plugin-boilerplate is loaded as a dependency
 * get the latest release of wpdtrt-plugin-boilerplate.
 * This has to run before version_replace
 * so that the correct version information is available
 */
gulp.task("version_update", () => {

    if ( is_boilerplate() || is_travis() ) {
        return true;
    }

    gulp_helper_taskheader(
        "4a",
        "Version",
        "Bump",
        "Update Composer dependencies"
    );

    // return stream or promise for run-sequence
    return gulp.src(dummyFile, {read: false})
        .pipe(shell([
            "composer update --no-interaction --no-suggest"
        ]));
});

/**
 * Function: version_replace
 * 
 * Replace version strings using the version set in package.json.
 */
gulp.task("version_replace", () => {

    gulp_helper_taskheader(
        "4b",
        "Version",
        "Bump",
        "Replace version strings"
    );

    return wpdtrtPluginBump({
        root_input_path: "",
        wpdtrt_plugin_boilerplate_input_path: get_boilerplate_path()
    });
});

/**
 * Function: version_update_autoload
 * 
 * Regenerate the list of PHP classes to be autoloaded.
 */
gulp.task("version_update_autoload", () => {

    if ( is_travis() ) {
        return true;
    }

    gulp_helper_taskheader(
        "4c",
        "Version",
        "Generate",
        "List of classes to be autoloaded"
    );

    // regenerate autoload files
    return gulp.src(dummyFile, {read: false})
        .pipe(shell([
            "composer dump-autoload --no-interaction"
        ]));
});

/**
 * Function: docs
 * 
 * Tasks which generate documentation.
 *
 * Parameters:
 *   callback - The runSequenceCallback that handles the response
 */
gulp.task("docs", (callback) => {

    gulp_helper_taskheader(
        "5",
        "Documentation",
        "Generate",
        "All (PHP & JavaScript)"
    );

    let command = "";

    /* eslint-disable quotes */

    if ( is_travis() ) {
        // Travis install
        // https://github.com/NaturalDocs/NaturalDocs/issues/39
        // quotes escape space better than backslash on Travis
        command = `mono "Natural Docs/NaturalDocs.exe" ./config/naturaldocs`;
    } else {
        // global install
        command = `mono "/Applications/Natural Docs/NaturalDocs.exe" ./config/naturaldocs`;
    }

    /* eslint-enable quotes */

    // return stream or promise for run-sequence
    // note: src files are not used,
    // this structure is only used
    // to include the preceding log()
    return gulp.src(dummyFile, {read: false})
        .pipe(shell([
            command
        ]));
});

/**
 * Function: unit_test
 * 
 * Tasks which set up or run unit tests.
 *
 * Parameters:
 *   callback - The runSequenceCallback that handles the response
 */
gulp.task("unit_test", (callback) => {

    gulp_helper_taskheader(
        "6",
        "QA",
        "",
        ""
    );

    runSequence(
        "wpunit_install",
        "wpunit_run",
        callback
    );
});

/**
 * Function: wpunit_install
 * 
 * Install WPUnit test suite.
 * 
 * See: <Testing & Debugging: https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/wiki/Testing-&-Debugging#environmental-variables>
 */
gulp.task("wpunit_install", () => {

    gulp_helper_taskheader(
        "6a",
        "QA",
        "Setup",
        "WPUnit"
    );

    const boilerplate = is_boilerplate();
    const boilerplate_path = get_boilerplate_path();
    const db_name = `${pluginNameSafe}_wpunit_${Date.now()}`;
    const wp_version = "latest";
    let installer_path = "bin/";

    if (! boilerplate) {
        installer_path = `${boilerplate_path}bin/`;
    }

    return gulp.src(dummyFile, {read: false})
        .pipe(shell([
            `bash ${installer_path}install-wp-tests.sh ${db_name} ${wp_version}`
        ]));
});

/**
 * Function: wpunit_run
 * 
 * Run WPUnit tests
 * 
 * See: <Trouble running PHPUnit in Travis Build: https://stackoverflow.com/a/42467775/6850747>
 */
gulp.task("wpunit_run", () => {

    gulp_helper_taskheader(
        "6b",
        "QA",
        "Run",
        "WPUnit"
    );

    const boilerplate_path = get_boilerplate_path();

    return gulp.src(dummyFile, {read: false})
        .pipe(shell([
            `./vendor/bin/phpunit --configuration ${boilerplate_path}phpunit.xml.dist`
        ]));
});

/**
 * Function: release
 * 
 * Tasks which package a release.
 *
 * Parameters:
 *   callback - The runSequenceCallback that handles the response
 */
gulp.task("release", (callback) => {

    const travis = is_travis();

    if (travis) {
        gulp_helper_taskheader(
            "7",
            "Release",
            "Generate",
            ""
        );

        runSequence(
            "release_composer_dist",
            "release_yarn_dist",
            "release_delete_pre",
            "release_copy",
            "release_zip",
            "release_delete_post",
            callback
        );
    } else {
        callback();
    }
});

/**
 * Function: release_composer_dist
 * 
 * Uninstall PHP development dependencies.
 */
gulp.task("release_composer_dist", () => {

    gulp_helper_taskheader(
        "7a",
        "Release",
        "Uninstall dev dependencies",
        "Composer (PHP)"
    );

    /**
    * Remove dev packages once we"ve used them
    *
    * See: <#47
    */
    return gulp.src(dummyFile, {read: false})
        .pipe(shell([
            "composer install --prefer-dist --no-interaction --no-dev --no-suggest"
        ]));
});

/**
 * Function: release_yarn_dist
 * 
 * Uninstall Yarn development dependencies.
 */
gulp.task("release_yarn_dist", () => {

    gulp_helper_taskheader(
        "7b",
        "Release",
        "Uninstall dev dependencies",
        "Yarn"
    );

    // return stream or promise for run-sequence
    return gulp.src(dummyFile, {read: false})
        .pipe(shell([
            "yarn install --non-interactive --production"
        ]));
});

/**
 * Function: release_delete_pre
 * 
 * Delete existing release.zip.
 */
gulp.task("release_delete_pre", () => {

    gulp_helper_taskheader(
        "7c",
        "Release",
        "Delete",
        "Previous release"
    );

    // return stream or promise for run-sequence
    return del([
        "release.zip"
    ]);
});

/**
 * Function: release_copy
 * 
 * Copy release files to a temporary folder
 * 
 * See: <globtester: http://www.globtester.com/>
 */
gulp.task("release_copy", () => {

    gulp_helper_taskheader(
        "7d",
        "Release",
        "Copy files",
        "To temporary folder"
    );

    // Release files are those that are required
    // to use the package as a WP Plugin
    const releaseFiles = [
        // Composer file, contains TGMPA dependencies object
        "./composer.json",
        // Compiled CSS
        "./css/**/*",
        // Cypress.io
        "./cypress.json",
        "./cypress/**/*",
        // Any icons
        "./icons/**/*",
        // Any images
        "./images/**/*",
        // Not the project logo
        "!./images/**/*.pxm",
        // Transpiled ES5 JS incl backend-es5.js from boilerplate
        "./js/**/*-es5.js",
        // WP i18n .pot files
        "./languages/**/*",
        // Yarn front-end dependencies
        "./node_modules/**/*",
        // Yarn environment symlink
        "!./node_modules/wpdtrt-plugin-boilerplate",
        // Yarn environment symlink contents
        "!./node_modules/wpdtrt-plugin-boilerplate/**/*",
        // Plugin logic
        "./src/**/*",
        // Plugin template partials
        "./template-parts/**/*",
        // Any PHP dependencies
        "./vendor/**/*",
        // Not binary executables
        "!./vendor/bin/**/*",
        // Not JSON files
        "!./node_modules/**/*.json",
        "!./vendor/**/*.json",
        // Not Less files
        "!./node_modules/**/*.less",
        "!./vendor/**/*.less",
        // Not License files
        "!./node_modules/**/LICENSE",
        "!./vendor/**/LICENSE",
        // Not Markdown files
        "!./node_modules/**/*.md",
        "!./vendor/**/*.md",
        // Not PHP sample files
        "!./node_modules/**/*example*.php",
        "!./vendor/**/*example*.php",
        // Not Sass files
        "!./node_modules/**/*.scss",
        "!./vendor/**/*.scss",
        // Not SCSS folders
        "!./node_modules/**/*/scss",
        "!./vendor/**/*/scss",
        // Not test files
        "!./node_modules/**/test/**/*",
        "!./vendor/**/test/**/*",
        // Not tests files
        "!./node_modules/**/tests/**/*",
        "!./vendor/**/tests/**/*",
        // Not XML files
        "!./node_modules/**/*.xml",
        "!./vendor/**/*.xml",
        // Not Zip files
        "!./node_modules/**/*.zip",
        "!./vendor/**/*.zip",
        // Plugin WP Read Me
        "./readme.txt",
        // Plugin WP Uninstaller
        "./uninstall.php",
        // Plugin root config
        `./${pluginName}.php`,
        // Not CSS source maps
        "!./css/maps/**/*",
        // Not demo files
        "!./icons/icomoon/demo-files/**/*",
        // Not docs
        "!./docs/**/*",
        // Not node module src files
        "!./node_modules/**/src/**/*",
    ];

    // return stream or promise for run-sequence
    // https://stackoverflow.com/a/32188928/6850747
    return gulp.src(releaseFiles, {base: "."})
        .pipe(print())
        .pipe(gulp.dest(distDir));
});

/**
 * Function: release_zip
 * 
 * Generate release.zip for deployment by Travis/Github.
 */
gulp.task("release_zip", () => {

    gulp_helper_taskheader(
        "7e",
        "Release",
        "Generate",
        "ZIP file"
    );

    // return stream or promise for run-sequence
    // https://stackoverflow.com/a/32188928/6850747
    return gulp.src([
        `./${distDir}/**/*`
    ], {base: "."})
        .pipe(zip("release.zip"))
        .pipe(gulp.dest("./"));
});

/**
 * Function: release_delete_post
 * 
 * Delete the temporary folder.
 */
gulp.task("release_delete_post", () => {

    gulp_helper_taskheader(
        "7f",
        "Release",
        "Delete",
        "Temporary folder"
    );

    // return stream or promise for run-sequence
    return del([
        distDir
    ]);
});

/**
 * Function: watch
 * 
 * Watch for changes to `.scss` files.
 */
gulp.task("watch", () => {

    if (! is_travis() ) {
        gulp_helper_taskheader(
            "*",
            "Watch",
            "Lint + Compile",
            "JS, SCSS + PHP"
        );

        gulp.watch(scssFiles, ["lint_sass", "compile_css"]);
        gulp.watch(jsFiles, ["lint_js", "transpile_js"]);
    }
});

/**
 * Function: default
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
gulp.task("default", (callback) => {

    const travis = is_travis();

    gulp_helper_taskheader(
        "0",
        "Installation",
        "Gulp",
        `Install${ travis ? " and package for release" : ""}`
    );

    runSequence(
        // 1
        "install_dependencies",
        // 2
        "lint",
        // 3
        "compile",
        // 4
        "version",
        // 5
        "docs",
        // 6
        "unit_test",
        // 7
        "release"
    );

    callback();
});

/* eslint-enable max-len */

