set :application, 'flaming-archer'
set :repo_url, 'git@github.com:jeremykendall/flaming-archer.git'

set :branch, 'develop'

set :deploy_to, '/var/www/net.jeremykendall.365'
set :deploy_via, :remote_cache
set :scm, :git

# Seems to require 'role :app'
set :linked_files, %w{config/local.php db/flaming-archer.db public/feed.xml}
set :linked_dirs, %w{logs templates/cache}

set :keep_releases, 5

namespace :deploy do

    desc 'composer install'
    task :composer_install do
        on roles(:web) do
            within release_path do
                execute 'composer', 'install', '--no-dev', '--optimize-autoloader'
            end
        end
    end

    after :updated, 'deploy:composer_install'

    desc 'Restart application - does nothing, see comments below'
    task :restart do
        on roles(:app), in: :sequence, wait: 5 do
            # This is present b/c 'cap production deploy' is blowing up w/o it.
            # Not sure what's up with that, the Google hasn't helped, and I'm tired
            # of screwing with it.  It stays in for now.
        end
    end

end
