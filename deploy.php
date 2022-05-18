<?php

namespace Deployer;

require 'recipe/laravel.php';
require 'contrib/php-fpm.php';
require 'contrib/npm.php';

set('application', 'rucred-dev.ru');
set('repository', 'git@bitbucket.org:rukred/rucred-crm.git');
set('branch', 'master');
set('default_stage', 'stage');
set('php_fpm_version', '8.0');
set('ssh_arguments', ['-o UserKnownHostsFile=/dev/null', '-o StrictHostKeyChecking=no']);
set('shared_dirs', ['configuration']);

host('51.250.98.13')
    ->set('remote_user', 'ploi')
    ->set('deploy_path', '/home/ploi/rucred-dev.ru');

task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'deploy:publish',
]);

after('deploy:failed', 'deploy:unlock');
