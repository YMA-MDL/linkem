module.exports = function(grunt)
{
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		phpcs: {
			options: {
				bin: 'vendor/bin/phpcs',
				standard: 'psr2'
			},

			classes: {
				dir: ['src/**/*.php']
			},

			tests: {
				dir: ['tests/**/*.php']
			}
		},

		phpunit: {
			test: {
				options: {
					bin: 'vendor/bin/phpunit'
				}
			}
		},

		watch: {
			options: {
				reload: true
			},
			
			config: {
				files: 'Gruntfile.js'
			},

			classes: {
				files: 'src/**/*.php',
				tasks: ['phpunit:test', 'phpcs:classes']
			},

			tests: {
				files: 'tests/**/*.php',
				tasks: ['phpunit:test', 'phpcs:tests']
			}
		}
	})

	grunt.loadNpmTasks('grunt-contrib-watch')
	grunt.loadNpmTasks('grunt-phpcs')
	grunt.loadNpmTasks('grunt-phpunit')

	grunt.registerTask('default', ['watch'])
	grunt.registerTask('test', ['phpunit:test'])
}