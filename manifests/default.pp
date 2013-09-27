group { 'puppet': ensure => present }
Exec { path => [ '/bin/', '/sbin/', '/usr/bin/', '/usr/sbin/' ] }
File { owner => 0, group => 0, mode => 0644 }

class {'apt':
  always_apt_update => true,
}

Class['::apt::update'] -> Package <|
    title != 'python-software-properties'
and title != 'software-properties-common'
|>

    apt::key { '4F4EA0AAE5267A6C': }

apt::ppa { 'ppa:ondrej/php5-oldstable':
  require => Apt::Key['4F4EA0AAE5267A6C']
}

class { 'puphpet::dotfiles': }

package { [
    'build-essential',
    'vim',
    'curl',
    'git-core',
    'python',
    'graphviz'
  ]:
  ensure  => 'installed',
}

class { 'apache': }

apache::dotconf { 'custom':
  content => 'EnableSendfile Off',
}

apache::module { 'rewrite': }

exec { "change_httpd_user":
    command => "sed -i 's/www-data/vagrant/g' /etc/apache2/envvars",
    onlyif => "/bin/grep -q 'www-data' '/etc/apache2/envvars'",
    notify => Service['apache2'],
    require => Package['apache2'],
}

file { "/var/lock/apache2":
    ensure => "directory",
    owner => "vagrant",
    group => "vagrant",
    require => Exec['change_httpd_user'],
}

apache::vhost { 'flaming-archer.dev':
  server_name   => 'flaming-archer.dev',
  serveraliases => [
],
  docroot       => '/var/www/public/',
  port          => '80',
  env_variables => [
    'SLIM_MODE development'
  ],
  priority      => '1',
}

class { 'php':
  service             => 'apache',
  service_autorestart => false,
  module_prefix       => '',
}

php::module { 'php5-cli': }
php::module { 'php5-curl': }
php::module { 'php5-intl': }
php::module { 'php5-mcrypt': }
php::module { 'php5-sqlite': }
php::module { 'php-apc': }

class { 'php::devel':
  require => Class['php'],
}

class { 'php::pear':
  require => Class['php'],
}

$xhprofPath = '/var/www/xhprof'

php::pecl::module { 'xhprof':
  use_package     => false,
  preferred_state => 'beta',
}

if !defined(Package['git-core']) {
  package { 'git-core' : }
}

vcsrepo { $xhprofPath:
  ensure   => present,
  provider => git,
  source   => 'https://github.com/facebook/xhprof.git',
  require  => Package['git-core']
}

file { "${xhprofPath}/xhprof_html":
  ensure  => 'directory',
  owner   => 'vagrant',
  group   => 'vagrant',
  mode    => '0775',
  require => Vcsrepo[$xhprofPath]
}

composer::run { 'xhprof-composer-run':
  path    => $xhprofPath,
  require => [
    Class['composer'],
    File["${xhprofPath}/xhprof_html"]
  ]
}

composer::run { 'flaming-archer-run':
  path    => '/var/www',
  require => [
    Class['composer'],
  ]
}

apache::vhost { 'xhprof':
  server_name => 'xhprof',
  docroot     => "${xhprofPath}/xhprof_html",
  port        => 80,
  priority    => '1',
  require     => [
    Php::Pecl::Module['xhprof'],
    File["${xhprofPath}/xhprof_html"]
  ]
}

class { 'xdebug':
  service => 'apache',
}

class { 'composer':
  require => Package['php5', 'curl'],
}

puphpet::ini { 'xdebug':
  value   => [
    'xdebug.default_enable = 1',
    'xdebug.remote_autostart = 0',
    'xdebug.remote_connect_back = 1',
    'xdebug.remote_enable = 1',
    'xdebug.remote_handler = "dbgp"',
    'xdebug.remote_port = 9000',
    'xdebug.profiler_enable = 0',
    'xdebug.profiler_enable_trigger = 1',
    'xdebug.profiler_output_name = cachegrind.out.%H_%R.%r'
  ],
  ini     => '/etc/php5/conf.d/zzz_xdebug.ini',
  notify  => Service['apache'],
  require => Class['php'],
}

puphpet::ini { 'php':
  value   => [
    'date.timezone = "UTC"'
  ],
  ini     => '/etc/php5/conf.d/zzz_php.ini',
  notify  => Service['apache'],
  require => Class['php'],
}

puphpet::ini { 'custom':
  value   => [
    'display_errors = On',
    'error_reporting = -1',
    'display_startup_errors = 1'
  ],
  ini     => '/etc/php5/conf.d/zzz_custom.ini',
  notify  => Service['apache'],
  require => Class['php'],
}

apache::vhost { 'webgrind':
  server_name   => 'webgrind',
  serveraliases => [
],
  docroot       => '/var/www/webgrind',
  port          => '80',
  env_variables => [
  ],
  priority      => '1',
}

apache::vhost { 'apc':
  server_name   => 'apc',
  serveraliases => [
],
  docroot       => '/var/www/apc',
  port          => '80',
  env_variables => [
  ],
  priority      => '1',
}
