CREATE USER 'testuser'@'localhost' IDENTIFIED BY 'testpass';
GRANT SELECT, INSERT, UPDATE, DELETE on `flaming_archer`.* TO 'testuser'@'localhost';