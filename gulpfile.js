/**
 * @file DTRT WordPress Plugin Boilerplate gulpfile.js
 * @summary
 *     Gulp build tasks
 *
 * @example usage:
 *    npm run build
 *    npm run install_deps
 *    npm run package
 *    npm run test
 *    npm run version
 *    npm run watch
 *
 * @version     1.4.30
 */

/**
 * @namespace gulp
 */

/* eslint-env node */

var gulp = require("gulp");
var autoprefixer = require("autoprefixer");
var del = require("del");
var jsdoc = require("gulp-jsdoc3");
var eslint = require("gulp-eslint");
var log = require("fancy-log");
var phpcs = require("gulp-phpcs");
var postcss = require("gulp-postcss");
var print = require("gulp-print").default;
var pxtorem = require("postcss-pxtorem");
var runSequence = require("run-sequence");
var sass = require("gulp-sass");
var sassLint = require("gulp-sass-lint");
var shell = require("gulp-shell");
var validate = require("gulp-nice-package");
var wpdtrtPluginBump = require("gulp-wpdtrt-plugin-bump");
var zip = require("gulp-zip");

/**
 * @summary Get the pluginName from package.json
 * @return {string} pluginName
 * @memberOf gulp
 */
function get_pluginName() {
    // pop() - remove the last element from the path array and return it
    var pluginName = process.cwd().split("/").pop();

    return pluginName;
}

/**
 * @summary Determines whether we're in the boilerplate, or using it as a dependency
 * @return {Boolean} True if we're in the boilerplate
 * @memberOf gulp
 */
function is_boilerplate() {
    var pluginName = get_pluginName();

    return (pluginName === "wpdtrt-plugin-boilerplate");
}

/**
 * @summary Determines whether the current Gulp process is running on Travis CI
 * @return {Boolean}
 * @see https://docs.travis-ci.com/user/environment-variables/#Default-Environment-Variables
 * @memberOf gulp
 */
function is_travis() {
    return (typeof process.env.TRAVIS !== "undefined");
}

/**
 * @summary Get the path to the boilerplate
 * @return {string} path
 * @memberOf gulp
 */
function get_boilerplate_path() {
    var path = "";
    var boilerplate = is_boilerplate();

    if (! boilerplate) {
        path = "vendor/dotherightthing/wpdtrt-plugin-boilerplate/";
    }

    return path;
}

/**
 * @summary Get list of JavaScript files to lint and document
 * @return {array} jsFiles Array of files
 * @see http://usejsdoc.org/about-including-package.html
 * @memberOf gulp
 */
function get_js_files() {

    var boilerplate_path = get_boilerplate_path();

    if ( boilerplate_path !== "" ) {
        boilerplate_path += "/";
    }

    var jsFiles = [
        "./js/*.js",
        "package.json",
        boilerplate_path + "gulpfile.js",
        boilerplate_path + "js/backend.js"
    ];

    return jsFiles;
}

/**
 * @summary Displays a block comment for each task that runs
 * @param  {string} step          Step number
 * @param  {string} task_category Task category
 * @param  {string} task_action   Task action
 * @param  {string} task_detail   Task detail
 * @return {string}               Task header
 * @memberOf gulp
 */
function gulp_helper_taskheader(step, task_category, task_action, task_detail) {

    "use strict";

    log(" ");
    log("========================================");
    log(step + " - " + task_category + ":");
    log("=> " + task_action + ": " + task_detail);
    log("----------------------------------------");
    log(" ");
}

var pluginName = get_pluginName();
var pluginNameSafe = pluginName.replace(/-/g, "_");
var cssDir = "css";
var distDir = pluginName;
var dummyFile = "README.md";
var scssFiles = "./scss/*.scss";

/**
 * @callback runSequenceCallback
 * @summary Tells runSequence that a task has finished.
 * @description
 *     By returning a stream,
 *     the task system is able to plan the execution of those streams.
 *     But sometimes, especially when you're in callback hell
 *     or calling some streamless plugin,
 *     you aren't able to return a stream.
 *     That's what the callback is for.
 *     To let the task system know that you're finished
 *     and to move on to the next call in the execution chain.
 * @see https://stackoverflow.com/a/29299107/6850747
 * @memberOf gulp
 */

