# base.pp
stage { 'pre': before  => Stage['main'] }

class base {
    group { 'puppet':
        ensure => present,
    }

    user { 'vagrant':
        groups => [
            'sudo'
        ]
    }

    exec { 'apt-get -y update':
        alias  => 'aptupdate',
        path   => '/usr/bin',
        user   => 'root',
    }

}

class {'base': stage => pre}

class {'php5':}
php5::pkg { [
    'php5-intl',
    'php5-mcrypt',
    'php5-sqlite',
    'php5-xdebug',
    'php5-curl'
]:}

class {'apache2':}
apache2::vhost { 'flaming-archer.dev':
    port            => 80,
    docroot         => '/home/vagrant/sites/dev.flaming-archer/public',
    configfile_name => 'dev.flaming-archer'
}
