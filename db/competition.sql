
-- --------------------------------------------------------

--
-- Table structure for table `competition`
--

CREATE TABLE `competition` (
  `competition_id` int(11) NOT NULL,
  `competition_series_id` int(11) NOT NULL DEFAULT 0,
  `competition_name` varchar(80) NOT NULL DEFAULT '',
  `competition_date` date NOT NULL DEFAULT '0000-00-00',
  `type` enum('Singles','Mixed','Doubles') NOT NULL DEFAULT 'Singles',
  `weighting` int(11) NOT NULL DEFAULT 100,
  `protected` tinyint(1) NOT NULL DEFAULT 0,
  `glicko_eligible` tinyint(1) NOT NULL DEFAULT 0,
  `system_id` int(11) NOT NULL DEFAULT 1,
  `rar_id` int(11) NOT NULL DEFAULT 0,
  `scale` varchar(10) NOT NULL DEFAULT '',
  `scale_id` int(11) NOT NULL DEFAULT 1,
  `army_points` int(11) NOT NULL DEFAULT 400,
  `location` varchar(80) NOT NULL,
  `lists_allowed` float NOT NULL DEFAULT 1,
  `rounds` int(11) NOT NULL DEFAULT 4,
  `num_competitors` int(11) NOT NULL,
  `scoring_system_id` int(11) NOT NULL DEFAULT 1,
  `format_id` int(11) NOT NULL,
  `theme_id` int(11) NOT NULL DEFAULT 5,
  `notes` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci COMMENT='Competition_date should be the date of the last day of the c';
