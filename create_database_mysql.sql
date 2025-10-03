USE `projet`;

CREATE TABLE `users` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`username` VARCHAR(50) NOT NULL,
	`password_hash` VARCHAR(255) NOT NULL,
	`email` VARCHAR(50) NOT NULL,
	`created_at` VARCHAR(50) NOT NULL,

	PRIMARY KEY (`id`)
);

CREATE TABLE `roles` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(50) NOT NULL,
	`description` VARCHAR(255) NOT NULL,

	PRIMARY KEY (`id`)
);

CREATE TABLE `permissions` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(50) NOT NULL,
	`description` VARCHAR(255) NOT NULL,

	PRIMARY KEY (`id`)
);

CREATE TABLE `role_user` (
    `user_id` INT(11) NOT NULL,
    `role_id` INT(11) NOT NULL,
    
    PRIMARY KEY (`user_id`, `role_id`),
    CONSTRAINT `fk_role_user_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_role_user_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
);

CREATE TABLE `permission_role` (
    `role_id` INT(11) NOT NULL,
    `permission_id` INT(11) NOT NULL,
    
    PRIMARY KEY (`role_id`, `permission_id`),
    CONSTRAINT `fk_permission_role_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_permission_role_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
);
