
-- --------------------------------------------------------

--
-- Table structure for table `competitor`
--

CREATE TABLE `competitor` (
  `competitor_id` int(11) NOT NULL,
  `first_name` varchar(20) NOT NULL DEFAULT '',
  `last_name` varchar(60) NOT NULL DEFAULT '',
  `cr_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `iwf_id` varchar(7) NOT NULL DEFAULT '''None''',
  `rar_id` int(11) NOT NULL,
  `anonymous` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
