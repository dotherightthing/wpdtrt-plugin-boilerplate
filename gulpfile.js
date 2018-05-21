/**
 * Gulp Task Runner
 * Compile front-end resources
 *
 * @example usage from parent plugin:
 *    gulp
 *    install | dev | dist
 *
 * @example usage from child plugin:
 *    gulp
 *    install | dev | dist
 *    --gulpfile ./vendor/dotherightthing/wpdtrt-plugin/gulpfile.js --cwd ./
 *
 * @package     WPDTRT_Plugin
 * @version     1.4.19
 */

/*jshint node:true*/
/*jshint node:true*/

/**
 * ===== dependencies =====
 */

var gulp = require("gulp");
var autoprefixer = require("autoprefixer");
var del = require("del");
var jsdoc = require("gulp-jsdoc3");
var jshint = require("gulp-jshint");
var log = require("fancy-log");
var phpcs = require("gulp-phpcs");
var postcss = require("gulp-postcss");
var print = require("gulp-print").default;
var pxtorem = require("postcss-pxtorem");
var runSequence = require("run-sequence");
var sass = require("gulp-sass");
var shell = require("gulp-shell");
var stylish = require("jshint-stylish");
var wpdtrtPluginBump = require("gulp-wpdtrt-plugin-bump");
var zip = require("gulp-zip");

/**
 * ===== paths =====
 */

// pop() - remove the last element from the path array and return it
var pluginName = process.cwd().split("/").pop();
var cssDir = "css";
var distDir = pluginName;
var dummyFile = "README.md";
var jsFiles = [
    "./js/*.js",
    "gulpfile.js"
];
var scssFiles = "./scss/*.scss";

/**
 * ===== helpers =====
 */

function taskheader(task_category, task_action, task_detail) {

    "use strict";

    log(" ");
    log("========================================");
    log(task_category + ":");
    log("=> " + task_action + ": " + task_detail);
    log("----------------------------------------");
    log(" ");
}

/**
 * ===== tasks =====
 */

gulp.task("yarn", function () {

    "use strict";

    taskheader(
        "Dependencies",
        "Install",
        "Yarn (Node / ex-Bower)"
    );

    // return stream or promise for run-sequence
    return gulp.src(dummyFile, {read: false})
        .pipe(shell([
            "yarn install --non-interactive"
        ]));
});

gulp.task("yarn_dist", function () {

    "use strict";

    taskheader(
        "Dependencies",
        "Uninstall dev dependencies",
        "Yarn (Node / ex-Bower)"
    );

    // return stream or promise for run-sequence
    return gulp.src(dummyFile, {read: false})
        .pipe(shell([
            "yarn install --non-interactive --production"
        ]));
});