/**
 * @function install_dependencies
 * @summary Tasks which install dependencies
 * @param {runSequenceCallback} callback - The callback that handles the response
 * @memberOf gulp
 */
gulp.task("install_dependencies", function(callback) {

    "use strict";

    gulp_helper_taskheader(
        "1",
        "Dependencies",
        "Install",
        ""
    );

    runSequence(
        "install_dependencies_yarn",
        "install_dependencies_composer",
        "install_dependencies_boilerplate",
        callback
    );
});

/**
 * @function install_dependencies_yarn
 * @summary Install Yarn dependencies
 * @memberOf gulp
 */
gulp.task("install_dependencies_yarn", function () {

    "use strict";

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
 * @function install_dependencies_composer
 * @summary Install Composer dependencies
 * @memberOf gulp
 */
gulp.task("install_dependencies_composer", function () {

    "use strict";

    gulp_helper_taskheader(
        "1b",
        "Dependencies",
        "Install",
        "Composer (PHP)"
    );

    // return stream or promise for run-sequence
    return gulp.src(dummyFile, {read: false})
        .pipe(shell([
            "composer install --prefer-dist --no-interaction"
        ]));
});

/**
 * @function install_dependencies_boilerplate
 * @summary Install Boilerplate dependencies
 * @description
 *     The boilerplate has various dev dependencies
 *     which need to be made available to the child plugin for install tasks.
 *     Composer projects only install dev dependencies listed in their own `require-dev`,
 *     so we copy in the parent dev dependencies so that these are available to the child too.
 *     This approach allows us to easily remove all dev dependencies,
 *     before zipping project files,
 *     by re-running the `composer` install with the `--no-dev` flag.
 * @see https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/47
 * @see https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/51
 * @memberOf gulp
 */
gulp.task("install_dependencies_boilerplate", function () {

    "use strict";

    gulp_helper_taskheader(
        "1c",
        "Dependencies",
        "Install dev dependencies",
        "Composer (PHP)"
    );

    var boilerplate = is_boilerplate();

    if (! boilerplate) {

        // Read the require-dev list from the parent"s composer.json
        // The require function is relative to this gulpfile || node_modules
        // @see https://stackoverflow.com/a/23643087/6850747
        var composer_json = require("./composer.json");
        var dev_packages = composer_json["require-dev"];
        var dev_packages_str = "";

        // convert the require-dev list into a space-separated string
        // foo/bar:1.2.3
        // @see https://stackoverflow.com/a/1963179/6850747
        // Replaced with Object.keys as reqd by JSLint
        // @see https://jsperf.com/fastest-way-to-iterate-object
        Object.keys(dev_packages).forEach(function (element) {
            // element is the name of the key.
            // key is just a numerical value for the array
            dev_packages_str += (" " + element + ":" + dev_packages[element]);
        });

        // add each dependency from the parent"s require-dev
        // to the child"s require-dev
        return gulp.src(dummyFile, {read: false})
            .pipe(shell([
                "composer require" + dev_packages_str + " --dev"
            ]));
    }

    return;
});

/**
 * @function lint
 * @summary Tasks which lint files
 * @param {runSequenceCallback} callback - The callback that handles the response
 * @memberOf gulp
 */
gulp.task("lint", function(callback) {

    "use strict";

    gulp_helper_taskheader(
        "2",
        "QA",
        "Lint",
        ""
    );

    runSequence(
        "lint_sass",
        "lint_js",
        "lint_package_json",
        // "lint_php"
        callback
    );
});

/**
 * @function lint_sass
 * @summary Lint Sass files
 * @memberOf gulp
 */
gulp.task("lint_sass", function() {

    "use strict";

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
 * @function lint_js
 * @summary Lint JavaScript files
 * @memberOf gulp
 */
gulp.task("lint_js", function () {

    "use strict";

    gulp_helper_taskheader(
        "2b",
        "QA",
        "Lint",
        "JS"
    );

    var files = get_js_files();

    // return stream or promise for run-sequence
    return gulp.src(files)
        .pipe(eslint())
        .pipe(eslint.format());
        // .pipe(eslint.failAfterError());
});

/**
 * @function lint_package_json
 * @summary Lint package.json
 * @memberOf gulp
 */
gulp.task("lint_package_json", function () {

    "use strict";

    gulp_helper_taskheader(
        "2c",
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
 * @function lint_php
 * @summary Lint PHP files
 * @see https://packagist.org/packages/squizlabs/php_codesniffer
 * @see https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/89
 * @memberOf gulp
 */
gulp.task("lint_php", function () {

    "use strict";

    gulp_helper_taskheader(
        "2d",
        "QA",
        "Lint",
        "PHP"
    );

    return gulp.src([
        "**/*.php",
        "!docs/**/*.php",
        "!node_modules/**/*.php",
        "!vendor/**/*.php"
    ])
        // Validate files using PHP Code Sniffer
        .pipe(phpcs({
            bin: "vendor/bin/phpcs",
            standard: "WordPress-VIP", // "PSR2"
            warningSeverity: 0
        }))
        // Log all problems that were found
        .pipe(phpcs.reporter("log"));
});

/**
 * @function compile
 * @summary Tasks which compile
 * @param {runSequenceCallback} callback - The callback that handles the response
 * @memberOf gulp
 */
gulp.task("compile", function(callback) {

    "use strict";

    gulp_helper_taskheader(
        "3",
        "Assets",
        "Compile",
        ""
    );

    runSequence(
        "compile_css",
        callback
    );
});

/**
 * @function compile_css
 * @summary Compile CSS
 * @memberOf gulp
 */
gulp.task("compile_css", function () {

    "use strict";

    gulp_helper_taskheader(
        "3a",
        "Assets",
        "Compile",
        "SCSS -> CSS"
    );

    var processors = [
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
 * @function version
 * @summary Tasks which version the plugin
 * @param {runSequenceCallback} callback - The callback that handles the response
 * @memberOf gulp
 */
gulp.task("version", function (callback) {

    "use strict";

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
 * @function version_update
 * @summary Update the boilerplate dependency to the latest version
 * @description
 *     If wpdtrt-plugin-boilerplate is loaded as a dependency
 *     get the latest release of wpdtrt-plugin-boilerplate.
 *     This has to run before `version_replace`
 *     so that the correct version information is available
 * @memberOf gulp
 */
gulp.task("version_update", function () {

    "use strict";

    gulp_helper_taskheader(
        "4a",
        "Version",
        "Bump",
        "Update wpdtrt-plugin-boilerplate"
    );

    var boilerplate = is_boilerplate();

    if (! boilerplate) {
        // return stream or promise for run-sequence
        return gulp.src(dummyFile, {read: false})
            .pipe(shell([
                /* eslint-disable max-len */
                "composer update dotherightthing/wpdtrt-plugin-boilerplate --no-interaction"
                /* eslint-enable max-len */
            ]));
    }

    return;
});

/**
 * @function version_replace
 * @summary Replace version strings using the version set in package.json
 * @memberOf gulp
 */
gulp.task("version_replace", function () {

    "use strict";

    gulp_helper_taskheader(
        "4b",
        "Version",
        "Bump",
        "Replace version strings"
    );

    var boilerplate_path = get_boilerplate_path();

    return wpdtrtPluginBump({
        root_input_path: "",
        wpdtrt_plugin_boilerplate_input_path: boilerplate_path
    });
});

/**
 * @function version_update_autoload
 * @summary Regenerate the list of PHP classes to be autoloaded
 * @memberOf gulp
 */
gulp.task("version_update_autoload", function () {

    "use strict";

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
 * @function docs
 * @summary Tasks which generate documentation
 * @param {runSequenceCallback} callback - The callback that handles the response
 * @memberOf gulp
 */
gulp.task("docs", function (callback) {

    "use strict";

    gulp_helper_taskheader(
        "5",
        "Documentation",
        "",
        ""
    );

    runSequence(
        "docs_delete",
        "docs_js",
        "docs_php",
        callback
    );
});

/**
 * @function docs_delete
 * @summary Delete existing generated docs
 * @memberOf gulp
 */
gulp.task("docs_delete", function () {

    "use strict";

    gulp_helper_taskheader(
        "5a",
        "Documentation",
        "Delete",
        ""
    );

    // return stream or promise for run-sequence
    return del([
        "docs/jsdoc",
        "docs/phpdoc"
    ]);
});

/**
 * @function docs_js
 * @summary Generate JavaScript documentation
 * @memberOf gulp
 */
gulp.task("docs_js", function () {

    "use strict";

    gulp_helper_taskheader(
        "5b",
        "Documentation",
        "Generate",
        "JS"
    );

    var files = get_js_files();

    // require path is relative to this gulpfile
    var jsdocConfig = require("./jsdoc.json");

    // return stream or promise for run-sequence
    return gulp.src(files)
        // note: output cannot be piped on from jsdoc
        .pipe(jsdoc(jsdocConfig));
});

/**
 * @function docs_php
 * @summary Generate PHP documentation
 * @memberOf gulp
 */
gulp.task("docs_php", function () {

    "use strict";

    gulp_helper_taskheader(
        "5c",
        "Documentation",
        "Generate",
        "PHP"
    );

    var boilerplate = is_boilerplate();
    var boilerplate_path = get_boilerplate_path();
    var directory = ".";

    if (! boilerplate) {
        directory = "../../../"; // path to root from bin executable
    }

    // return stream or promise for run-sequence
    // note: src files are not used,
    // this structure is only used
    // to include the preceding log()
    return gulp.src(dummyFile, {read: false})
        .pipe(shell([
            "vendor/bin/phpdoc"
            + " --config " + boilerplate_path + "phpdoc.dist.xml"
            + " --directory " + directory
            // + " --ignore index.php" // overides all ignores in config file
        ]));
});

/**
 * @function unit_test
 * @summary Tasks which set up or run unit tests
 * @param {runSequenceCallback} callback - The callback that handles the response
 * @memberOf gulp
 */
gulp.task("unit_test", function (callback) {

    "use strict";

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
 * @function wpunit_install
 * @summary Install WPUnit test suite
 * @see https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/wiki/
 * Testing-&-Debugging#environmental-variables
 * @memberOf gulp
 */
gulp.task("wpunit_install", function () {

    "use strict";

    gulp_helper_taskheader(
        "6a",
        "QA",
        "Setup",
        "WPUnit"
    );

    var boilerplate = is_boilerplate();
    var boilerplate_path = get_boilerplate_path();
    var db_name = pluginNameSafe + "_wpunit_" + Date.now();
    var wp_version = "latest";
    var installer_path = "bin/";

    if (! boilerplate) {
        /* eslint-disable max-len */
        installer_path = boilerplate_path + "bin/";
        /* eslint-enable max-len */
    }

    return gulp.src(dummyFile, {read: false})
        .pipe(shell([
            /* eslint-disable max-len */
            "bash " + installer_path + "install-wp-tests.sh " + db_name + " " + wp_version
            /* eslint-enable max-len */
        ]));
});

/**
 * @function wpunit_run
 * @summary Run WPUnit tests
 * @memberOf gulp
 */
gulp.task("wpunit_run", function () {

    "use strict";

    gulp_helper_taskheader(
        "6b",
        "QA",
        "Run",
        "WPUnit"
    );

    var boilerplate_path = get_boilerplate_path();

    return gulp.src(dummyFile, {read: false})
        .pipe(shell([
            /* eslint-disable max-len */
            "phpunit --configuration " + boilerplate_path + "phpunit.xml.dist"
            /* eslint-enable max-len */
        ]));
});

/**
 * @function release
 * @summary Tasks which package a release
 * @param {runSequenceCallback} callback - The callback that handles the response
 * @memberOf gulp
 */
gulp.task("release", function (callback) {

    "use strict";

    gulp_helper_taskheader(
        "7",
        "Release",
        "Generate",
        ""
    );

    var travis = is_travis();

    if (travis) {
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
 * @function release_composer_dist
 * @summary Uninstall PHP development dependencies
 * @memberOf gulp
 */
gulp.task("release_composer_dist", function () {

    "use strict";

    gulp_helper_taskheader(
        "7a",
        "Release",
        "Uninstall dev dependencies",
        "Composer (PHP)"
    );

    /**
    * Remove dev packages once we"ve used them
    *
    * @see #47
    */
    return gulp.src(dummyFile, {read: false})
        .pipe(shell([
            "composer install --prefer-dist --no-interaction --no-dev"
        ]));
});

/**
 * @function release_yarn_dist
 * @summary Uninstall Yarn development dependencies
 * @memberOf gulp
 */
gulp.task("release_yarn_dist", function () {

    "use strict";

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
 * @function release_delete_pre
 * @summary Delete existing release.zip
 * @memberOf gulp
 */
gulp.task("release_delete_pre", function () {

    "use strict";

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
 * @function release_copy
 * @summary Copy release files to a temporary folder
 * @see http://www.globtester.com/
 * @memberOf gulp
 */
gulp.task("release_copy", function () {

    "use strict";

    gulp_helper_taskheader(
        "7d",
        "Release",
        "Copy files",
        "To temporary folder"
    );

    var releaseFiles = [
        "./config/**/*",
        "./css/**/*",
        "./docs/**/*",
        "!./docs/phpdoc/phpdoc-cache-*",
        "!./docs/phpdoc/phpdoc-cache-*/**/*",
        "./icons/**/*",
        "./images/**/*",
        "!./images/**/*.pxm",
        "./js/**/*",
        "./languages/**/*",
        "./node_modules/**/*",
        // Yarn environment symlink:
        "!./node_modules/wpdtrt-plugin-boilerplate",
        // Yarn environment symlink contents:
        "!./node_modules/wpdtrt-plugin-boilerplate/**/*",
        "./src/**/*",
        "./template-parts/**/*",
        "./vendor/**/*",
        "./views/**/*",
        "./index.php",
        "./readme.txt",
        "./uninstall.php",
        "./" + pluginName + ".php"
    ];

    // return stream or promise for run-sequence
    // https://stackoverflow.com/a/32188928/6850747
    return gulp.src(releaseFiles, {base: "."})
        .pipe(print())
        .pipe(gulp.dest(distDir));
});

/**
 * @function release_zip
 * @summary Generate release.zip for deployment by Travis/Github
 * @memberOf gulp
 */
gulp.task("release_zip", function () {

    "use strict";

    gulp_helper_taskheader(
        "7e",
        "Release",
        "Generate",
        "ZIP file"
    );

    // return stream or promise for run-sequence
    // https://stackoverflow.com/a/32188928/6850747
    return gulp.src([
        "./" + distDir + "/**/*"
    ], {base: "."})
        .pipe(zip("release.zip"))
        .pipe(gulp.dest("./"));
});

/**
 * @function release_delete_post
 * @summary Delete the temporary folder
 * @memberOf gulp
 */
gulp.task("release_delete_post", function () {

    "use strict";

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
 * @function watch
 * @summary Watch for changes to `.scss` files
 * @memberOf gulp
 */
gulp.task("watch", function () {

    "use strict";

    gulp_helper_taskheader(
        "8",
        "Watch",
        "Compile",
        "SCSS"
    );

    var travis = is_travis();

    if (!travis) {
        gulp.watch(scssFiles, ["css"]);
    }
});

/**
 * @function default
 * @summary Default task
 * @example
 * gulp
 * @param {runSequenceCallback} callback - The callback that handles the response
 * @memberOf gulp
 */
gulp.task("default", function (callback) {

    "use strict";

    var travis = is_travis();

    gulp_helper_taskheader(
        "0",
        "Installation",
        "Gulp",
        "Install" + (travis ? " and package for release" : "")
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
        "release",
        // 8
        "watch"
    );

    callback();
});

/**
 * @function dist
 * @summary Legacy dist task
 * @example
 * gulp dist
 * @todo Remove once all legacy calls have been removed from generated plugins
 * @memberOf gulp
 */
gulp.task("dist", [
    "default"
]);

/**
 * @function dev
 * @summary Legacy dev task
 * @example
 * gulp dev
 * @todo Remove once all legacy calls have been removed from generated plugins
 * @memberOf gulp
 */
gulp.task("dev", [
    "default"
]);

/**
 * @function install
 * @summary Legacy install task
 * @example
 * gulp install
 * @todo Remove once all legacy calls have been removed from generated plugins
 * @memberOf gulp
 */
gulp.task("install", [
    "default"
]);
