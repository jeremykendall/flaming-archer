BEGIN TRANSACTION;

    ALTER TABLE `images` RENAME TO `images_tmp`;

    CREATE TABLE `images` (
        `id` INTEGER PRIMARY KEY AUTOINCREMENT,
        `day` INT unsigned NOT NULL,
        `photoId` INT unsigned NOT NULL,
        `posted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        UNIQUE (`day`),
        UNIQUE (`photoId`)
    );

    INSERT INTO images (`day`, `photoId`, `posted`)
        SELECT `day`, `photo_id`, `posted` FROM `images_tmp`;

    ALTER TABLE `users` RENAME TO `users_tmp`;

    CREATE TABLE `users` (
        `id` INTEGER PRIMARY KEY AUTOINCREMENT,
        `email` TEXT NOT NULL,
        `emailCanonical` TEXT NOT NULL,
        `passwordHash` TEXT NOT NULL,
        `lastLogin` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
        UNIQUE (`email`)
    );

    INSERT INTO `users` (`email`, `emailCanonical`, `passwordHash`, `lastLogin`)
        SELECT `email`, lower(`email`), `password_hash`, `last_login` FROM `users_tmp`;

    DROP TABLE `images_tmp`;
    DROP TABLE `users_tmp`;

END TRANSACTION;
