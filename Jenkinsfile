pipeline {
    agent {
        docker {
            image 'brasegon/ubuntu-amq:php8'
            args '-v $PWD:/app -w /app -u root'
        }
    }
    stages {
        stage('Git clone') {
            steps {
                git branch: 'master', credentialsId: 'test', url: 'git@github.com:Valounte/workee_backend.git'
            }
        }
        stage('Install dependencies') {
            steps {
                dir("workee_backend") {
                    sh 'composer install && cd tools/php-cs-fixer && composer install'
                }
            }
        }
        stage('Run PHP CS Fixer') {
            steps {
                dir("workee_backend") {
                    sh 'tools/php-cs-fixer/vendor/bin/php-cs-fixer fix src --diff --dry-run'
                }
            }
        }
        stage('Launch php stan') {
            steps {
                dir("workee_backend") {
                    sh 'vendor/bin/phpstan analyse src --level 3'
                }
            }
        }
        stage('Launch Unit Test') {
              steps {
                  dir("workee_backend") {
                      sh 'export SYMFONY_DEPRECATIONS_HELPER=weak && vendor/bin/phpunit'
                  }
              }
        }
    }
}
