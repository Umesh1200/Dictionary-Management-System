<!-- sample-data.sql content placeholder -->
INSERT INTO users (username, email, password_hash, role)
VALUES ('admin', 'admin@site.com', SHA2('admin123', 256), 'admin');

INSERT INTO words (word, definition, example, created_by)
VALUES 
('eloquent', 'Fluent or persuasive in speaking or writing.', 'She gave an eloquent speech.', 1),
('serendipity', 'The occurrence of events by chance in a happy way.', 'Meeting her was pure serendipity.', 1);

INSERT INTO categories (name) VALUES ('Language'), ('Emotion');
INSERT INTO tags (name) VALUES ('Formal'), ('Positive');
