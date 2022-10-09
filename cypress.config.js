const { defineConfig } = require('cypress');

module.exports = defineConfig({
    e2e: {
        baseUrl: 'https://dontbelievethehype.co.nz'
    },
    pageLoadTimeout: 20000,
    responseTimeout: 15000,
    retries: {
        openMode: 2,
        runMode: 0
    },
    reporter: 'mochawesome',
    reporterOptions: {
        overwrite: false,
        html: false,
        json: true
    },
    video: false,
    viewportWidth: 1366,
    viewportHeight: 768,
    watchForFileChanges: true
});
