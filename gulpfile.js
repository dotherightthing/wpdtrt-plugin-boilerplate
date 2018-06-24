/**
 * Gulp Task Runner
 * Compile front-end resources
 *
 * @example usage from parent plugin:
 *    gulp
 *
 * @example usage from child plugin:
 *    gulp
 *    --gulpfile
 *    ./vendor/dotherightthing/wpdtrt-plugin-boilerplate/gulpfile.js
 *    --cwd ./
 *
 * @version     1.4.30
 */

/* eslint-env node */

/**
 * ===== dependencies =====
 */

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
 * ===== paths =====
 */

// pop() - remove the last element from the path array and return it
var vendorName = "dotherightthing";
var pluginName = process.cwd().split("/").pop();
var pluginNameSafe = pluginName.replace(/-/g, "_");
var cssDir = "css";
var distDir = pluginName;
var dummyFile = "README.md";
var jsFiles = [
    "./js/*.js",
    "gulpfile.js"
];
var scssFiles = "./scss/*.scss";

// https://docs.travis-ci.com/user/environment-variables/#Default-Environment-Variables
var travis = (typeof process.env.TRAVIS !== "undefined");

/**
 * ===== helpers =====
 */

function taskheader(step_number, task_category, task_action, task_detail) {

    "use strict";

    log(" ");
    log("========================================");
    log(step_number + " - " + task_category + ":");
    log("=> " + task_action + ": " + task_detail);
    log("----------------------------------------");
    log(" ");
}

/**
 * ===== tasks =====
 */

/**
 * ===== 1. install_dependencies =====
 */

gulp.task("install_dependencies", function(callback) {

    "use strict";

    taskheader(
        "1",
        "Dependencies",
        "Install",
        ""
    );

    runSequence(
        "install_dependencies_yarn",
        "install_dependencies_composer",
        "install_dependencies_boilerplate",
        // By returning a stream,
        // the task system is able to plan the execution of those streams.
        // But sometimes, especially when you're in callback hell
        // or calling some streamless plugin,
        // you aren't able to return a stream.
        // That's what the callback is for.
        // To let the task system know that you're finished
        // and to move on to the next call in the execution chain.
        // see https://stackoverflow.com/a/29299107/6850747
        callback
    );
});

