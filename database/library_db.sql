CREATE DATABASE IF NOT EXISTS library_db;
USE library_db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE authors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    isbn VARCHAR(20) NOT NULL UNIQUE,
    author_id INT NOT NULL,
    category_id INT NOT NULL,
    total_copies INT NOT NULL DEFAULT 1,
    available_copies INT NOT NULL DEFAULT 1,
    FOREIGN KEY (author_id) REFERENCES authors(id),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_number VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    status ENUM('active', 'suspended') DEFAULT 'active'
);

CREATE TABLE loans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,
    member_id INT NOT NULL,
    issue_date DATE NOT NULL,
    due_date DATE NOT NULL,
    return_date DATE DEFAULT NULL,
    FOREIGN KEY (book_id) REFERENCES books(id),
    FOREIGN KEY (member_id) REFERENCES members(id)
);

INSERT INTO users (username, password) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

INSERT INTO authors (name) VALUES 
('Chinua Achebe'), ('Ngugi wa Thiong\'o'), ('J.K. Rowling'), ('George Orwell'), ('Jane Austen');

INSERT INTO categories (name) VALUES 
('African Literature'), ('Fantasy'), ('Dystopian'), ('Classic Fiction'), ('Science Fiction');

INSERT INTO books (title, isbn, author_id, category_id, total_copies, available_copies) VALUES
('Things Fall Apart', '978-0385474542', 1, 1, 5, 5),
('Weep Not, Child', '978-0143106692', 2, 1, 3, 3),
('Harry Potter', '978-0439708180', 3, 2, 10, 10),
('1984', '978-0451524935', 4, 3, 4, 4),
('Animal Farm', '978-0452284241', 4, 3, 6, 6),
('Pride and Prejudice', '978-0141439518', 5, 4, 2, 2),
('The Hobbit', '978-0547928227', 3, 2, 5, 5),
('Arrow of God', '978-0385017083', 1, 1, 3, 3),
('Brave New World', '978-0060850524', 4, 5, 4, 4),
('Sense and Sensibility', '978-0141439662', 5, 4, 2, 2);

INSERT INTO members (member_number, name, email, status) VALUES
('M001', 'John Doe', 'john@example.com', 'active'),
('M002', 'Jane Smith', 'jane@example.com', 'active'),
('M003', 'Ali Hassan', 'ali@example.com', 'suspended'),
('M004', 'Mary Wanjiku', 'mary@example.com', 'active'),
('M005', 'Peter Okello', 'peter@example.com', 'active'),
('M006', 'Grace Mwangi', 'grace@example.com', 'active'),
('M007', 'David Ochieng', 'david@example.com', 'active'),
('M008', 'Sarah Connor', 'sarah@example.com', 'active'),
('M009', 'James Bond', 'james@example.com', 'active'),
('M010', 'Lara Croft', 'lara@example.com', 'active');

INSERT INTO loans (book_id, member_id, issue_date, due_date, return_date) VALUES
(1, 1, '2023-10-01', '2023-10-15', '2023-10-10'),
(2, 2, '2023-10-05', '2023-10-19', NULL),
(3, 3, '2023-10-10', '2023-10-24', NULL),
(4, 4, '2023-10-12', '2023-10-26', '2023-10-20'),
(5, 5, '2023-10-15', '2023-10-29', NULL),
(6, 1, '2023-10-18', '2023-11-01', NULL),
(7, 6, '2023-10-20', '2023-11-03', NULL),
(8, 7, '2023-10-21', '2023-11-04', NULL),
(9, 8, '2023-10-22', '2023-11-05', NULL),
(10, 9, '2023-10-23', '2023-11-06', '2023-10-25');P
