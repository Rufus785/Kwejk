SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `Comments` (
  `comment_id` int(11) NOT NULL,
  `image_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `Images` (
  `image_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `caption` text DEFAULT NULL,
  `image_url` varchar(255) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `Likes` (
  `like_id` int(11) NOT NULL,
  `image_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `Reports` (
  `report_id` int(11) NOT NULL,
  `image_id` int(11) NOT NULL,
  `reported_by` int(11) NOT NULL,
  `resolved_by` int(11) DEFAULT NULL,
  `reason` text NOT NULL,
  `is_resolved` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `resolved_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `Users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `profile_picture_url` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `Comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `fk_comments_image` (`image_id`),
  ADD KEY `fk_comments_user` (`user_id`);

ALTER TABLE `Images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `fk_images_user` (`user_id`);

ALTER TABLE `Likes`
  ADD PRIMARY KEY (`like_id`),
  ADD UNIQUE KEY `image_id` (`image_id`,`user_id`),
  ADD KEY `fk_likes_user` (`user_id`);

ALTER TABLE `Reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `fk_reports_image` (`image_id`),
  ADD KEY `fk_reports_reported_by` (`reported_by`),
  ADD KEY `fk_reports_resolved_by` (`resolved_by`);

ALTER TABLE `Users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

ALTER TABLE `Comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Likes`
  MODIFY `like_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Comments`
  ADD CONSTRAINT `fk_comments_image` FOREIGN KEY (`image_id`) REFERENCES `Images` (`image_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_comments_user` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `Images`
  ADD CONSTRAINT `fk_images_user` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `Likes`
  ADD CONSTRAINT `fk_likes_image` FOREIGN KEY (`image_id`) REFERENCES `Images` (`image_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_likes_user` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `Reports`
  ADD CONSTRAINT `fk_reports_image` FOREIGN KEY (`image_id`) REFERENCES `Images` (`image_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reports_reported_by` FOREIGN KEY (`reported_by`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reports_resolved_by` FOREIGN KEY (`resolved_by`) REFERENCES `Users` (`user_id`);
COMMIT;
