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
        BUILD_ID = generateBuildName()
        TEST_ID = generateDeployedName()
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
                sh "rm .env && echo 'removed old .env file' || echo 'no .env file found'"
                sh "rm .env.php && echo 'removed old .env.php file' || echo 'no .env.php file found'"
                sh 'composer install --verbose --prefer-dist --optimize-autoloader --no-progress --no-interaction'
                sh 'chmod u+x bin/console'
                sh 'bin/console cache:clear --no-warmup --env=prod'
            }
        }
        stage('test') {
            agent {
                docker {
                    image 'plumtreesystems/symfony_test_env'
                    reuseNode true
                }
            }
            steps {
                sh 'rm -rf var/cache/test && echo "removed test cache" || echo no test cache'
                sh 'phpcs'
                sh 'ls vendor'
                sh 'vendor/bin/phpunit --configuration phpunit.xml.dist'
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
                sh "docker build --rm -t prelaunchbuilder --build-arg app_env=${APP_ENV} --build-arg db_url=${DATABASE_URL} --build-arg mailer_url=${MAILER_URL} --build-arg recaptcha_front_key=${RECAPTCHA_USR} --build-arg recaptcha_key=${RECAPTCHA_PSW} ."
                sh "docker rm -f prelaunchbuilder && echo 'removed old container' || echo 'old container does not exist'"
                sh "docker run -dit -e DATABASE_URL=${DATABASE_URL} -e VIRTUAL_HOST=${VIRTUAL_HOST} --net dockernet --restart unless-stopped --name prelaunchbuilder prelaunchbuilder"
                sh "docker exec prelaunchbuilder bash -c 'chmod -R 777 ./public/uploads'"
                sh "docker exec prelaunchbuilder bash -c 'bin/console doctrine:migration:migrate'"
            }
        }
        stage('deploy-review') {
            when {
                beforeAgent true
                expression { return params.DEPLOY_REVIEW_BUILD || deployCommit()}
            }
            environment {
                DATABASE_URL = "mysql://root@mysql_test_db/prelaunchbuilder_${BUILD_ID}"
                MAILER_URL = 'smtp://smtp'
                VIRTUAL_PATH_NAME = "${BUILD_ID}"
            }
            steps {
                sh "sed -i '/MAILER_URL=*/ c\\MAILER_URL=${MAILER_URL}' ./.env"
                sh "rm .env.php && echo 'removed old .env.php file' || echo 'no .env.php file found'"
                sh "docker build --rm -t prelaunchbuilder -f Dockerfile.dev ."
                sh "docker rm -f ${BUILD_ID} && echo 'removed old container' || echo 'old container does not exist'"
                sh "docker run -dit -e VIRTUAL_PATH_NAME=${VIRTUAL_PATH_NAME} -e DATABASE_URL=${DATABASE_URL} -e MAILER_URL=${MAILER_URL} --net dockernet --restart unless-stopped --name ${BUILD_ID} prelaunchbuilder"
                sh "docker exec ${BUILD_ID} bash -c 'composer reset-db'"
                sh "docker exec ${BUILD_ID} bash -c 'cd public && ln -s . ${BUILD_ID}'"
                sh "docker exec ${BUILD_ID} bash -c 'chmod -R 777 /var/www/html/var'"
                sh "docker exec ${BUILD_ID} bash -c 'chmod -R 777 /var/www/html/public/uploads'"
                sh "echo build deployed at http://builds.plumtreesystems.com/${VIRTUAL_PATH_NAME}/"
            }
        }

    post {
        always {
            sh "docker-compose -f docker-compose-test.yml -p \$TEST_ID down"
        }
    }
}
