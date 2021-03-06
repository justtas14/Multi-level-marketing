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
        stage('build-js') {
            agent {
                docker {
                    image 'node:11.15.0-stretch'
                    reuseNode true
                }
            }
            steps {
                sh 'npm install'
                sh 'npm run build'
            }
        }
        stage('test') {
            agent {
                docker {
                    image 'plumtreesystems/symfony_test_env'
                    args '-v prelaunchbuilder_phpunit:/cache -e COMPOSER_CACHE_DIR=/cache -u root:sudo'
                    reuseNode true
                }
            }
            steps {
                sh 'vendor/bin/phpcs'
                sh 'vendor/bin/simple-phpunit'
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
                INVITATION_SENDER = 'noreply@plumtreesystems.com'
            }
            steps {
                sh "docker build --rm -t prelaunchbuilder --build-arg app_env=${APP_ENV} --build-arg db_url=${DATABASE_URL} ."
                sh "docker rm -f prelaunchbuilder && echo 'removed old container' || echo 'old container does not exist'"
                sh "docker run -dit -v prelaunch_staging_media:/var/www/html/public/files -e MAILER_URL=${MAILER_URL} -e INVITATION_SENDER=${INVITATION_SENDER} -e DATABASE_URL=${DATABASE_URL} -e VIRTUAL_HOST=${VIRTUAL_HOST} --net dockernet --restart unless-stopped --name prelaunchbuilder prelaunchbuilder"
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
        stage('push-dev-to-aws') {
            when {
                branch 'master'
            }
            environment {
                APP_ENV = 'prod'
                APP_DEBUG = '0'
                URL_BASEPATH = '/'
                DOCKER_TAG = "master-${GIT_COMMIT}"
            }
            steps {
                sh "docker build --rm -f Dockerfile --build-arg app_version=${GIT_COMMIT} --build-arg app_env=${APP_ENV} --build-arg app_debug=${APP_DEBUG} --build-arg url_basepath=${URL_BASEPATH} -t 643652872181.dkr.ecr.eu-west-2.amazonaws.com/prelaunchbuilder:${DOCKER_TAG} ."
                withDockerRegistry([credentialsId: 'ecr:eu-west-2:plumtree_aws', url: 'https://643652872181.dkr.ecr.eu-west-2.amazonaws.com']) {
                    sh "docker push 643652872181.dkr.ecr.eu-west-2.amazonaws.com/prelaunchbuilder:${DOCKER_TAG}"
                }
            }
        }
    }
}
