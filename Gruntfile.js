module.exports = function(grunt) {

    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        /**
         * Global vars
         */
        project : {
            resources : 'resources',
            assets    : 'web/assets',
            modules   : 'node_modules',
            dist : {
                js : 'web/assets/js/app.min.js'
            }
        },

        /**
         * Para monitorizar los archivos
         */
        watch : {
            css : {
                files : ['<%= project.resources %>/css/{,*/}*.css'],
                tasks : ['cssmin'],
                options : {
                    livereload : true
                }
            },
            less : {
                files : ['<%= project.resources %>/less/{,*/}*.less'],
                tasks : ['less:web'],
                options : {
                    livereload : true
                }
            },
            js : {
                files : ['<%= project.resources %>/js/{,*/}*.js'],
                tasks : ['concat', 'copy:extra_js_modules', 'eslint'],
                options: {
                    livereload : true
                }
            },
            img : {
                files : ['<%= project.resources %>/img/{,*/}*'],
                tasks : ['imagemin', 'copy:extra_images'],
                options: {
                    livereload : true
                }
            }
        },

        cssmin: {
            web: {
                files: [{
                    expand: true,
                    cwd: '<%= project.resources %>/css',
                    src: ['*.css'],
                    dest: '<%= project.assets %>/css',
                    ext: '.min.css'
                }]
            }
        },

        uglify: {
            // individual: {
            //     files: [{
            //         expand: true,
            //         cwd: '<%= project.resources %>/js',
            //         src: '*.js',
            //         dest: '<%= project.assets %>/js',
            //         ext: '.min.js'
            //     }]
            // },
            all: {
                files: [{
                    src: '<%= project.dist.js %>',
                    dest: '<%= project.dist.js %>'
                }]
            }
        },

        imagemin: {
            dynamic: {
                files: [{
                    expand: true,
                    cwd: '<%= project.resources %>/img',
                    src: ['**/*.{png,jpg,gif}'],
                    dest: '<%= project.assets %>/img'
                }]
            }
        },

        copy : {
            extra_images : {
                expand: true,
                cwd: '<%= project.resources %>/img',
                src: ['*.svg'],
                dest : '<%= project.assets %>/img'

            },
            fonts : {
                expand: true,
                cwd: '<%= project.resources %>/less/icons',
                src : '**',
                dest : '<%= project.assets %>/css/icons'

            }//,
            // jquery_plugins : {
            //     files : [
            //         {expand: true, cwd: '<%= project.modules %>/datatables/media/js', src: 'jquery.dataTables.min.js', dest: '<%= project.assets %>/js/vendor/dataTables/'},
            //         {expand: true, cwd: '<%= project.modules %>/datatables/media/css', src: 'jquery.dataTables.min.css', dest: '<%= project.assets %>/js/vendor/dataTables/css/'},
            //         {expand: true, cwd: '<%= project.modules %>/datatables/media/images', src: '**', dest: '<%= project.assets %>/js/vendor/dataTables/images/'},
            //     ]
            // },
            // jquery : {
            //     files : [
            //         {expand: true, cwd: '<%= project.modules %>/jquery/dist', src: 'jquery.min.js', dest: '<%= project.assets %>/js/vendor/'}
            //     ]
            // },
            //graph : {
            //    files : [
            //        {expand: true, cwd: '<%= project.modules %>/chart.js', src: 'Chart.min.js', dest: '<%= project.assets %>/js/vendor/'}
            //    ]
            //},
            //bootstrap : {
            //    files : [
            //        {expand: true, cwd: '<%= project.modules %>/bootstrap/dist/', src: '**', dest: '<%= project.assets %>/js/vendor/bootstrap/'}
            //    ]
            //},
            // extra_js_modules : {
            //     files :[{expand: true, cwd: '<%= project.resources %>/js/vendor', src: ['**'], dest: '<%= project.assets %>/js/vendor'}]
            // }
        },
        concat: {
            options: {
                separator: ';\n',
                stripBanners : true,
                process: true
            },
            web: {
                nonull: true,
                src: [
                    '<%= project.modules %>/jquery/dist/jquery.min.js',

                    '<%= project.modules %>/js-cookie/src/js.cookie.js',

                    '<%= project.resources %>/js/js-core/jquery-ui-core.js',
                    '<%= project.resources %>/js/js-core/jquery-ui-widget.js',
                    '<%= project.resources %>/js/js-core/jquery-ui-position.js',
                    '<%= project.resources %>/js/vendor/button-ui/button.js',
                    '<%= project.resources %>/js/vendor/dialog/dialog.js',
                    '<%= project.resources %>/js/vendor/datepicker-ui/datepicker.js',
                    '<%= project.resources %>/js/vendor/charts/piegage/piegage.js',

                    '<%= project.resources %>/js/vendor/multi-upload/jquery.fileupload.js',

                    '<%= project.resources %>/js/vendor/datatable/datatable.js',
                    '<%= project.resources %>/js/vendor/datatable/datatable-bootstrap.js',

                    '<%= project.resources %>/js/vendor/dropdown/dropdown.js',
                    '<%= project.resources %>/js/vendor/superclick/superclick.js',
                    '<%= project.resources %>/js/vendor/nicescroll/nicescroll.js',
                    '<%= project.resources %>/js/vendor/uniform/uniform.js',
                    '<%= project.resources %>/js/vendor/input-switch/inputswitch.js',
                    '<%= project.resources %>/js/vendor/chosen/chosen.js',
                    '<%= project.resources %>/js/app.js'
                ],
                dest: '<%= project.dist.js %>'
            }
        },
        less: {
            web : {
                options: {
                    compress : true,
                    paths: ['<%= project.resources %>/less']
                },

                files: {
                    '<%= project.assets %>/css/theme.min.css': '<%= project.resources %>/less/main.less'
                }
            }
        },

        clean: {
            css: ['<%= project.assets %>/css'],
            js: ['<%= project.assets %>/js']
        },

        eslint: {
            target: ['<%= project.resources %>/js/*.js']
        }
    });

    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-imagemin');
    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-eslint');


    grunt.registerTask('package', [
        'clean',
        'cssmin',
        'less',
        'copy',
        'concat',
        'uglify',
        'imagemin'
    ]);

    grunt.registerTask('debug', [
        'clean',
        'cssmin',
        'less',
        'copy',
        'concat',
        'imagemin',
        'watch'
    ]);

    grunt.registerTask('default', [
        'clean',
        'cssmin',
        'less',
        'copy',
        'concat',
        'uglify',
        'imagemin',
    ]);
};
