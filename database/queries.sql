SELECT 
    books.title AS Book_Title, 
    authors.name AS Author_Name, 
    categories.name AS Category
FROM books
JOIN authors ON books.author_id = authors.id
JOIN categories ON books.category_id = categories.id;

SELECT 
    members.name AS Member_Name, 
    books.title AS Book_Title, 
    loans.issue_date, 
    loans.due_date
FROM loans
JOIN members ON loans.member_id = members.id
JOIN books ON loans.book_id = books.id
WHERE loans.return_date IS NULL;

SELECT 
    members.name AS Member_Name, 
    books.title AS Book_Title, 
    loans.due_date
FROM loans
JOIN members ON loans.member_id = members.id
JOIN books ON loans.book_id = books.id
WHERE loans.return_date IS NULL 
AND loans.due_date < CURDATE();

SELECT 
    categories.name AS Category_Name, 
    COUNT(books.id) AS Total_Books
FROM categories
JOIN books ON categories.id = books.category_id
GROUP BY categories.id, categories.name;
