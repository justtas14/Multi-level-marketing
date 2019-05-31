String generateBuildName() {
    return scm.getUserRemoteConfigs()[0].getUrl().split(':')[1].split("\\.")[0].replace("/", "_") + '_' + env.BRANCH_NAME.replace("/", "_")
}
Boolean deployCommit() {
    result = sh (script: "git log -1 | grep '\\!deploy'", returnStatus: true)
    if (result == 0) {
        return true
    } else {
        return false
    }
}
String generateDeployedName() {
    return new Date().format( 'yyyyMMddHHmmss' )
}
pipeline {
    agent any
    environment {
        BUILD_ID = generateBuildName().take(62)
        TEST_ID = generateDeployedName()
        BUILD_URL = "https://builds.plumtreesystems.com/${BUILD_ID}/"
    }
    parameters {
        booleanParam(name: 'DEPLOY_REVIEW_BUILD', defaultValue: false, description: 'Deploy test build for PR review')
    }
    stages {
        stage('build') {
            agent {
                docker {
                    image 'plumtreesystems/composer_php'
                    args '-v composer_cache:/cache -e COMPOSER_CACHE_DIR=/cache -u root:sudo'
                    reuseNode true
                }
            }
            steps {
                sh 'composer install --verbose --prefer-dist --optimize-autoloader --no-progress --no-interaction'
                sh 'chmod u+x bin/console'
                sh 'bin/console cache:clear --no-warmup --env=prod'
            }
        }
        stage('test') {
            steps {
                sh "docker-compose -f docker-compose-test.yml -p \$TEST_ID build"
                sh "docker-compose -f docker-compose-test.yml -p \$TEST_ID up -d"
                sh "docker-compose -f docker-compose-test.yml -p \$TEST_ID exec -T prelaunchbuilder bash -c 'vendor/bin/phpcs'"
                sh "docker-compose -f docker-compose-test.yml -p \$TEST_ID exec -T prelaunchbuilder bash -c 'vendor/bin/simple-phpunit'"
            }
            post {
                cleanup {
                    sh "docker-compose -f docker-compose-test.yml -p \$TEST_ID kill"
                    sh "docker-compose -f docker-compose-test.yml -p \$TEST_ID down --rmi local"
                }
            }
        }
        stage('deploy-staging') {
            when {
                beforeAgent true
                branch 'dev'
            }
            environment {
                VIRTUAL_HOST = "prelaunchbuilder.*"
                DATABASE_URL = credentials('prelaunchbuilder_pts')
                MAILER_URL = 'smtp://smtp'
                APP_ENV = 'prod'
            }
            steps {
                sh "docker build --rm -t prelaunchbuilder --build-arg app_env=${APP_ENV} --build-arg db_url=${DATABASE_URL} --build-arg mailer_url=${MAILER_URL} ."
                sh "docker rm -f prelaunchbuilder && echo 'removed old container' || echo 'old container does not exist'"
                sh "docker run -dit -e DATABASE_URL=${DATABASE_URL} -e VIRTUAL_HOST=${VIRTUAL_HOST} --net dockernet --restart unless-stopped --name prelaunchbuilder prelaunchbuilder"
                sh "docker exec prelaunchbuilder bash -c 'bin/console doctrine:migration:migrate'"
            }
        }
        stage('deploy-review') {
            when {
                beforeAgent true
                expression { return params.DEPLOY_REVIEW_BUILD || deployCommit()}
            }
            environment {
                MAILER_URL = 'smtp://smtp'
                VIRTUAL_PATH_NAME = "${BUILD_ID}"
                APP_ENV = 'dev'
                NAME = "${BUILD_ID}"
            }
            steps {
                sh "docker-compose -f docker-compose-review.yml down --rmi local"
                sh "docker-compose -f docker-compose-review.yml build"
                sh "docker-compose -f docker-compose-review.yml up -d"
                sh "docker-compose -f docker-compose-review.yml exec -T prelaunchbuilder bash -c 'chmod 777 -R /var/www/html/var'"
                sh "docker-compose -f docker-compose-review.yml exec -T prelaunchbuilder bash -c 'cd public && ln -s . ${VIRTUAL_PATH_NAME}'"
                sh "docker-compose -f docker-compose-review.yml exec -T prelaunchbuilder bash -c 'composer reset-db'"
                sh "echo build deployed at ${BUILD_URL}"
            }
        }
    }
}