gulp.task("install_dependencies_yarn", function () {

    "use strict";

    taskheader(
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

gulp.task("install_dependencies_composer", function () {

    "use strict";

    taskheader(
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
 * The parent plugin has various dev dependencies
 * which need to be made available to the child plugin for install tasks.
 * Composer projects only install dev dependencies
 * listed in their own require-dev,
 * so we copy in the parent dev dependencies
 * so that these are available to the child too.
 * This approach allows us to easily remove all dev dependencies,
 * before zipping project files,
 * by re-running the composer install with the --no-dev flag.
 *
 * See also "Command Line Configuration", above.
 *
 * @see https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/47
 * @see https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/51
 */
gulp.task("install_dependencies_boilerplate", function () {

    "use strict";

    taskheader(
        "1c",
        "Dependencies",
        "Install dev dependencies",
        "Composer (PHP)"
    );

    if (pluginName !== "wpdtrt-plugin-boilerplate") {

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
 * ===== 2. lint =====
 */

gulp.task("lint", function(callback) {

    "use strict";

    taskheader(
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

gulp.task("lint_sass", function() {

    "use strict";

    taskheader(
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

gulp.task("lint_js", function () {

    "use strict";

    taskheader(
        "2b",
        "QA",
        "Lint",
        "JS"
    );

    // return stream or promise for run-sequence
    return gulp.src(jsFiles)
        .pipe(eslint())
        .pipe(eslint.format());
        // .pipe(eslint.failAfterError());
});

gulp.task("lint_package_json", function () {

    "use strict";

    taskheader(
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
 * PHP Code Sniffer
 *
 * @see https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/89
 */
gulp.task("lint_php", function () {

    "use strict";

    taskheader(
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
 * ===== 3. compile =====
 */

gulp.task("compile", function(callback) {

    "use strict";

    taskheader(
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

gulp.task("compile_css", function () {

    "use strict";

    taskheader(
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
 * ===== 4. version =====
 */

gulp.task("version", function (callback) {

    "use strict";

    taskheader(
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

gulp.task("version_update", function () {

    "use strict";

    taskheader(
        "4a",
        "Version",
        "Bump",
        "Update wpdtrt-plugin-boilerplate"
    );

    // if wpdtrt-plugin-boilerplate is loaded as a dependency
    if (pluginName !== "wpdtrt-plugin-boilerplate") {
        // get the latest release of wpdtrt-plugin-boilerplate
        // this has to run before version_replace
        // so that the correct version information is available
        //
        // return stream or promise for run-sequence
        return gulp.src(dummyFile, {read: false})
            .pipe(shell([
                /* eslint-disable max-len */
                "composer update " + vendorName + "/" + pluginName + " --no-interaction"
                /* eslint-enable max-len */
            ]));
    }

    return;
});

gulp.task("version_replace", function () {

    "use strict";

    taskheader(
        "4b",
        "Version",
        "Bump",
        "Replace version strings"
    );

    // if run from wpdtrt-plugin-boilerplate:
    // gulp version
    var root_input_path = "";
    var wpdtrt_plugin_boilerplate_input_path = "";

    // if run from a child plugin:
    // gulp version
    // --gulpfile
    // ./vendor/dotherightthing/wpdtrt-plugin-boilerplate/gulpfile.js
    // --cwd ./
    if (pluginName !== "wpdtrt-plugin-boilerplate") {
        root_input_path = "";
        /* eslint-disable max-len */
        wpdtrt_plugin_boilerplate_input_path = "vendor/dotherightthing/wpdtrt-plugin-boilerplate/";
        /* eslint-enable max-len */
    }

    return wpdtrtPluginBump({
        root_input_path: root_input_path,
        /* eslint-disable max-len */
        wpdtrt_plugin_boilerplate_input_path: wpdtrt_plugin_boilerplate_input_path
        /* eslint-enable max-len */
    });
});

gulp.task("version_update_autoload", function () {

    "use strict";

    taskheader(
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
 * ===== 5. docs =====
 */

gulp.task("docs", function (callback) {

    "use strict";

    taskheader(
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

gulp.task("docs_delete", function () {

    "use strict";

    taskheader(
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

gulp.task("docs_js", function () {

    "use strict";

    taskheader(
        "5b",
        "Documentation",
        "Generate",
        "JS"
    );

    var jsdocConfig_path = "./";

    if (pluginName !== "wpdtrt-plugin-boilerplate") {
        /* eslint-disable max-len */   
        jsdocConfig_path = "./vendor/dotherightthing/wpdtrt-plugin-boilerplate/";
        /* eslint-enable max-len */
    }

    // require path is relative to this gulpfile
    var jsdocConfig = require("./jsdoc.json");

    // return stream or promise for run-sequence
    return gulp.src(jsFiles)
        // note: output cannot be piped on from jsdoc
        .pipe(jsdoc(jsdocConfig));
});

gulp.task("docs_php", function () {

    "use strict";

    taskheader(
        "5c",
        "Documentation",
        "Generate",
        "PHP"
    );

    var config_path = "";
    var directory = ".";
    // var ignore = "";

    if (pluginName !== "wpdtrt-plugin-boilerplate") {
        config_path = "vendor/dotherightthing/wpdtrt-plugin-boilerplate/";
        directory = "../../../"; // path to root from bin executable
        // ignore = " --ignore index.php"; // ignores config ignores
    }

    // return stream or promise for run-sequence
    // note: src files are not used,
    // this structure is only used
    // to include the preceding log()
    return gulp.src(dummyFile, {read: false})
        .pipe(shell([
            "vendor/bin/phpdoc"
            + " --config " + config_path + "phpdoc.dist.xml"
            + " --directory " + directory
            // + ignore
        ]));
});

/**
 * ===== 6. unit_test =====
 */

gulp.task("unit_test", function (callback) {

    "use strict";

    taskheader(
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
 * Install WPUnit test suite
 *
 * @see https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/wiki/
 * Testing-&-Debugging#environmental-variables
 */
gulp.task("wpunit_install", function () {

    "use strict";

    taskheader(
        "6a",
        "QA",
        "Setup",
        "WPUnit"
    );

    var db_name = pluginNameSafe + "_wpunit_" + Date.now();
    var wp_version = "latest";
    var installer_path = "bin/";

    if (pluginName !== "wpdtrt-plugin-boilerplate") {
        /* eslint-disable max-len */
        installer_path = "vendor/dotherightthing/wpdtrt-plugin-boilerplate/bin/";
        /* eslint-enable max-len */
    }

    return gulp.src(dummyFile, {read: false})
        .pipe(shell([
            /* eslint-disable max-len */
            "bash " + installer_path + "install-wp-tests.sh " + db_name + " " + wp_version
            /* eslint-enable max-len */
        ]));
});

gulp.task("wpunit_run", function () {

    "use strict";

    taskheader(
        "6b",
        "QA",
        "Run",
        "WPUnit"
    );

    var config_path = "";

    if (pluginName !== "wpdtrt-plugin-boilerplate") {
        config_path = "vendor/dotherightthing/wpdtrt-plugin-boilerplate/";
    }

    return gulp.src(dummyFile, {read: false})
        .pipe(shell([
            "phpunit --configuration " + config_path + "phpunit.xml.dist"
        ]));
});

/**
 * ===== 7. release =====
 */

gulp.task("release", function (callback) {

    "use strict";

    taskheader(
        "7",
        "Release",
        "Generate",
        ""
    );

    if ( travis ) {
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

gulp.task("release_composer_dist", function () {

    "use strict";

    taskheader(
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

gulp.task("release_yarn_dist", function () {

    "use strict";

    taskheader(
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

gulp.task("release_delete_pre", function () {

    "use strict";

    taskheader(
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

gulp.task("release_copy", function () {

    "use strict";

    taskheader(
        "7d",
        "Release",
        "Copy files",
        "To temporary folder"
    );

    // @see http://www.globtester.com/
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

gulp.task("release_zip", function () {

    "use strict";

    taskheader(
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

gulp.task("release_delete_post", function () {

    "use strict";

    taskheader(
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
 * ===== 8. watch =====
 */

gulp.task("watch", function () {

    "use strict";

    taskheader(
        "8",
        "Watch",
        "Compile",
        "SCSS"
    );

    if ( ! travis ) {
        gulp.watch(scssFiles, ["css"]);
    }
});

/**
 * ===== 0. run tasks =====
 */

gulp.task("default", function (callback) {

    "use strict";

    taskheader(
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

// TODO remove once all legacy calls have been removed from generated plugins
gulp.task("dist", [
    "default"
]);

// TODO remove once all legacy calls have been removed from generated plugins
gulp.task("dev", [
    "default"
]);

// TODO remove once all legacy calls have been removed from generated plugins
gulp.task("install", [
    "default"
]);
