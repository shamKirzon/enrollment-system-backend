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
