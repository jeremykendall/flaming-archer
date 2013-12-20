BEGIN TRANSACTION;

    ALTER TABLE `users` RENAME TO `users_tmp`;

    CREATE TABLE `users` (
        `id` INTEGER PRIMARY KEY AUTOINCREMENT,
        `email` TEXT NOT NULL,
        `emailCanonical` TEXT NOT NULL,
        `passwordHash` TEXT NOT NULL,
        `role` TEXT NOT NULL,
        `lastLogin` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
        UNIQUE (`email`)
    );

    INSERT INTO `users` (`email`, `emailCanonical`, `passwordHash`, `role`, `lastLogin`)
        SELECT `email`, `emailCanonical`, `passwordHash`, 'admin', `lastLogin` FROM `users_tmp`;

    DROP TABLE `users_tmp`;

END TRANSACTION;
