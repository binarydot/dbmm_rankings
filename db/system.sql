
-- --------------------------------------------------------

--
-- Table structure for table `system`
--

CREATE TABLE `system` (
  `system_id` int(11) NOT NULL,
  `system_name` varchar(20) NOT NULL DEFAULT '',
  `version` varchar(10) NOT NULL DEFAULT '',
  `rules_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
