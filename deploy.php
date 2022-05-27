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
set('shared_dirs', ['configuration', 'files']);

host('51.250.98.13')
    ->set('remote_user', 'ploi')
    ->set('deploy_path', '/home/ploi/rucred-dev.ru');

task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'deploy:publish',
    'files:link',
]);

task('files:link', function () {
    cd('{{release_or_current_path}}');
    run('ln -s /home/ploi/rucred-dev.ru/current/files /home/ploi/rucred-dev.ru/current/public/files');
});

after('deploy:failed', 'deploy:unlock');
