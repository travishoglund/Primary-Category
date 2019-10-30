const sass = require('node-sass');

module.exports = function (grunt) {

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        sass: {
            dev: {
                options: {
                    implementation: sass,
                    sourceMap: true
                },
                files: [{
                    expand: true,
                    src: ['./*.scss'],
                    dest: './',
                    ext: '.css',
                    extDot: 'last'
                }]
            }
        },
        watch: {
            sass: {
                files: [
                    '*.scss'
                ],
                tasks: ['sass'],
                options: {
                    style: 'compressed',
                    compass: true
                }
            },
        }
    });

    // Load tasks
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-sass');

    // Register tasks
    grunt.registerTask('default', [
        'sass',
    ]);
    grunt.registerTask('dev', [
        'watch'
    ]);

};
