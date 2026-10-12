
-- --------------------------------------------------------

--
-- Table structure for table `rar`
--

CREATE TABLE `rar` (
  `rar_id` int(11) NOT NULL,
  `rar_name` varchar(40) NOT NULL DEFAULT '',
  `rar_tag` char(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci COMMENT='Table to define Ratings Administrative Regions.';