gulp.task("composer", function () {

    "use strict";

    taskheader(
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

gulp.task("css", function () {

    "use strict";

    taskheader(
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

gulp.task("jshint", function () {

    "use strict";

    taskheader(
        "QA",
        "Lint",
        "JS"
    );

    // return stream or promise for run-sequence
    return gulp.src(jsFiles)
        .pipe(jshint())
        .pipe(jshint.reporter(stylish));
});

gulp.task("jsdoc", function () {

    "use strict";

    taskheader(
        "Documentation",
        "Generate",
        "JS"
    );

    var jsdocConfig = require("./jsdocConfig");

    // return stream or promise for run-sequence
    return gulp.src(jsFiles)
        // note: output cannot be piped on from jsdoc
        .pipe(jsdoc(jsdocConfig));
});

gulp.task("phpdoc_delete", function () {

    "use strict";

    taskheader(
        "Documentation",
        "Delete",
        "PHP"
    );

    // return stream or promise for run-sequence
    return del([
        "docs/phpdoc"
    ]);
});

gulp.task("phpdoc_remove_before", function () {

    "use strict";

    taskheader(
        "Documentation",
        "Uninstall",
        "Problematic packages"
    );

    // Read the extra data from the parent"s composer.json
    // The require function is relative to this gulpfile || node_modules
    // @see https://stackoverflow.com/a/23643087/6850747
    var composer_json = require("./composer.json");
    var phpdoc_remove_before = composer_json.extra[0]["require-after-phpdoc"];
    var phpdoc_remove_before_no_version = phpdoc_remove_before.split(":")[0];

    // return stream or promise for run-sequence
    // note: src files are not used,
    // this structure is only used
    // to include the preceding log()
    return gulp.src(dummyFile, {read: false})
        .pipe(shell([
            // install plugin which generates Fatal Error (#12)
            // if previously installed via package.json
            "composer remove " + phpdoc_remove_before_no_version
        ]));
});

gulp.task("phpdoc_doc", function () {

    "use strict";

    taskheader(
        "Documentation",
        "Generate",
        "PHP"
    );

    // return stream or promise for run-sequence
    // note: src files are not used,
    // this structure is only used
    // to include the preceding log()
    return gulp.src(dummyFile, {read: false})
        .pipe(shell([
            "vendor/bin/phpdoc -d . -t ./docs/phpdoc"
        ]));
});

gulp.task("phpdoc_require_after", function () {

    "use strict";

    taskheader(
        "Documentation",
        "Re-install",
        "Problematic packages"
    );

    // Read the extra data from the parent"s composer.json
    // The require function is relative to this gulpfile || node_modules
    // @see https://stackoverflow.com/a/23643087/6850747
    var composer_json = require("./composer.json");
    var phpdoc_require_after = composer_json.extra[0]["require-after-phpdoc"];

    // return stream or promise for run-sequence
    // note: src files are not used,
    // this structure is only used
    // to include the preceding log()
    return gulp.src(dummyFile, {read: false})
        .pipe(shell([
            // install plugin which generates Fatal Error (#12)
            // if previously installed via package.json
            "composer require " + phpdoc_require_after
        ]));
});

/**
 * PHP Code Sniffer
 *
 * @see https://github.com/dotherightthing/wpdtrt-plugin/issues/89
 */
gulp.task("phpcs", function () {

    "use strict";

    taskheader(
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

gulp.task("phpunit", function () {

    "use strict";

    taskheader(
        "QA",
        "Test",
        "PHP"
    );

    return gulp.src(dummyFile, {read: false})
        .pipe(shell([
            "phpunit"
        ]));
});

gulp.task("release_delete_pre", function () {

    "use strict";

    taskheader(
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
 * @see https://github.com/dotherightthing/wpdtrt-plugin/issues/47
 * @see https://github.com/dotherightthing/wpdtrt-plugin/issues/51
 */
gulp.task("add_dev_dependencies", function () {

    "use strict";

    taskheader(
        "Dependencies",
        "Install dev dependencies",
        "Composer (PHP)"
    );

    if (pluginName !== "wpdtrt-plugin") {

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

gulp.task("remove_dev_dependencies", function () {

    "use strict";

    taskheader(
        "Dependencies",
        "Uninstall dev dependencies",
        "Composer (PHP)"
    );

    /**
    * Remove dev packages once we"ve used them
    * @see https://github.com/dotherightthing/wpdtrt-plugin/issues/47
    */
    return gulp.src(dummyFile, {read: false})
        .pipe(shell([
            "composer install --prefer-dist --no-interaction --no-dev"
        ]));
});

gulp.task("release_delete_post", function () {

    "use strict";

    taskheader(
        "Release",
        "Delete",
        "Temporary folder"
    );

    // return stream or promise for run-sequence
    return del([
        distDir
    ]);
});

gulp.task("release_copy", function () {

    "use strict";

    taskheader(
        "Release",
        "Copy files",
        "To temporary folder"
    );

    // @see http://www.globtester.com/
    var releaseFiles = [
        "./config/**/*",
        "./css/**/*",
        "./docs/**/*",
        "./images/**/*",
        "!./images/**/*.pxm",
        "./js/**/*",
        "./languages/**/*",
        "./node_modules/**/*",
        // Yarn environment symlink:
        "!./node_modules/wpdtrt-plugin",
        // Yarn environment symlink contents:
        "!./node_modules/wpdtrt-plugin/**/*",
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

gulp.task("release", function (callback) {

    "use strict";

    runSequence(
        "release_delete_pre",
        "release_copy",
        "release_zip",
        "release_delete_post",
        callback
    );
});

/**
 * Tasks
 *
 * @see https://github.com/dotherightthing/wpdtrt-plugin/issues/60
 */

gulp.task("watch", function () {

    "use strict";

    taskheader(
        "Assets",
        "Compile",
        "Watch SCSS"
    );

    gulp.watch(scssFiles, ["css"]);
});

gulp.task("bump_update", function () {

    "use strict";

    taskheader(
        "Version",
        "Bump",
        "Update wpdtrt-plugin"
    );

    // if wpdtrt-plugin is loaded as a dependency
    if (pluginName !== "wpdtrt-plugin") {
        // get the latest release of wpdtrt-plugin
        // this has to run before bump_replace
        // so that the correct version information is available
        //
        // return stream or promise for run-sequence
        return gulp.src(dummyFile, {read: false})
            .pipe(shell([
                "composer update dotherightthing/wpdtrt-plugin --no-interaction"
            ]));
    }

    return;
});

gulp.task("bump_replace", function () {

    "use strict";

    taskheader(
        "Version",
        "Bump",
        "Replace version strings"
    );

    // if run from wpdtrt-plugin:
    // gulp bump
    var root_input_path = "";
    var wpdtrt_plugin_input_path = "";

    // if run from a child plugin:
    // gulp bump
    // --gulpfile ./vendor/dotherightthing/wpdtrt-plugin/gulpfile.js --cwd ./
    if (pluginName !== "wpdtrt-plugin") {
        root_input_path = "";
        wpdtrt_plugin_input_path = "vendor/dotherightthing/wpdtrt-plugin/";
    }

    return wpdtrtPluginBump({
        root_input_path: root_input_path,
        wpdtrt_plugin_input_path: wpdtrt_plugin_input_path
    });
});

gulp.task("bump_update_autoload", function () {

    "use strict";

    taskheader(
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

gulp.task("bump", function (callback) {

    "use strict";

    runSequence(
        "bump_update",
        "bump_replace",
        "bump_update_autoload",
        callback
    );
});

gulp.task("install", function (callback) {

    "use strict";

    taskheader(
        "Installation",
        "Install",
        ""
    );

    runSequence(
        // install dependencies
        "yarn",
        "composer",
        // add dev dependencies from wpdtrt-plugin
        "add_dev_dependencies",
        // compile CSS
        "css",
        // lint code for errors
        "jshint",
        // "phpcs",
        // bump version
        "bump_replace",
        // generate documentation
        "jsdoc",
        "phpdoc_doc",
        "phpdoc_require_after",
        // run unit tests in a WordPress environment
        "phpunit"
    );

    callback();
});

gulp.task("dev", function (callback) {

    "use strict";

    taskheader(
        "Installation",
        "Dev Install",
        "Re-install dev dependencies"
    );

    runSequence(
        // install dependencies
        "yarn",
        "composer",
        // add dev dependencies from wpdtrt-plugin
        "add_dev_dependencies",
        // compile CSS
        "css",
        // lint code for errors
        "jshint",
        // "phpcs",
        // generate documentation
        "jsdoc",
        "phpdoc_delete",
        "phpdoc_remove_before",
        "phpdoc_doc",
        "phpdoc_require_after",
        // run unit tests in a WordPress environment
        "phpunit",
        // watch for CSS changes
        "watch"
    );

    callback();
});

gulp.task("dist", function (callback) {

    "use strict";

    taskheader(
        "Installation",
        "Dist Install",
        "Package for release"
    );

    runSequence(
        // install dependencies
        "yarn",
        "composer",
        // add dev dependencies from wpdtrt-plugin
        "add_dev_dependencies",
        // compile CSS
        "css",
        // lint code for errors
        "jshint",
        // "phpcs"
        // bump version
        "bump_replace",
        // generate documentation
        "jsdoc",
        "phpdoc_delete",
        "phpdoc_remove_before",
        "phpdoc_doc",
        "phpdoc_require_after",
        // run unit tests in a WordPress environment
        "phpunit",
        // package release
        "remove_dev_dependencies",
        "yarn_dist",
        "release"
    );

    callback();
});

gulp.task("default", [
    "install"
]);
