
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- newsletter_confirmation
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `newsletter_confirmation`;

CREATE TABLE `newsletter_confirmation`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `confirmation_token` VARCHAR(255) NOT NULL,
    `newsletter_id` INTEGER NOT NULL,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `fi_newsletter_confirmation_id` (`newsletter_id`),
    CONSTRAINT `fk_newsletter_confirmation_id`
        FOREIGN KEY (`newsletter_id`)
        REFERENCES `newsletter` (`id`)
        ON UPDATE RESTRICT
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
