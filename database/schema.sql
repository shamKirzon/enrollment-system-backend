-- Users (Authentication)
CREATE TABLE users (
  id CHAR(36) PRIMARY KEY, -- uuid()
  created_at timestamp NOT NULL DEFAULT current_timestamp(),

  first_name varchar(255) NOT NULL,
  middle_name varchar(255),
  last_name varchar(255) NOT NULL,
  suffix_name varchar(255), -- Jr., III., etc.
  email varchar(255) NOT NULL,
  contact_number varchar(20) NOT NULL,
  role ENUM('student', 'teacher', 'parent', 'admin') NOT NULL DEFAULT 'student',
  avatar_url TEXT,

  password TEXT NOT NULL

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


--- Parent Account
-- Will be used for parents who want to create an account and view their children's enrollment status, grades, etc.
CREATE TABLE parent_student_links (
  id CHAR(36) PRIMARY KEY, -- uuid()

  parent_id CHAR(36) NOT NULL,
  student_id CHAR(36) NOT NULL,

  FOREIGN KEY (parent_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


--- Student's Personal Information

CREATE TABLE address (
  id INT PRIMARY KEY AUTO_INCREMENT, -- CHANGE THIS  
  country VARCHAR(255) NOT NULL,
  region VARCHAR(255) NOT NULL,
  province VARCHAR(255) NOT NULL,
  city VARCHAR(255) NOT NULL,
  barangay VARCHAR(255) NOT NULL,
  street TEXT
)

-- Student's Family Members
CREATE TABLE student_family_members (
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

-- For students only?
CREATE TABLE student_profiles (
  id CHAR(36) PRIMARY KEY, -- uuid()

  lrn VARCHAR(20) NOT NULL UNIQUE, -- Learning Reference Number (LRN)
  birth_date DATE NOT NULL,
  birth_place TEXT NOT NULL,
  sex ENUM('male', 'female') NOT NULL,
  citizenship VARCHAR(100) NOT NULL,
  religion VARCHAR(100) NOT NULL,

  -- Preferred details
  parent_contact_number VARCHAR(20) NOT NULL,
  landline VARCHAR(20) NOT NULL,

  address_id INT NOT NULL,
  student_id CHAR(36) NOT NULL,

  FOREIGN KEY (address_id) REFERENCES address(id) ON DELETE CASCADE,
  FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




--- Enrollment stuff

CREATE TABLE year_levels (
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




CREATE TABLE sections (
  id VARCHAR(50) PRIMARY KEY,
  name VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Only for SHS
CREATE TABLE strands (
  id VARCHAR(100) PRIMARY KEY,
  name VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sections per grade level
CREATE TABLE section_levels (
  id VARCHAR(100) PRIMARY KEY,

  section_id VARCHAR(50) NOT NULL,
  year_level_id VARCHAR(50) NOT NULL,

  FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE CASCADE,
  FOREIGN KEY (year_level_id) REFERENCES year_levels(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SHS Sections and their strands
CREATE TABLE section_strands (
  id VARCHAR(100) PRIMARY KEY,

  section_level_id VARCHAR(100) NOT NULL,
  strand_id VARCHAR(100) NOT NULL,

  FOREIGN KEY (section_level_id) REFERENCES section_levels(id) ON DELETE CASCADE,
  FOREIGN KEY (strand_id) REFERENCES strands(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;





--- Payments/Transactions

-- The school's payment accounts
CREATE TABLE payment_accounts (
  id VARCHAR(255) PRIMARY KEY, -- account_name + '-' + account_number
  account_name VARCHAR(100) NOT NULL,
  account_number VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- This is the school's payment modes
CREATE TABLE payment_modes (
  id VARCHAR(50) PRIMARY KEY,
  channel VARCHAR(100) NOT NULL, -- BPI, GCash, onsite (f2f), etc.

  payment_account_id VARCHAR(255), -- Probably NULL if mode is onsite

  FOREIGN KEY (payment_account_id) REFERENCES payment_accounts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Transactions
-- This is submitted by the student or parent after they finish paying
CREATE TABLE transactions (
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
CREATE TABLE tuition_plans (
  id VARCHAR(20) PRIMARY KEY, 
  name VARCHAR(50) NOT NULL  -- A-1, ..., B-1, ..., Cash
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- The amount of tuition per year
CREATE TABLE tuition_plan_levels (
  id CHAR(36) PRIMARY KEY,
  down_payment_amount DECIMAL(10, 2) UNSIGNED NOT NULL DEFAULT 0.0,
  monthly_payment_amount DECIMAL(10, 2) UNSIGNED NOT NULL DEFAULT 0.0,

  tuition_plan_id VARCHAR(20) NOT NULL,
  year_level_id VARCHAR(50) NOT NULL,

  FOREIGN KEY (tuition_plan_id) REFERENCES tuition_plans(id) ON DELETE CASCADE,
  FOREIGN KEY (year_level_id) REFERENCES year_levels(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




-- Enrollment Fees

CREATE TABLE enrollment_fees (
  id VARCHAR(255) PRIMARY KEY, 
  name VARCHAR(255) NOT NULL
  -- amount DECIMAL(10, 2) UNSIGNED NOT NULL,
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Amount of each enrollment fee per level
-- The sum of all enrollment fee per level will be the total tuition
CREATE TABLE enrollment_fee_levels (
  id VARCHAR(255) PRIMARY KEY,
  amount DECIMAL(10, 2) UNSIGNED NOT NULL,

  year_level_id VARCHAR(50) NOT NULL,

  FOREIGN KEY (year_level_id) REFERENCES year_levels(id) ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



-- Enrollment Discounts

CREATE TABLE enrollment_discounts (
  id VARCHAR(255) PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  percentage DECIMAL(10, 2) UNSIGNED NOT NULL

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




CREATE TABLE academic_years (
  id INT AUTO_INCREMENT,
  start_at DATE NOT NULL,
  end_at DATE NOT NULL,
  status ENUM('upcoming', 'open', 'ongoing', 'finished') NOT NULL DEFAULT 'upcoming'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE enrollments (
  id CHAR(36) PRIMARY KEY, -- uuid()
  enrolled_at TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  status enum('pending','done') NOT NULL DEFAULT 'pending',

  student_id CHAR(36) NOT NULL,
  academic_year_id INT NOT NULL,
  year_level_id VARCHAR(50) NOT NULL,
  transaction_id CHAR(36) NOT NULL UNIQUE,
  tuition_plan_id VARCHAR(20), -- NULL if transaction payment_method is 'cash'

  FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE,
  FOREIGN KEY (year_level_id) REFERENCES year_levels(id) ON DELETE CASCADE,
  FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
  FOREIGN KEY (tuition_plan_id) REFERENCES tuition_plans(id) ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;





--- Subjects

CREATE TABLE subjects (
  id VARCHAR(50) PRIMARY KEY,
  name VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Subjects per grade level
CREATE TABLE subject_levels (
  id VARCHAR(50) PRIMARY KEY,
  subject_id VARCHAR(50) NOT NULL,
  year_level_id VARCHAR(255) NOT NULL,

  FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
  FOREIGN KEY (year_level_id) REFERENCES year_levels(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SHS Subjects and their strands
CREATE TABLE subject_strands (
  id VARCHAR(50) PRIMARY KEY,
  subject_level_id VARCHAR(50) NOT NULL,
  strand_id VARCHAR(100) NOT NULL,

  FOREIGN KEY (subject_level_id) REFERENCES subject_levels(id) ON DELETE CASCADE,
  FOREIGN KEY (strand_id) REFERENCES strands(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;





--- LEAVE THIS HERE

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
