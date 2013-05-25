CREATE TABLE IF NOT EXISTS `images` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `day` INT unsigned NOT NULL,
  `photo_id` INT unsigned NOT NULL,
  `posted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `users` (
  `id` INTEGER PRIMARY KEY,
  `firstName` TEXT NOT NULL,
  `lastName` TEXT NOT NULL,
  `email` TEXT NOT NULL,
  `emailCanonical` TEXT NOT NULL,
  `flickrUsername` TEXT NOT NULL,
  `flickrApiKey` TEXT NOT NULL,
  `externalUrl` TEXT NOT NULL,
  `passwordHash` TEXT NOT NULL,
  `lastLogin` timestamp NULL
);
