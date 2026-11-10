-- 1. JOIN QUERY: List all books along with their Author's name and Category name.
-- Why: The books table only holds author_id and category_id. We need JOINs to see the actual text.
SELECT 
    books.title AS Book_Title, 
    authors.name AS Author_Name, 
    categories.name AS Category
FROM books
JOIN authors ON books.author_id = authors.id
JOIN categories ON books.category_id = categories.id;

-- 2. JOIN QUERY: Show all books that are currently on loan, including the Member's name.
-- Why: Proves Business Rule #2 (tracking who has what). Return_date is NULL means they haven't brought it back.
SELECT 
    members.name AS Member_Name, 
    books.title AS Book_Title, 
    loans.issue_date, 
    loans.due_date
FROM loans
JOIN members ON loans.member_id = members.id
JOIN books ON loans.book_id = books.id
WHERE loans.return_date IS NULL;

-- 3. JOIN QUERY: Find Overdue Loans.
-- Why: This is a real-world librarian task. We look for books not returned where the due_date is in the past (using CURDATE()).
SELECT 
    members.name AS Member_Name, 
    books.title AS Book_Title, 
    loans.due_date
FROM loans
JOIN members ON loans.member_id = members.id
JOIN books ON loans.book_id = books.id
WHERE loans.return_date IS NULL 
AND loans.due_date < CURDATE();

-- 4. AGGREGATE QUERY (GROUP BY): Number of books per category.
-- Why: Required by the PDF. This counts how many books belong to each category.
SELECT 
    categories.name AS Category_Name, 
    COUNT(books.id) AS Total_Books
FROM categories
JOIN books ON categories.id = books.category_id
GROUP BY categories.id, categories.name;