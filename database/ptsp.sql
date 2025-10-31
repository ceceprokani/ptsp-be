/*
 Navicat Premium Data Transfer

 Source Server         : Server Local
 Source Server Type    : MariaDB
 Source Server Version : 101114
 Source Host           : localhost:3306
 Source Schema         : ptsp

 Target Server Type    : MariaDB
 Target Server Version : 101114
 File Encoding         : 65001

 Date: 28/10/2025 09:44:35
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for activity_log
-- ----------------------------
DROP TABLE IF EXISTS `activity_log`;
CREATE TABLE `activity_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NULL DEFAULT NULL,
  `level` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `ip_address` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `method` enum('create','update','delete') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `device` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `platform` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `browser` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `version` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `status` enum('success','failure','critical_failure') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'critical_failure',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `time` datetime NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `users_id`(`users_id`) USING BTREE,
  FULLTEXT INDEX `ip_address`(`ip_address`),
  CONSTRAINT `activity_log_ibfk_2` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of activity_log
-- ----------------------------

-- ----------------------------
-- Table structure for authentication_log
-- ----------------------------
DROP TABLE IF EXISTS `authentication_log`;
CREATE TABLE `authentication_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `ip_address` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `method` enum('normal','secretkey','unknown') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `device` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `platform` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `browser` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `version` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `time` datetime NOT NULL,
  `status` enum('success','wrong_password','critical_failure') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'critical_failure',
  `type` enum('signin','signout','change_password','edit_profile','twofactor') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  FULLTEXT INDEX `ip_address`(`ip_address`),
  CONSTRAINT `authentication_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 15 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of authentication_log
-- ----------------------------
INSERT INTO `authentication_log` VALUES (1, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '141.0.0.0', '2025-10-27 11:44:06', 'success', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (2, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '141.0.0.0', '2025-10-27 11:44:19', 'success', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (3, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '141.0.0.0', '2025-10-28 00:04:05', 'wrong_password', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (4, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '141.0.0.0', '2025-10-28 00:04:38', 'wrong_password', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (5, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '141.0.0.0', '2025-10-28 00:04:49', 'wrong_password', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (6, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '141.0.0.0', '2025-10-28 00:39:23', 'wrong_password', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (7, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '141.0.0.0', '2025-10-28 01:06:00', 'wrong_password', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (8, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '141.0.0.0', '2025-10-28 01:06:05', 'success', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (9, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '141.0.0.0', '2025-10-28 01:06:44', 'success', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (10, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '141.0.0.0', '2025-10-28 01:09:40', 'success', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (11, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '141.0.0.0', '2025-10-28 01:10:48', 'success', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (12, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '141.0.0.0', '2025-10-28 01:10:54', 'success', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (13, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '141.0.0.0', '2025-10-28 01:11:03', 'success', 'signin', NULL);
INSERT INTO `authentication_log` VALUES (14, 1, '::1', 'normal', 'Desktop', 'Windows', 'Chrome', '141.0.0.0', '2025-10-28 01:12:56', 'success', 'signin', NULL);

-- ----------------------------
-- Table structure for module_guestbook_event
-- ----------------------------
DROP TABLE IF EXISTS `module_guestbook_event`;
CREATE TABLE `module_guestbook_event`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `start_date` date NULL DEFAULT NULL,
  `end_date` date NULL DEFAULT NULL,
  `capacity` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `pic` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `description` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `user_id` int(11) NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  CONSTRAINT `module_guestbook_event_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of module_guestbook_event
-- ----------------------------

-- ----------------------------
-- Table structure for module_guestbook_event_member
-- ----------------------------
DROP TABLE IF EXISTS `module_guestbook_event_member`;
CREATE TABLE `module_guestbook_event_member`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guest_id` int(11) NULL DEFAULT NULL,
  `event_id` int(11) NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `guest_id`(`guest_id`) USING BTREE,
  INDEX `event_id`(`event_id`) USING BTREE,
  CONSTRAINT `module_guestbook_event_member_ibfk_1` FOREIGN KEY (`guest_id`) REFERENCES `module_guestbook_guest` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `module_guestbook_event_member_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `module_guestbook_event` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of module_guestbook_event_member
-- ----------------------------

-- ----------------------------
-- Table structure for module_guestbook_guest
-- ----------------------------
DROP TABLE IF EXISTS `module_guestbook_guest`;
CREATE TABLE `module_guestbook_guest`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `from` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `email` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `needs` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `address` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `date` datetime NULL DEFAULT NULL,
  `attachment` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of module_guestbook_guest
-- ----------------------------

-- ----------------------------
-- Table structure for module_letter_incoming
-- ----------------------------
DROP TABLE IF EXISTS `module_letter_incoming`;
CREATE TABLE `module_letter_incoming`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `sender` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `regarding` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `description` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `date` datetime NULL DEFAULT NULL,
  `attachment` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `status` enum('draft','assigned') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `user_id` int(11) NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  CONSTRAINT `module_letter_incoming_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 17 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of module_letter_incoming
-- ----------------------------
INSERT INTO `module_letter_incoming` VALUES (1, '123123', '123asdasda', 'asd', 'asdasdasd', '2025-10-14 00:00:00', NULL, 'draft', NULL, '2025-10-27 15:55:11', NULL, NULL);
INSERT INTO `module_letter_incoming` VALUES (2, '123123', '123asdasda', 'asd', 'asdasdasd', '2025-10-14 00:00:00', NULL, 'draft', NULL, '2025-10-27 15:58:09', NULL, NULL);
INSERT INTO `module_letter_incoming` VALUES (3, '123123', '123asdasda', 'asd', 'asdasdasd', '2025-10-14 00:00:00', 'http://localhost:4040/uploads/letter/incoming\\attachment-a1b0c127171d6b7d.png', 'draft', NULL, '2025-10-27 15:59:31', NULL, NULL);
INSERT INTO `module_letter_incoming` VALUES (4, '2045020394203', 'Agung Herkules', 'Surat Magang', 'Lorem ipsum', '2025-10-31 00:00:00', 'http://localhost:4040/uploads/letter/incoming\\attachment-c8f722dda6343d3a.pdf', 'draft', NULL, '2025-10-27 16:01:46', NULL, NULL);
INSERT INTO `module_letter_incoming` VALUES (6, '05012930184102', 'PT. Agus Subagja', 'Surat Magang Lanjutan', 'Lorem ipsum banget', '2025-10-16 00:00:00', 'http://localhost:4040/uploads/letter/incoming\\attachment-b584ce284259310f.pdf', 'draft', NULL, '2025-10-27 16:03:09', NULL, NULL);
INSERT INTO `module_letter_incoming` VALUES (7, '05012930184102', 'PT. Agus Subagja', 'Surat Magang Lanjutan', 'Lorem ipsum banget', '2025-10-16 00:00:00', 'http://localhost:4040/uploads/letter/incoming\\attachment-bd2fb8fa33f781be.pdf', 'draft', NULL, '2025-10-27 16:03:10', NULL, NULL);
INSERT INTO `module_letter_incoming` VALUES (8, '05012930184102', 'PT. Agus Subagja', 'Surat Magang Lanjutan', 'Lorem ipsum banget', '2025-10-16 00:00:00', 'http://localhost:4040/uploads/letter/incoming\\attachment-1d67ac5da642182d.pdf', 'draft', NULL, '2025-10-27 16:04:07', NULL, NULL);
INSERT INTO `module_letter_incoming` VALUES (9, '05012930184102', 'PT. Agus Subagja', 'Surat Magang Lanjutan', 'Lorem ipsum banget', '2025-10-16 00:00:00', 'http://localhost:4040/uploads/letter/incoming\\attachment-3d550170e103d457.pdf', 'draft', NULL, '2025-10-27 16:04:26', NULL, NULL);
INSERT INTO `module_letter_incoming` VALUES (10, '581239015401293013', 'PT. Bahagia', 'Surat Masuk', '-', '2025-10-09 00:00:00', 'http://localhost:4040/uploads/letter/incoming\\attachment-14e4ce82899c82b3.pdf', 'draft', NULL, '2025-10-27 16:07:16', NULL, NULL);
INSERT INTO `module_letter_incoming` VALUES (11, '50912039104910239', 'TES TEST', 'p0129041203', 'asdaksd', '2025-10-08 00:00:00', 'http://localhost:4040/uploads/letter/incoming\\attachment-a03d8620fe36af46.pdf', 'draft', NULL, '2025-10-27 16:11:16', NULL, NULL);
INSERT INTO `module_letter_incoming` VALUES (12, '50912039104910239', 'TES TEST', 'p0129041203', 'asdaksd', '2025-10-08 00:00:00', 'http://localhost:4040/uploads/letter/incoming\\attachment-b7dc035b4b9c6234.pdf', 'draft', NULL, '2025-10-27 16:11:19', NULL, NULL);
INSERT INTO `module_letter_incoming` VALUES (13, '50912039104910239', 'TES TEST', 'p0129041203', 'asdaksd', '2025-10-08 00:00:00', 'http://localhost:4040/uploads/letter/incoming\\attachment-ea8685bc9fe56094.pdf', 'draft', NULL, '2025-10-27 16:11:30', NULL, NULL);
INSERT INTO `module_letter_incoming` VALUES (14, '1241231', '1029349129091', 'a-1203-1024-', 'asldpaosdp[adl', '2025-10-16 00:00:00', 'http://localhost:4040/uploads/letter/incoming\\attachment-7ce2ffac70c8cec6.pdf', 'draft', NULL, '2025-10-27 16:14:27', NULL, NULL);
INSERT INTO `module_letter_incoming` VALUES (15, '1241231', '1029349129091', 'a-1203-1024-', 'asldpaosdp[adl', '2025-10-16 00:00:00', 'http://localhost:4040/uploads/letter/incoming\\attachment-026bd7e6a4ee42ec.pdf', 'draft', NULL, '2025-10-27 16:16:20', NULL, NULL);
INSERT INTO `module_letter_incoming` VALUES (16, '1241231', '1029349129091', 'a-1203-1024-', 'asldpaosdp[adl', '2025-10-16 00:00:00', 'http://localhost:4040/uploads/letter/incoming\\attachment-c4e403e0493b4b52.pdf', 'draft', NULL, '2025-10-27 16:17:03', NULL, NULL);

-- ----------------------------
-- Table structure for module_letter_incoming_assigned
-- ----------------------------
DROP TABLE IF EXISTS `module_letter_incoming_assigned`;
CREATE TABLE `module_letter_incoming_assigned`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `letter_incoming_id` int(11) NULL DEFAULT NULL,
  `user_id` int(11) NULL DEFAULT NULL,
  `note` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `letter_incoming_id`(`letter_incoming_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  CONSTRAINT `module_letter_incoming_assigned_ibfk_1` FOREIGN KEY (`letter_incoming_id`) REFERENCES `module_letter_incoming` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `module_letter_incoming_assigned_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of module_letter_incoming_assigned
-- ----------------------------

-- ----------------------------
-- Table structure for module_letter_outcoming
-- ----------------------------
DROP TABLE IF EXISTS `module_letter_outcoming`;
CREATE TABLE `module_letter_outcoming`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `proposer` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `regarding` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `purpose` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `description` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `date` datetime NULL DEFAULT NULL,
  `attachment` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `status` enum('draft','assigned') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `user_id` int(11) NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  CONSTRAINT `module_letter_outcoming_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of module_letter_outcoming
-- ----------------------------

-- ----------------------------
-- Table structure for role
-- ----------------------------
DROP TABLE IF EXISTS `role`;
CREATE TABLE `role`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) CHARACTER SET latin1 COLLATE latin1_bin NULL DEFAULT NULL,
  `name` varchar(100) CHARACTER SET latin1 COLLATE latin1_bin NULL DEFAULT NULL,
  `created_at` datetime NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP,
  `deleted` tinyint(1) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = latin1 COLLATE = latin1_bin ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of role
-- ----------------------------
INSERT INTO `role` VALUES (1, 'superadmin', 'Super Admin', '2025-10-27 10:48:37', '2025-10-27 10:49:06', NULL);
INSERT INTO `role` VALUES (2, 'teacher', 'Guru', '2025-10-27 10:48:37', '2025-10-27 10:48:56', NULL);
INSERT INTO `role` VALUES (3, 'staff', 'Staff TU', '2025-10-27 10:48:39', '2025-10-27 10:48:52', NULL);

-- ----------------------------
-- Table structure for user_staffs
-- ----------------------------
DROP TABLE IF EXISTS `user_staffs`;
CREATE TABLE `user_staffs`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NULL DEFAULT NULL,
  `name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `identity_number` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `email` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `address` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  CONSTRAINT `user_staffs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of user_staffs
-- ----------------------------

-- ----------------------------
-- Table structure for user_teachers
-- ----------------------------
DROP TABLE IF EXISTS `user_teachers`;
CREATE TABLE `user_teachers`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NULL DEFAULT NULL,
  `name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `identity_number` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `email` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `address` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  CONSTRAINT `user_teachers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of user_teachers
-- ----------------------------

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NULL DEFAULT NULL,
  `username` varchar(100) CHARACTER SET latin1 COLLATE latin1_bin NULL DEFAULT NULL,
  `email` varchar(100) CHARACTER SET latin1 COLLATE latin1_bin NULL DEFAULT NULL,
  `password` varchar(100) CHARACTER SET latin1 COLLATE latin1_bin NULL DEFAULT NULL,
  `name` varchar(100) CHARACTER SET latin1 COLLATE latin1_bin NULL DEFAULT NULL,
  `last_login` datetime NULL DEFAULT NULL,
  `created_at` datetime NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE CURRENT_TIMESTAMP,
  `deleted` tinyint(1) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `role_id`(`role_id`) USING BTREE,
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = latin1 COLLATE = latin1_bin ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 1, 'superadmin', 'superadmin@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$SnBGVmZhZE85akJTcVdHeA$n/ufdKvIZpwhsjXqz8bEvHrNh9ybZggWJ5zWUD259mw', 'Super Admin', NULL, NULL, '2025-10-27 11:40:48', NULL);

SET FOREIGN_KEY_CHECKS = 1;
