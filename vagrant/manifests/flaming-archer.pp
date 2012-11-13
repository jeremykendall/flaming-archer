class flaming-archer {
    mysql::db { 'flaming_archer':
        username => 'testuser',
        password => 'testpass'
    }
}
