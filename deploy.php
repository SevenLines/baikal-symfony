<?php
namespace Deployer;
require 'recipe/symfony.php';

// Configuration

set('ssh_type', 'native');
set('ssh_multiplexing', true);
set('dump_assets', true);
set('bin_dir', 'bin');

set('repository', 'https://github.com/SevenLines/baikal-symfony.git');

add('shared_files', []);
add('shared_dirs', []);

add('writable_dirs', []);

// Servers
server('production', '83.220.170.91')
    ->user('light')
    ->port('50212')
    ->identityFile()
    ->set('deploy_path', '~/projects/baikal-symfony')
    ->pty(true);

// Tasks
desc('Restart PHP-FPM service');
task('php-fpm:restart', function () {
    // The user must have rights for restart service
    // /etc/sudoers: username ALL=NOPASSWD:/bin/systemctl restart php-fpm.service
    run('sudo systemctl restart php7-fpm.service');
});


after('deploy:symlink', 'php-fpm:restart');

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.
before('deploy:symlink', 'database:migrate');
