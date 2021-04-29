DROP TABLE IF EXISTS `newsletter_confirmation`;

DELETE `message_i18n` FROM `message_i18n` INNER JOIN `message` ON `message_i18n`.`id` = `message`.`id` WHERE `message`.`name` = 'newsletter_email_confirmation';
DELETE `message_version` FROM `message_version` INNER JOIN `message` ON `message_version`.`id` = `message`.`id` WHERE `message`.`name` = 'newsletter_email_confirmation';
DELETE FROM `message` WHERE `name` = 'newsletter_email_confirmation';