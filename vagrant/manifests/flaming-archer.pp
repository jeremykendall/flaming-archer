class flaming-archer {

    exec { 'create-db':
        unless  => 'mysql -u root flaming_archer',
        path    => ['/bin', '/usr/bin'],
        command => 'mysql -u root -e "CREATE DATABASE flaming_archer;"',
        require => Service['mysql']
    }
}
