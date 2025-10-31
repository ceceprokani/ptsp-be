<?php
namespace Deployer;

require 'recipe/composer.php';

// Config

set('repository', 'git@github.com:oeltimacreation/ennijuliani-backend.git');

add('shared_files', ['.env']);
add('shared_dirs', ['storage', 'public/uploads']);
add('writable_dirs', []);

// Hosts

host('development')
    ->set('hostname', '51.79.161.59')
    ->set('port', 11511)
    ->set('remote_user', 'ubuntu')
    ->set('branch', 'master')
    ->set('deploy_path', '/var/www/pentasmob/apps/backend-dev');

host('production')
    ->set('hostname', '51.79.161.59')
    ->set('port', 11511)
    ->set('remote_user', 'ubuntu')
    ->set('branch', 'live')
    ->set('deploy_path', '/var/www/pentasmob/apps/backend-live');

// Tasks

task('reload:opcache', function () {
    run('sudo cachetool opcache:reset');
});

// Hooks

after('deploy', 'reload:opcache');
after('deploy:failed', 'deploy:unlock');
