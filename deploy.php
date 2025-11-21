<?php
namespace Deployer;

require 'recipe/laravel.php';

// Config

set('repository', 'https://github.com/dhanifudin/laravel-todo.git');

add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

// Hosts

host('194.127.193.198')
    ->set('remote_user', 'el')
    ->set('deploy_path', '/var/www/todo.dhanifudin.com');

// Hooks

after('deploy:failed', 'deploy:unlock');
