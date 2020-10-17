<?php
namespace Deployer;

require 'recipe/common.php';

// Project name
set('application', 'attendance_app');

// Project repository
set('repository', 'git@github.com:syrissel/attendance-app.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true); 

// Shared files/dirs between deploys 
set('shared_files', []);
set('shared_dirs', []);

// Writable dirs by web server 
set('writable_dirs', []);


// Hosts

host('attendance-app@attendanceapp.stephanemireault.ca')
    ->set('deploy_path', '~/attendance-app');    
    

// Tasks

desc('Deploy your project');
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:writable',
    'deploy:vendors',
    'add-env',
    'run-npm',
    'deploy:clear_paths',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'success'
]);

task('restart-apache-fpm', function () {
    run('service apache2 restart');
});

task('add-env', function () {
    run("bash -c \"printf '%s\n%s\n%s\n%s\n%s\n' 'DB_HOST=localhost' 'DB_NAME=clockin_db' 'DB_USER=clockin_app' 'DB_PASS=5drLJQuHZ9S3fOi0t7uh' 'ROOT=/' > .env\"");
    run("mv .env /home/attendance-app/attendance-app/current");
});

task('run-npm', function () {
    run('cd /home/attendance-app/attendance-app/current; npm install');
    run('cd /home/attendance-app/attendance-app/current; npm run build');
});

// [Optional] If deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
