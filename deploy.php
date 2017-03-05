<?php
namespace Deployer;
use Deployer\Type\Csv;

require 'recipe/symfony3.php';

// Configuration

set('ssh_type', 'native');
set('ssh_multiplexing', true);
set('dump_assets', true);

set('repository', 'https://github.com/SevenLines/baikal-symfony.git');

add('shared_files', []);
add('shared_dirs', []);

add('writable_dirs', []);
set('keep_releases', 5);

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


/**
 * Return list of releases on server.
 */
set('releases_list', function () {
    cd('{{deploy_path}}');

    // If there is no releases return empty list.
    if (!run('[ -d releases ] && [ "$(ls -A releases)" ] && echo "true" || echo "false"')->toBool()) {
        return [];
    }

    // Will list only dirs in releases.
    $list = run('cd releases && ls -t -d -1 */')->toArray();

    // Prepare list.
    $list = array_map(function ($release) {
        return basename(rtrim(trim($release), '/'));
    }, $list);

    $releases = []; // Releases list.

    // Collect releases based on .dep/releases info.
    // Other will be ignored.

    if (run('if [ -f .dep/releases ]; then echo "true"; fi')->toBool()) {
        $keepReleases = get('keep_releases');
        if ($keepReleases === -1) {
            $csv = run('cat .dep/releases');
        } else {
            // Instead of `tail -n` call here can be `cat` call,
            // but on servers with a lot of deploys (more 1k) it
            // will output a really big list of previous releases.
            // It spoils appearance of output log, to make it pretty,
            // we limit it to `n*2 + 5` lines from end of file (15 lines).
            $csv = run("tail -n " . ($keepReleases * 2 + 5) . " .dep/releases");
        }

        $metainfo = Csv::parse($csv);

        for ($i = count($metainfo) - 1; $i >= 0; --$i) {
            if (is_array($metainfo[$i]) && count($metainfo[$i]) >= 2) {
                list($date, $release) = $metainfo[$i];
                $index = array_search($release, $list, true);
                if ($index !== false) {
                    $releases[] = $release;
                    unset($list[$index]);
                }
            }
        }
    }

    return $releases;
});

