-- Users (Authentication)
CREATE TABLE IF NOT EXISTS users (
  id CHAR(36) PRIMARY KEY, -- uuid()
  created_at timestamp NOT NULL DEFAULT current_timestamp(),

  first_name varchar(255) NOT NULL,
  middle_name varchar(255),
  last_name varchar(255) NOT NULL,
  suffix_name varchar(255), -- Jr., III., etc.
  email varchar(255) NOT NULL,
  contact_number varchar(20) NOT NULL,
  role ENUM('student', 'parent', 'teacher', 'admin') NOT NULL DEFAULT 'student',
  avatar_url TEXT, -- 1x1 picture / ID picture

  password TEXT NOT NULL -- Hashed password

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;






-- Parent Account
-- Will be used for parents who want to create an account and view their children's enrollment status, grades, etc.
CREATE TABLE IF NOT EXISTS parent_student_links (
  id CHAR(36) PRIMARY KEY, -- uuid()

  parent_id CHAR(36) NOT NULL,
  student_id CHAR(36) NOT NULL,

  FOREIGN KEY (parent_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Student's Personal Information

CREATE TABLE IF NOT EXISTS address (
  id INT PRIMARY KEY AUTO_INCREMENT, -- Auto increment for simplicity
  country VARCHAR(255) NOT NULL,
  region VARCHAR(255) NOT NULL,
  province VARCHAR(255) NOT NULL,
  city VARCHAR(255) NOT NULL,
  barangay VARCHAR(255) NOT NULL,
  street TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Student's Family Members
CREATE TABLE IF NOT EXISTS student_family_members (
  id CHAR(36) PRIMARY KEY, -- uuid()
  first_name varchar(255) NOT NULL,
  middle_name varchar(255),
  last_name varchar(255) NOT NULL,
  suffix_name varchar(255), -- Jr., III., etc.

  relationship ENUM('mother', 'father', 'guardian') NOT NULL,
  occupation VARCHAR(255) NOT NULL,

  address_id INT NOT NULL, -- Just use the same address for their student
  student_id CHAR(36) NOT NULL,

  FOREIGN KEY (address_id) REFERENCES address(id) ON DELETE CASCADE,
  FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Information of the student themselves
CREATE TABLE IF NOT EXISTS student_profiles (
  id CHAR(36) PRIMARY KEY, -- uuid()

  lrn VARCHAR(20) NOT NULL UNIQUE, -- Learning Reference Number (LRN)
  birth_date DATE NOT NULL,
  birth_place TEXT NOT NULL,
  sex ENUM('male', 'female') NOT NULL,
  citizenship VARCHAR(100) NOT NULL,
  religion VARCHAR(100) NOT NULL,

  -- Preferred contact details
  parent_contact_number VARCHAR(20) NOT NULL,
  landline VARCHAR(20) NOT NULL,

  -- Documents
  -- Put them in here instead on `enrollments` since there is no need to reupload the same certificate
  birth_certificate_url TEXT NOT NULL,
  baptismal_certificate_url TEXT NOT NULL,

  address_id INT NOT NULL,
  student_id CHAR(36) NOT NULL UNIQUE,

  FOREIGN KEY (address_id) REFERENCES address(id) ON DELETE CASCADE,
  FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




-- Enrollment stuff

CREATE TABLE IF NOT EXISTS academic_years (
  id INT PRIMARY KEY AUTO_INCREMENT,
  start_at DATE NOT NULL,
  end_at DATE NOT NULL,
  status ENUM('upcoming', 'open', 'ongoing', 'finished') NOT NULL DEFAULT 'upcoming'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS year_levels (
  id VARCHAR(50) PRIMARY KEY,
  name VARCHAR(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample year levels to insert
INSERT INTO year_levels (id, name)
VALUES
    ('nur', 'Nursery'),
    ('kin', 'Kinder'),
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



-- NOTE: This may or may not be a user
-- For now, don't make it a user for simplicity
-- The grades for the report card will be entered by the admins instead
CREATE TABLE IF NOT EXISTS teachers (
  id CHAR(36) PRIMARY KEY,
  first_name varchar(255) NOT NULL,
  middle_name varchar(255),
  last_name varchar(255) NOT NULL,
  suffix_name varchar(255), -- Jr., III., etc.
  sex ENUM('male', 'female') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS sections (
  id VARCHAR(50) PRIMARY KEY,
  name VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Only for SHS
CREATE TABLE IF NOT EXISTS strands (
  id VARCHAR(100) PRIMARY KEY,
  name VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sections per grade level + Advisers
CREATE TABLE IF NOT EXISTS section_levels (
  id VARCHAR(100) PRIMARY KEY,

  section_id VARCHAR(50) NOT NULL,
  year_level_id VARCHAR(50) NOT NULL,

  -- Assigned teachers for that section
  -- Some sections may not have advisers
  adviser_id CHAR(36), 

  FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE CASCADE,
  FOREIGN KEY (year_level_id) REFERENCES year_levels(id) ON DELETE CASCADE,
  FOREIGN KEY (adviser_id) REFERENCES teachers(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SHS Sections and their strands
CREATE TABLE IF NOT EXISTS section_strands (
  id VARCHAR(100) PRIMARY KEY,

  section_level_id VARCHAR(100) NOT NULL,
  strand_id VARCHAR(100) NOT NULL,

  FOREIGN KEY (section_level_id) REFERENCES section_levels(id) ON DELETE CASCADE,
  FOREIGN KEY (strand_id) REFERENCES strands(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



-- Use to assign students to their corresponding sections per enrollment
CREATE TABLE IF NOT EXISTS section_assignments (
  id INT PRIMARY KEY AUTO_INCREMENT,

  enrollment_id CHAR(36) NOT NULL,
  section_level_id VARCHAR(100) NOT NULL,

  FOREIGN KEY (enrollment_id) REFERENCES enrollments(id) ON DELETE CASCADE,
  FOREIGN KEY (section_level_id) REFERENCES section_levels(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Only for SHS students
CREATE TABLE IF NOT EXISTS section_assignment_strands (
  id INT PRIMARY KEY AUTO_INCREMENT,

  section_assignment_id INT NOT NULL,
  strand_id VARCHAR(100) NOT NULL,

  FOREIGN KEY (section_assignment_id) REFERENCES section_assignments(id) ON DELETE CASCADE,
  FOREIGN KEY (strand_id) REFERENCES strands(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Payments/Transactions

-- The school's payment accounts
CREATE TABLE IF NOT EXISTS payment_accounts (
  id VARCHAR(255) PRIMARY KEY, -- account_name + '-' + account_number
  account_name VARCHAR(100) NOT NULL,
  account_number VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- This is the school's payment modes
CREATE TABLE IF NOT EXISTS payment_modes (
  id VARCHAR(50) PRIMARY KEY,
  channel VARCHAR(100) NOT NULL, -- BPI, GCash, onsite (f2f), etc.

  payment_account_id VARCHAR(255), -- Probably NULL if mode is onsite

  FOREIGN KEY (payment_account_id) REFERENCES payment_accounts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Transactions
-- This is submitted by the student or parent after they finish paying
CREATE TABLE IF NOT EXISTS transactions (
  id CHAR(36) PRIMARY KEY, -- uuid()
  created_at TIMESTAMP NOT NULL DEFAULT current_timestamp(),

  transaction_number VARCHAR(50) NOT NULL, -- Transaction number
  payment_amount DECIMAL(10, 2) UNSIGNED NOT NULL, -- Payment amount
  payment_method ENUM('cash', 'installment') NOT NULL,
  payment_receipt_url TEXT NOT NULL,

  payment_mode_id VARCHAR(50) NOT NULL,

  FOREIGN KEY (payment_mode_id) REFERENCES payment_modes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;





-- Tuition Plans
-- This only applies if the payment is installment based
CREATE TABLE IF NOT EXISTS tuition_plans (
  id VARCHAR(20) PRIMARY KEY, 
  name VARCHAR(50) NOT NULL  -- A-1, ..., B-1, ...
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- The amount of installment payment of each tuition plan per year level
CREATE TABLE IF NOT EXISTS tuition_plan_levels (
  id CHAR(36) PRIMARY KEY,
  down_payment_amount DECIMAL(10, 2) UNSIGNED NOT NULL,
  monthly_payment_amount DECIMAL(10, 2) UNSIGNED NOT NULL,

  tuition_plan_id VARCHAR(20) NOT NULL,
  year_level_id VARCHAR(50) NOT NULL,

  FOREIGN KEY (tuition_plan_id) REFERENCES tuition_plans(id) ON DELETE CASCADE,
  FOREIGN KEY (year_level_id) REFERENCES year_levels(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




-- Enrollment Fees
-- e.g. tuition fee, computer fee, energy fee, etc.
CREATE TABLE IF NOT EXISTS enrollment_fees (
  id VARCHAR(255) PRIMARY KEY, -- `name` in lower case
  name VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Amount of each enrollment fee per level
-- The sum of all enrollment fees will be the total enrollment fee for that year level
CREATE TABLE IF NOT EXISTS enrollment_fee_levels (
  id VARCHAR(255) PRIMARY KEY, -- `enrollment_fee_id` + `year_level_id`
  amount DECIMAL(10, 2) UNSIGNED NOT NULL,

  enrollment_fee_id VARCHAR(255) NOT NULL,
  year_level_id VARCHAR(50) NOT NULL,

  FOREIGN KEY (enrollment_fee_id) REFERENCES enrollment_fees(id) ON DELETE CASCADE,
  FOREIGN KEY (year_level_id) REFERENCES year_levels(id) ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



-- Enrollment Discounts
-- SUM(amount FROM enrollment_fee_levels) * (1 - (percentage FROM enrollment_discounts)) = discounted enrollment fee
CREATE TABLE IF NOT EXISTS enrollment_discounts (
  id VARCHAR(255) PRIMARY KEY, -- `name` in lower case
  name VARCHAR(255) NOT NULL,
  percentage DECIMAL(6, 2) UNSIGNED NOT NULL

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




-- Enrollments of students

CREATE TABLE IF NOT EXISTS enrollments (
  id CHAR(36) PRIMARY KEY, -- uuid()
  enrolled_at TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  status enum('pending', 'done') NOT NULL DEFAULT 'pending',

  student_id CHAR(36) NOT NULL,
  academic_year_id INT NOT NULL,
  year_level_id VARCHAR(50) NOT NULL,
  transaction_id CHAR(36) NOT NULL UNIQUE,

  FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE,
  FOREIGN KEY (year_level_id) REFERENCES year_levels(id) ON DELETE CASCADE,
  FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Only for SHS students
CREATE TABLE IF NOT EXISTS enrollment_strands (
  id VARCHAR(150) PRIMARY KEY, -- `strand_id` + "-" + `id`

  enrollment_id CHAR(36) NOT NULL,
  strand_id VARCHAR(100) NOT NULL,

  FOREIGN KEY(enrollment_id) REFERENCES enrollments(id) ON DELETE CASCADE,
  FOREIGN KEY(strand_id) REFERENCES strands(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- All discounts for students
CREATE TABLE IF NOT EXISTS enrollment_discount_applications (
  id CHAR(36) PRIMARY KEY,

  enrollment_id CHAR(36) NOT NULL,
  enrollment_discount_id VARCHAR(255) NOT NULL, 

  FOREIGN KEY(enrollment_id) REFERENCES enrollments(id) ON DELETE CASCADE,
  FOREIGN KEY(enrollment_discount_id) REFERENCES enrollment_discounts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Tuition plans of those who enrolled via installment
CREATE TABLE IF NOT EXISTS enrolled_tuition_plans (
  id CHAR(36) PRIMARY KEY, -- uuid()

  enrollment_id CHAR(36) NOT NULL,
  tuition_plan_id VARCHAR(20) NOT NULL,

  FOREIGN KEY (enrollment_id) REFERENCES enrollments(id) ON DELETE CASCADE,
  FOREIGN KEY (tuition_plan_id) REFERENCES tuition_plans(id) ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




-- Only for transferees or returnees / new students
-- A student is considered "new" if they did not enroll in the previous school year
-- regardless if they've enrolled on the school in the past (before the previous school year)
CREATE TABLE IF NOT EXISTS report_cards (
  id INT PRIMARY KEY AUTO_INCREMENT, -- Auto increment for simplicity
  report_card_url TEXT NOT NULL,

  enrollment_id CHAR(36) NOT NULL,

  FOREIGN KEY(enrollment_id) REFERENCES enrollments(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Subjects

-- All subjects
CREATE TABLE IF NOT EXISTS subjects (
  id VARCHAR(50) PRIMARY KEY,
  name VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Subjects per grade level
CREATE TABLE IF NOT EXISTS subject_levels (
  id VARCHAR(50) PRIMARY KEY,
  subject_id VARCHAR(50) NOT NULL,
  year_level_id VARCHAR(255) NOT NULL,

  FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
  FOREIGN KEY (year_level_id) REFERENCES year_levels(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SHS subjects and their strands
CREATE TABLE IF NOT EXISTS subject_strands (
  id VARCHAR(50) PRIMARY KEY,
  subject_level_id VARCHAR(50) NOT NULL,
  strand_id VARCHAR(100) NOT NULL,
  semester ENUM('1', '2') NOT NULL,

  FOREIGN KEY (subject_level_id) REFERENCES subject_levels(id) ON DELETE CASCADE,
  FOREIGN KEY (strand_id) REFERENCES strands(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- TODO: Which grading period the subject is on
-- Only applies for SHS ?
CREATE TABLE IF NOT EXISTS subject_periods (
  id VARCHAR(255) PRIMARY KEY,

  -- First to Fourth grading period, may also be translated to periods of each SHS semester
  -- e.g. 1st Sem - 1st Period = '1', 2nd Sem - 2nd Period = '4'
  period ENUM('1', '2', '3', '4') NOT NULL, 

  subject_strand_id VARCHAR(50) NOT NULL,

  FOREIGN KEY (subject_strand_id) REFERENCES subject_strands(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




-- Grades
-- Use to generate report card
CREATE TABLE IF NOT EXISTS student_grades (
  id INT PRIMARY KEY AUTO_INCREMENT,
  grade DECIMAL(6, 3) UNSIGNED NOT NULL, -- Only up to 100.000

  -- First to Fourth grading period, may also be translated to periods of each SHS semester
  -- e.g. 1st Sem - 1st Period = '1', 2nd Sem - 2nd Period = '4'
  period ENUM('1', '2', '3', '4') NOT NULL, 

  -- Use `subject_level` to get both the subject and year level
  -- Can also be used to get the subject strand if needed
  subject_level_id VARCHAR(50) NOT NULL,
  student_id CHAR(36) NOT NULL,

  FOREIGN KEY (subject_level_id) REFERENCES subject_levels(id) ON DELETE CASCADE,
  FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Only for SHS students
-- Some subjects may differ in strands and semesters
CREATE TABLE IF NOT EXISTS student_grade_strands (
  id INT PRIMARY KEY AUTO_INCREMENT,

  student_grade_id INT NOT NULL,
  strand_id VARCHAR(100) NOT NULL,

  FOREIGN KEY(student_grade_id) REFERENCES student_grades(id) ON DELETE CASCADE,
  FOREIGN KEY(strand_id) REFERENCES strands(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- LEAVE THIS HERE

-- SELECT
--    sub.id AS subject_id,
--    sub.name AS subject_name,
--    CONCAT('[', GROUP_CONCAT(yl.id), ']') AS year_level_ids,
--     CONCAT('[', GROUP_CONCAT(str.id), ']') AS strand_ids
-- FROM subjects sub
-- JOIN subject_levels sublvl ON sublvl.subject_id = sub.id
-- JOIN year_levels yl ON yl.id = sublvl.year_level_id
-- JOIN subject_strands substr ON substr.subject_level_id = sublvl.id
-- JOIN strands str ON str.id = substr.strand_id
-- GROUP BY sub.id;

SELECT
(
  (
    SELECT COUNT(student_id)
    FROM enrollments
    WHERE status = 'done' AND academic_year_id = ? AND year_level_id = ? 
  ) /
  (
    SELECT COUNT(id) 
    FROM section_levels
    WHERE year_level_id = ?
  )
) AS student_count_per_section

-- Select section levels
SELECT * FROM section_levels
WHERE year_level_id = ?

-- Loop through `section_levels` and assign 40 students for each section.
-- But also keep in mind if there are leftover students who have yet to be assigned.
SELECT * FROM enrollments
WHERE status = 'done' AND academic_year_id = ? AND year_level_id = ?
ORDER BY enrolled_at
LIMIT 40


INSERT INTO section_assignments (id, enrollment_id, section_level_id)
SELECT 

