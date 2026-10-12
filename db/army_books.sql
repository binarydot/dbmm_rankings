
-- --------------------------------------------------------

--
-- Table structure for table `army_books`
--

CREATE TABLE `army_books` (
  `book_id` int(11) NOT NULL,
  `book_name` varchar(40) NOT NULL,
  `system_id` int(11) NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'A'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
