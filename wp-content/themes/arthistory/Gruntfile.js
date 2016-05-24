/*jslint node: true */
'use strict';

module.exports = function(grunt){
    // load all grunt tasks matching the ['grunt-*', '@*/grunt-*'] patterns
    require('load-grunt-tasks')(grunt);
    var watchFiles = {
        mainJs: [
            'js/**/*.js'
        ],
        mainSass: [
            'scss/**/*.scss'
        ],
        handlebars: [
            'handlebars/**/*.hbs'
        ]
    };
    grunt.initConfig({
        concat: {
            uglifyBase: {
                src: [
                    'build/app.base.min.js',
                    'bower_components/vis/dist/vis.min.js'
                ],
                dest: 'build/app.base.min.js'
            }
        },
        copy :{
            images: {
                expand: true,
                cwd: 'assets/images/',
                src: '**/*.*',
                dest: 'build/assets/',
                flatten: true,
                filter: 'isFile'
            },
            fonts: {
                expand: true,
                cwd: 'assets/fonts',
                src: '**/*.*',
                flatten: true,
                dest: 'build/assets/'
            }
        },
        handlebars: {
            compile: {
                src: watchFiles.handlebars,
                dest: 'build/baobao.handlebars.min.js'
            },
            options: {
                namespace: 'Handlebars.templates',
                processName: function(filePath) {
                    var pathPieces = filePath.split('/'),//get filename from path
                        filePieces = pathPieces[pathPieces.length-1].split('.');//return name of file without extension
                    return filePieces[0];
                }
            }
        },
        jshint: {
            main: {
                src: [
                    watchFiles.mainJs
                ],
                options: {
                    jshintrc: true
                }
            }
        },
        postcss: {
            base: {
                options: {
                    map: true, // inline sourcemaps,
                    processors: [
                        require('autoprefixer')({browsers: 'last 2 versions'}) // add vendor prefixes
                    ]
                },
                dist: {
                    src: 'build/app.base.min.css'
                }
            },
            main: {
                options: {
                    map: true, // inline sourcemaps,
                    processors: [
                        require('autoprefixer')({browsers: 'last 2 versions'}) // add vendor prefixes
                    ]
                },
                dist: {
                    src: 'build/app.main.min.css'
                }
            }
        },
        sass: {
            base: {
                files: {
                    'build/app.base.min.css': 'scss/app_base.scss'
                },
                options: {
                    style: 'compressed',
                    trace: true
                }
            },
            main: {
                files: {
                    'build/app.main.min.css': 'scss/app_main.scss'
                },
                options: {
                    style: 'compressed',
                    trace: true
                }
            }
        },
        uglify: {
            base: {
                files: {
                    'build/app.base.min.js': [
                        'bower_components/cash/dist/cash.min.js',
                        'bower_components/bluebird/js/browser/bluebird.min.js',
                        'bower_components/velocity/velocity.min.js',
                        'bower_components/velocity/velocity.ui.min.js',
                        'bower_components/moment/min/moment.min.js'
                    ]
                },
                options: {
                    banner: '/*! <%= grunt.template.today("yyyy-mm-dd") %> */\n',
                    sourceMap: true,
                    preserveComments: false,
                    compress: true,
                    mangle: false
                }
            },
            main: {
                files: {
                    'build/app.main.min.js': [
                        'js/app.js',
                        'js/lib/*.js',
                        'js/modules/*.js',
                        'js/templates/theme/shared.js',
                        'js/**/*.js'
                    ]
                },
                options: {
                    banner: '/*! <%= grunt.template.today("yyyy-mm-dd") %> */\n',
                    sourceMap: true,
                    preserveComments: 'some',
                    mangle: false
                }
            }
        },
        watch: {
            mainJs: {
                files: watchFiles.mainJs,
                tasks: ['jshint:main','uglify:main'],
                options: {
                    livereload: true
                }
            },
            mainSass: {
                files: watchFiles.mainSass,
                tasks: ['sass:main','postcss:main'],
                options: {
                    livereload: true
                }
            },
            handlebars: {
                files: watchFiles.handlebars,
                tasks: ['handlebars:compile','uglify:main'],
                options: {
                    livereload: true
                }
            }
        }
    });

    // Dev task, run jshint, copy custom client side js scripts, then start server and watch
    grunt.registerTask('dev', [
        'newer:handlebars:compile',
        'newer:copy',
        'uglify',
        'concat',
        'newer:sass',
        'watch'
    ]);

    // build task, for initializing environment after clone or UI dependencies update
    grunt.registerTask('build', [
        'handlebars:compile',
        'copy',
        'uglify',
        'concat',
        'sass',
        'postcss'
    ]);

};
