CREATE USER 'testuser'@'localhost' IDENTIFIED BY 'testpass';
GRANT SELECT, INSERT, UPDATE, DELETE on `365`.* TO 'testuser'@'localhost';
