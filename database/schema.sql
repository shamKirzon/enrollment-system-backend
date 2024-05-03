CREATE TABLE year_levels (
  id VARCHAR(255) PRIMARY KEY,
  name VARCHAR(20) NOT NULL
)

INSERT INTO year_levels (id, name)
VALUES
    ('g1', 'Grade 1'),
    ('g2', 'Grade 2'),
    ('g3', 'Grade 3'),
    ('g4', 'Grade 4'),
    ('g5', 'Grade 5'),
    ('g6', 'Grade 6'),
    ('g7', 'Grade 7'),
    ('g8', 'Grade 8'),
    ('g9', 'Grade 9'),
    ('g10', 'Grade 10'),
    ('g11', 'Grade 11'),
    ('g12', 'Grade 12');

CREATE TABLE sections (
  id VARCHAR(255) PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
)

CREATE TABLE strands (
  id VARCHAR(255) PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
)

CREATE TABLE shs_sections (
  id VARCHAR(255) PRIMARY KEY,
  section_id VARCHAR(255) NOT NULL,
  year_level_id VARCHAR(255) NOT NULL,
  strand_id VARCHAR(255) 
)

CREATE TABLE transactions (
  id VARCHAR(255) PRIMARY KEY,
  amount DECIMAL(10, 2) UNSIGNED NOT NULL,
  payment_receipt_url VARCHAR(255) NOT NULL
)

CREATE TABLE `enrollments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `section` varchar(255) DEFAULT NULL,
  `tuition_plan` enum('a','b','c','d') NOT NULL,
  `status` enum('pending','done') NOT NULL DEFAULT 'pending',
  -- `payment_receipt_url` varchar(255) NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `academic_year_id` bigint(20) UNSIGNED NOT NULL,
  `year_level_id` varchar(255) NOT NULL,
  transaction_id VARCHAR(255) NOT NULL,
  tuition_plan_id VARCHAR(255) NOT NULL
) 

CREATE TABLE tuition_plans (
  id VARCHAR(255) PRIMARY KEY,
  name VARCHAR(50) NOT NULL,
  description VARCHAR(1000) NOT NULL
)
