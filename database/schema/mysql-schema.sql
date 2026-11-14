/*M!999999\- enable the sandbox mode */ 
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `applications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `first_name` varchar(75) DEFAULT NULL,
  `middle_name` varchar(75) DEFAULT NULL,
  `last_name` varchar(75) DEFAULT NULL,
  `suffix` varchar(20) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone` text NOT NULL,
  `home_address` text NOT NULL,
  `birth_date` date NOT NULL,
  `gender` varchar(255) NOT NULL,
  `civil_status` varchar(255) NOT NULL,
  `education` varchar(255) NOT NULL,
  `job_type` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `resume` varchar(255) NOT NULL,
  `coverletter` varchar(255) NOT NULL,
  `appletter` varchar(255) NOT NULL,
  `interview_date` date DEFAULT NULL,
  `interview_time` varchar(255) DEFAULT NULL,
  `interview_location` varchar(255) DEFAULT NULL,
  `interview_response` varchar(32) DEFAULT NULL,
  `interview_response_reason` text DEFAULT NULL,
  `interview_note` text DEFAULT NULL,
  `interview_completed_at` timestamp NULL DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `resume_original_name` varchar(255) DEFAULT NULL,
  `coverletter_original_name` varchar(255) DEFAULT NULL,
  `appletter_original_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `applications_user_id_unique` (`user_id`),
  CONSTRAINT `applications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `attendance_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendance_records` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `date` date NOT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `hours_worked` decimal(5,2) DEFAULT NULL,
  `status` varchar(255) NOT NULL,
  `archived` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `attendance_records_employee_id_date_unique` (`employee_id`,`date`),
  KEY `attendance_records_archived_date_index` (`archived`,`date`),
  KEY `attendance_records_status_index` (`status`),
  CONSTRAINT `attendance_records_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `banners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `banners` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `placement` varchar(255) NOT NULL DEFAULT 'admin',
  `path` varchar(255) NOT NULL,
  `alt` varchar(255) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `uploaded_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `banners_uploaded_by_foreign` (`uploaded_by`),
  KEY `banners_placement_index` (`placement`),
  CONSTRAINT `banners_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `biometric_sync_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `biometric_sync_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `calendar_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `calendar_events` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` date NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `calendar_events_created_by_foreign` (`created_by`),
  KEY `calendar_events_event_date_index` (`event_date`),
  CONSTRAINT `calendar_events_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `dashboard_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `dashboard_tasks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `task_date` date NOT NULL,
  `title` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dashboard_tasks_user_id_task_date_index` (`user_id`,`task_date`),
  CONSTRAINT `dashboard_tasks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `employee_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `submission_box_id` bigint(20) unsigned DEFAULT NULL,
  `employee_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `size_bytes` bigint(20) unsigned DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Pending',
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_documents_submission_box_id_foreign` (`submission_box_id`),
  KEY `employee_documents_employee_id_foreign` (`employee_id`),
  KEY `employee_documents_reviewed_by_foreign` (`reviewed_by`),
  KEY `employee_documents_status_index` (`status`),
  CONSTRAINT `employee_documents_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_documents_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_documents_submission_box_id_foreign` FOREIGN KEY (`submission_box_id`) REFERENCES `submission_boxes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `employees` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `employee_id` varchar(255) NOT NULL,
  `zkteco_user_id` varchar(50) DEFAULT NULL,
  `first_name` varchar(75) DEFAULT NULL,
  `middle_name` varchar(75) DEFAULT NULL,
  `last_name` varchar(75) DEFAULT NULL,
  `suffix` varchar(20) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  `position` varchar(150) DEFAULT NULL,
  `department` varchar(255) NOT NULL,
  `role_type` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` text DEFAULT NULL,
  `birthday` date NOT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `civil_status` varchar(255) DEFAULT NULL,
  `address` text NOT NULL,
  `education` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`education`)),
  `emergency_name` text NOT NULL,
  `emergency_contact` text NOT NULL,
  `employment_status` varchar(255) NOT NULL,
  `step_no` smallint(5) unsigned NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `sss_number` text DEFAULT NULL,
  `philhealth_number` text DEFAULT NULL,
  `pagibig_number` text DEFAULT NULL,
  `tin` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employees_employee_id_unique` (`employee_id`),
  UNIQUE KEY `employees_email_unique` (`email`),
  UNIQUE KEY `employees_zkteco_user_id_unique` (`zkteco_user_id`),
  KEY `employees_user_id_foreign` (`user_id`),
  KEY `employees_name_index` (`name`),
  KEY `employees_last_first_name_index` (`last_name`,`first_name`),
  CONSTRAINT `employees_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_openings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_openings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `job_type` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `is_open` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `leave_balance_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `leave_balance_resets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `year` smallint(5) unsigned NOT NULL,
  `reset_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reset_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leave_balance_resets_reset_by_foreign` (`reset_by`),
  KEY `leave_balance_resets_year_reset_at_index` (`year`,`reset_at`),
  CONSTRAINT `leave_balance_resets_reset_by_foreign` FOREIGN KEY (`reset_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `leave_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `leave_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `employee_code` varchar(255) DEFAULT NULL,
  `employee_name` varchar(255) NOT NULL,
  `department` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `requested_date` date NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text DEFAULT NULL,
  `leave_balance` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `employee_archived` tinyint(1) NOT NULL DEFAULT 0,
  `rejection_reason` text DEFAULT NULL,
  `admin_archived` tinyint(1) NOT NULL DEFAULT 0,
  `decided_by` bigint(20) unsigned DEFAULT NULL,
  `decided_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `hr_status` varchar(255) NOT NULL DEFAULT 'pending',
  `hr_decided_by` bigint(20) unsigned DEFAULT NULL,
  `hr_decided_at` timestamp NULL DEFAULT NULL,
  `admin_status` varchar(255) NOT NULL DEFAULT 'pending',
  `admin_decided_by` bigint(20) unsigned DEFAULT NULL,
  `admin_decided_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leave_requests_user_id_foreign` (`user_id`),
  KEY `leave_requests_decided_by_foreign` (`decided_by`),
  KEY `leave_requests_admin_archived_status_index` (`admin_archived`,`status`),
  KEY `leave_requests_requested_date_index` (`requested_date`),
  KEY `leave_requests_hr_decided_by_foreign` (`hr_decided_by`),
  KEY `leave_requests_admin_decided_by_foreign` (`admin_decided_by`),
  CONSTRAINT `leave_requests_admin_decided_by_foreign` FOREIGN KEY (`admin_decided_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leave_requests_decided_by_foreign` FOREIGN KEY (`decided_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leave_requests_hr_decided_by_foreign` FOREIGN KEY (`hr_decided_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leave_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `loan_repayments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `loan_repayments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `loan_request_id` bigint(20) unsigned NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `paid_at` date NOT NULL,
  `payslip_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `loan_repayments_loan_request_id_paid_at_index` (`loan_request_id`,`paid_at`),
  CONSTRAINT `loan_repayments_loan_request_id_foreign` FOREIGN KEY (`loan_request_id`) REFERENCES `loan_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `loan_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `loan_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `loan_type` varchar(255) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `reason` text DEFAULT NULL,
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `hr_status` varchar(255) NOT NULL DEFAULT 'pending',
  `hr_rejection_reason` text DEFAULT NULL,
  `hr_reviewed_at` timestamp NULL DEFAULT NULL,
  `hr_reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `accounting_status` varchar(255) NOT NULL DEFAULT 'pending',
  `accounting_rejection_reason` text DEFAULT NULL,
  `accounting_reviewed_at` timestamp NULL DEFAULT NULL,
  `accounting_reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `admin_status` varchar(255) NOT NULL DEFAULT 'pending',
  `admin_rejection_reason` text DEFAULT NULL,
  `admin_reviewed_at` timestamp NULL DEFAULT NULL,
  `admin_reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `archived_at` timestamp NULL DEFAULT NULL,
  `staff_archived_at` timestamp NULL DEFAULT NULL,
  `staff_archived_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `loan_requests_hr_reviewed_by_foreign` (`hr_reviewed_by`),
  KEY `loan_requests_accounting_reviewed_by_foreign` (`accounting_reviewed_by`),
  KEY `loan_requests_admin_reviewed_by_foreign` (`admin_reviewed_by`),
  KEY `loan_requests_requested_at_index` (`requested_at`),
  KEY `loan_requests_status_index` (`hr_status`,`accounting_status`,`admin_status`),
  KEY `loan_requests_employee_archive_index` (`employee_id`,`archived_at`),
  KEY `loan_requests_staff_archived_by_foreign` (`staff_archived_by`),
  KEY `loan_requests_staff_archived_at_index` (`staff_archived_at`),
  CONSTRAINT `loan_requests_accounting_reviewed_by_foreign` FOREIGN KEY (`accounting_reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `loan_requests_admin_reviewed_by_foreign` FOREIGN KEY (`admin_reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `loan_requests_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `loan_requests_hr_reviewed_by_foreign` FOREIGN KEY (`hr_reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `loan_requests_staff_archived_by_foreign` FOREIGN KEY (`staff_archived_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `non_working_days`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `non_working_days` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(40) NOT NULL,
  `pay_type` varchar(10) NOT NULL DEFAULT 'unpaid',
  `paid_days` decimal(3,2) NOT NULL DEFAULT 0.00,
  `paid_hours` decimal(4,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `non_working_days_date_unique` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) unsigned NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `archived_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`),
  KEY `notifications_archived_at_index` (`archived_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `offboarding_cases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `offboarding_cases` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `reason` varchar(255) NOT NULL,
  `last_working_day` date NOT NULL,
  `employee_message` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Open',
  `progress` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `sent_to_admin` tinyint(1) NOT NULL DEFAULT 0,
  `sent_to_admin_at` timestamp NULL DEFAULT NULL,
  `admin_final_approved` tinyint(1) NOT NULL DEFAULT 0,
  `admin_approved_at` timestamp NULL DEFAULT NULL,
  `admin_approved_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `requirements_sent_at` timestamp NULL DEFAULT NULL,
  `required_documents` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`required_documents`)),
  `completed_interviews` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`completed_interviews`)),
  `rejection_reason` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `offboarding_cases_employee_id_foreign` (`employee_id`),
  KEY `offboarding_cases_admin_approved_by_foreign` (`admin_approved_by`),
  KEY `offboarding_cases_sent_to_admin_admin_final_approved_index` (`sent_to_admin`,`admin_final_approved`),
  CONSTRAINT `offboarding_cases_admin_approved_by_foreign` FOREIGN KEY (`admin_approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `offboarding_cases_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `offboarding_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `offboarding_documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `offboarding_case_id` bigint(20) unsigned NOT NULL,
  `document_label` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `review_status` varchar(255) NOT NULL DEFAULT 'Under review',
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `offboarding_documents_offboarding_case_id_foreign` (`offboarding_case_id`),
  KEY `offboarding_documents_reviewed_by_foreign` (`reviewed_by`),
  CONSTRAINT `offboarding_documents_offboarding_case_id_foreign` FOREIGN KEY (`offboarding_case_id`) REFERENCES `offboarding_cases` (`id`) ON DELETE CASCADE,
  CONSTRAINT `offboarding_documents_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `payroll_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payroll_records` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `period` varchar(7) NOT NULL,
  `cutoff` varchar(4) NOT NULL,
  `step_no` smallint(5) unsigned NOT NULL DEFAULT 1,
  `days_worked` decimal(5,2) NOT NULL DEFAULT 22.00,
  `hours_worked` decimal(6,2) NOT NULL DEFAULT 176.00,
  `late_minutes` decimal(8,2) NOT NULL DEFAULT 0.00,
  `undertime_minutes` decimal(8,2) NOT NULL DEFAULT 0.00,
  `honorarium` decimal(10,2) NOT NULL DEFAULT 0.00,
  `sss` decimal(10,2) NOT NULL DEFAULT 0.00,
  `sss_loan` decimal(10,2) NOT NULL DEFAULT 0.00,
  `philhealth` decimal(10,2) NOT NULL DEFAULT 0.00,
  `philhealth_loan` decimal(10,2) NOT NULL DEFAULT 0.00,
  `peraa_ceap` decimal(10,2) NOT NULL DEFAULT 0.00,
  `peraa_ceap_loan` decimal(10,2) NOT NULL DEFAULT 0.00,
  `hdmf` decimal(10,2) NOT NULL DEFAULT 0.00,
  `hdmf_loan` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cash_advance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payroll_records_period_cutoff_employee_id_unique` (`period`,`cutoff`,`employee_id`),
  KEY `payroll_records_employee_id_foreign` (`employee_id`),
  KEY `payroll_records_period_index` (`period`),
  CONSTRAINT `payroll_records_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `payroll_steps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payroll_steps` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `step_no` smallint(5) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `daily_wage` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payroll_steps_step_no_unique` (`step_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `payslip_responses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payslip_responses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payslip_id` bigint(20) unsigned NOT NULL,
  `employee_id` bigint(20) unsigned NOT NULL,
  `status` varchar(255) NOT NULL,
  `reason` text DEFAULT NULL,
  `reviewed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payslip_responses_payslip_id_unique` (`payslip_id`),
  KEY `payslip_responses_employee_id_foreign` (`employee_id`),
  CONSTRAINT `payslip_responses_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payslip_responses_payslip_id_foreign` FOREIGN KEY (`payslip_id`) REFERENCES `payslips` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `payslips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payslips` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `cutoff` varchar(255) NOT NULL DEFAULT '15th',
  `transfer_date` date DEFAULT NULL,
  `step_name` varchar(255) DEFAULT NULL,
  `days_worked` decimal(6,2) NOT NULL DEFAULT 0.00,
  `hours_worked` decimal(7,2) NOT NULL DEFAULT 0.00,
  `late_minutes` int(10) unsigned NOT NULL DEFAULT 0,
  `undertime_minutes` int(10) unsigned NOT NULL DEFAULT 0,
  `basic_pay` decimal(12,2) NOT NULL DEFAULT 0.00,
  `daily_wage` decimal(12,2) NOT NULL DEFAULT 0.00,
  `honorarium` decimal(12,2) NOT NULL DEFAULT 0.00,
  `gross_pay` decimal(12,2) NOT NULL DEFAULT 0.00,
  `malasakitan` decimal(12,2) NOT NULL DEFAULT 50.00,
  `late_deduction` decimal(12,2) NOT NULL DEFAULT 0.00,
  `undertime_deduction` decimal(12,2) NOT NULL DEFAULT 0.00,
  `absences_deduction` decimal(12,2) NOT NULL DEFAULT 0.00,
  `sss` decimal(12,2) NOT NULL DEFAULT 0.00,
  `sss_loan` decimal(12,2) NOT NULL DEFAULT 0.00,
  `philhealth` decimal(12,2) NOT NULL DEFAULT 0.00,
  `philhealth_loan` decimal(12,2) NOT NULL DEFAULT 0.00,
  `peraa_ceap` decimal(12,2) NOT NULL DEFAULT 0.00,
  `peraa_ceap_loan` decimal(12,2) NOT NULL DEFAULT 0.00,
  `hdmf` decimal(12,2) NOT NULL DEFAULT 0.00,
  `hdmf_loan` decimal(12,2) NOT NULL DEFAULT 0.00,
  `cash_advance` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_deductions` decimal(12,2) NOT NULL DEFAULT 0.00,
  `net_pay` decimal(12,2) NOT NULL DEFAULT 0.00,
  `sent_to_admin_at` timestamp NULL DEFAULT NULL,
  `admin_status` varchar(255) NOT NULL DEFAULT 'Pending',
  `admin_rejection_reason` text DEFAULT NULL,
  `admin_reviewed_at` timestamp NULL DEFAULT NULL,
  `admin_reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `hr_status` varchar(255) NOT NULL DEFAULT 'Pending',
  `hr_rejection_reason` text DEFAULT NULL,
  `hr_reviewed_at` timestamp NULL DEFAULT NULL,
  `hr_reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `released_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payslips_employee_id_transfer_date_cutoff_unique` (`employee_id`,`transfer_date`,`cutoff`),
  KEY `payslips_admin_reviewed_by_foreign` (`admin_reviewed_by`),
  KEY `payslips_hr_reviewed_by_foreign` (`hr_reviewed_by`),
  KEY `payslips_admin_status_index` (`admin_status`),
  CONSTRAINT `payslips_admin_reviewed_by_foreign` FOREIGN KEY (`admin_reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payslips_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payslips_hr_reviewed_by_foreign` FOREIGN KEY (`hr_reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `submission_boxes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `submission_boxes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `deadline` date DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `employee_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `submission_boxes_created_by_foreign` (`created_by`),
  KEY `submission_boxes_employee_id_foreign` (`employee_id`),
  CONSTRAINT `submission_boxes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `submission_boxes_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `system_data_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_data_versions` (
  `id` tinyint(3) unsigned NOT NULL,
  `version` bigint(20) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `time_corrections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `time_corrections` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `correction_date` date NOT NULL,
  `correction_type` varchar(255) NOT NULL,
  `incorrect_time` time DEFAULT NULL,
  `correct_time` time NOT NULL,
  `note` text NOT NULL,
  `proof_path` varchar(255) DEFAULT NULL,
  `proof_original_name` varchar(255) DEFAULT NULL,
  `admin_status` varchar(255) NOT NULL DEFAULT 'Pending',
  `admin_reviewed_at` timestamp NULL DEFAULT NULL,
  `admin_reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `hr_status` varchar(255) NOT NULL DEFAULT 'Pending',
  `hr_reviewed_at` timestamp NULL DEFAULT NULL,
  `hr_reviewed_by` bigint(20) unsigned DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `applied_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `time_corrections_employee_id_foreign` (`employee_id`),
  KEY `time_corrections_admin_reviewed_by_foreign` (`admin_reviewed_by`),
  KEY `time_corrections_hr_reviewed_by_foreign` (`hr_reviewed_by`),
  KEY `time_corrections_correction_date_index` (`correction_date`),
  KEY `time_corrections_admin_status_index` (`admin_status`),
  KEY `time_corrections_hr_status_index` (`hr_status`),
  CONSTRAINT `time_corrections_admin_reviewed_by_foreign` FOREIGN KEY (`admin_reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `time_corrections_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `time_corrections_hr_reviewed_by_foreign` FOREIGN KEY (`hr_reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `zkteco_attendances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `zkteco_attendances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fingerprint` varchar(64) NOT NULL,
  `device` varchar(255) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `uid` int(10) unsigned DEFAULT NULL,
  `punched_at` datetime NOT NULL,
  `timezone` varchar(64) DEFAULT NULL,
  `direction` varchar(32) NOT NULL,
  `verify_method` varchar(32) NOT NULL,
  `raw_direction` smallint(5) unsigned NOT NULL DEFAULT 0,
  `raw_verify` smallint(5) unsigned NOT NULL DEFAULT 0,
  `work_code` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `zkteco_attendances_fingerprint_unique` (`fingerprint`),
  KEY `zkteco_attendances_user_id_punched_at_index` (`user_id`,`punched_at`),
  KEY `zkteco_attendances_device_index` (`device`),
  KEY `zkteco_attendances_user_id_index` (`user_id`),
  KEY `zkteco_attendances_punched_at_index` (`punched_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `zkteco_push_commands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `zkteco_push_commands` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `serial_number` varchar(255) NOT NULL,
  `command` text NOT NULL,
  `status` varchar(16) NOT NULL DEFAULT 'pending',
  `return_code` int(11) DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `zkteco_push_commands_serial_number_status_index` (`serial_number`,`status`),
  KEY `zkteco_push_commands_serial_number_index` (`serial_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `zkteco_push_devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `zkteco_push_devices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `serial_number` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `status` varchar(16) NOT NULL DEFAULT 'pending',
  `ip_address` varchar(45) DEFAULT NULL,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`details`)),
  `stamps` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`stamps`)),
  `last_seen_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `zkteco_push_devices_serial_number_unique` (`serial_number`),
  KEY `zkteco_push_devices_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

/*M!999999\- enable the sandbox mode */ 
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'0001_01_01_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2026_10_14_170147_create_permission_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2026_10_16_123520_create_applications_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2026_10_16_173854_create_banners_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2026_10_16_173958_create_calendar_events_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2026_10_17_102132_create_employee_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2026_10_17_112345_create_time_correction_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2026_10_17_142447_create_leave_request_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2026_10_18_113832_create_payslips_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2026_10_18_120411_create_loan_request_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2026_10_18_122340_create_offboarding_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2026_10_18_150333_craete_attendance_record_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2026_10_19_070652_create_employee_document_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2026_10_19_121156_create_payroll_record_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2026_10_19_122327_create_payroll_step_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2026_10_22_141145_create_dashboard_task_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2026_10_23_140223_add_employee_archive_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2026_10_23_151535_add_repayment_months_to_loan_requests_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2026_10_23_162756_add_employment_status_to_employees_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2026_10_23_162957_add_employment_status_to_employees_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2026_10_24_073000_create_payslip_responses_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2026_10_24_132611_add_account_field_to_employees_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2026_10_24_135823_update_employees_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2026_10_25_133227_create_non_working_days_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2026_10_26_094012_add_staff_archived_at_to_loan_requests_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2026_10_26_111452_create_job_openings_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2026_10_26_120000_add_title_to_job_openings_table',9);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2026_10_26_123153_add_interview_note_to_applications_table',10);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2026_10_26_182108_add_applied_at_to_time_connections_table',11);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2026_10_27_062941_create_notifications_table',12);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2026_10_27_063051_add_rejection_reason_to_requests',12);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2026_10_28_133958_align_employees_table_for_profile',13);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35,'2026_10_29_165808_add_employee_archieved_to_leave_requests_table',14);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36,'2026_10_29_170341_add_employee_archived_to_leave_requests_table',15);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37,'2026_10_29_183123_create_zkteco_tables',16);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38,'2026_10_29_190715_add_zkteco_user_id_to_employees_table',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39,'2026_10_31_142913_add_step_no_to_employees_table',18);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (40,'2026_11_01_000001_add_placement_to_banners_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (41,'2026_11_02_000000_create_system_data_versions_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (42,'2026_11_01_141500_create_leave_balance_resets_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (43,'2026_11_02_000001_add_interview_completed_at_to_applications_table',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (44,'2026_11_02_000002_add_original_document_names_to_applications_table',23);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (45,'2026_11_02_000003_add_hr_workflow_fields_to_offboarding_tables',24);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (46,'2026_11_02_000004_add_proof_original_name_to_time_corrections_table',25);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (47,'2026_11_02_000005_add_proof_original_name_to_time_corrections_table',26);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (48,'2026_11_02_130000_add_philhealth_loan_to_payslips_table',27);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (49,'2026_11_02_140000_add_philhealth_loan_to_payroll_records_table',28);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (50,'2026_11_02_153500_add_employee_id_to_submission_boxes_table',29);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (51,'2026_11_02_170000_add_archived_at_to_notifications_table',30);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (52,'2026_11_03_000001_add_name_parts_to_applications_table',31);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (53,'2026_11_03_000002_add_dual_review_to_leave_requests',32);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (54,'2026_11_08_025621_add_placement_and_is_default_to_banners_table',33);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (55,'2026_10_29_185114_create_zkteco_tables',34);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (56,'2026_10_31_155738_add_step_no_to_employees_table',34);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (57,'2026_11_03_220000_add_undertime_to_payroll_tables',34);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (58,'2026_11_10_000001_ensure_civil_status_and_gender_on_employees_table',35);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (59,'2026_11_12_100000_add_name_parts_to_employees_table',36);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (60,'2026_11_14_000001_add_interview_response_to_applications_table',37);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (61,'2026_11_14_000002_add_job_opening_details',38);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (62,'2026_11_14_000003_add_requirements_to_job_openings_table',39);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (63,'2026_11_10_045743_create_biometric_sync_logs_table',40);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (64,'2026_11_10_182951_create_personal_access_tokens_table',40);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (65,'2026_11_12_082342_add_archived_at_to_notifications_table',40);
