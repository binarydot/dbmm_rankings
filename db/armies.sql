
-- --------------------------------------------------------

--
-- Table structure for table `armies`
--

CREATE TABLE `armies` (
  `army_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(60) NOT NULL DEFAULT '',
  `book_number` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `list_number` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `start_year` smallint(6) NOT NULL DEFAULT 0 COMMENT 'BC dates negative',
  `end_year` smallint(6) NOT NULL DEFAULT 0 COMMENT 'BC dates negative',
  `system_id` int(11) NOT NULL DEFAULT 0 COMMENT 'rules system eg DBMM',
  `version` tinyint(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'army list book version',
  `book_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
