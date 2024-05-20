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
  id VARCHAR(100) PRIMARY KEY,
  name VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Only for SHS
CREATE TABLE strands (
  id VARCHAR(100) PRIMARY KEY,
  name VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE section_levels (
  id VARCHAR(255) PRIMARY KEY,
  section_id VARCHAR(100) NOT NULL,
  year_level_id VARCHAR(255) NOT NULL,
  -- Strands only apply for SHS
  strand_id VARCHAR(100),

  FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE CASCADE,
  FOREIGN KEY (year_level_id) REFERENCES year_levels(id) ON DELETE CASCADE,
  FOREIGN KEY (strand_id) REFERENCES strands(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Transactions

CREATE TABLE transactions (
  id CHAR(36) PRIMARY KEY, -- uuid()
  amount DECIMAL(10, 2) UNSIGNED NOT NULL,
  transaction_number VARCHAR(50) NOT NULL,
  payment_receipt_url TEXT NOT NULL,
)

CREATE TABLE enrollments (
  id INT PRIMARY KEY AUTO_INCREMENT,
  enrolled_at timestamp NOT NULL DEFAULT current_timestamp(),
  section VARCHAR(255) DEFAULT NULL,
  tuition_plan enum('a','b','c','d') NOT NULL,
  status enum('pending','done') NOT NULL DEFAULT 'pending',
  -- `payment_receipt_url` varchar(255) NOT NULL,
  student_id INT UNSIGNED NOT NULL,
  academic_year_id INT UNSIGNED NOT NULL,
  year_level_id VARCHAR(255) NOT NULL,
  transaction_id CHAR(36) NOT NULL UNIQUE,
  tuition_plan_id VARCHAR(20) NOT NULL
) 

ALTER TABLE enrollments
RENAME COLUMN payment_receipt_url TO transaction_id,
MODIFY transaction_id CHAR(36) CONSTRAINT enrollments_transaction_id_foreign FOREIGN KEY REFERENCES transactions(id)

INSERT INTO `transactions`(`amount`, `payment_receipt_url`) VALUES (40000, 'hello/world');
-- unhex(replace(uuid(),'-',''))
INSERT INTO `enrollments`(`section`, `tuition_plan`, `transaction_id`, `student_id`, `academic_year_id`, `year_level_id`) 
VALUES ('Agatha','a','a3d8d576-1019-11ef-bee1-00e18ce201d5','5',1,'g12')

-- Tuition

CREATE TABLE tuition_plans (
  id VARCHAR(20) PRIMARY KEY,
  name VARCHAR(50) NOT NULL UNIQUE, -- Not sure about this column
  description TEXT NOT NULL,
  amount DECIMAL(10, 2) UNSIGNED NOT NULL DEFAULT 0.0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- The amount of tuition per year?
CREATE TABLE tuition_plan_levels (
  id VARCHAR(50) PRIMARY KEY,
  tuition_plan_id VARCHAR(20) NOT NULL,
  year_level_id VARCHAR(255) NOT NULL,

  FOREIGN KEY (tuition_plan_id) REFERENCES tuition_plans(id) ON DELETE CASCADE,
  FOREIGN KEY (year_level_id) REFERENCES year_levels(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tuitions (
  id VARCHAR(20) PRIMARY KEY,
  amount DECIMAL(10, 2) UNSIGNED NOT NULL DEFAULT 0.0,
  year_level_id VARCHAR(255) NOT NULL,
)

--- Subjects

CREATE TABLE subjects (
  id VARCHAR(50) PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
)

CREATE TABLE subject_levels (
  id VARCHAR(50) PRIMARY KEY,
  subject_id VARCHAR(50) NOT NULL,
  year_level_id VARCHAR(255) NOT NULL,
  strand_id VARCHAR(255),

  FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
  FOREIGN KEY (year_level_id) REFERENCES year_levels(id) ON DELETE CASCADE,
  FOREIGN KEY (strand_id) REFERENCES year_levels(strand) ON DELETE CASCADE
)
