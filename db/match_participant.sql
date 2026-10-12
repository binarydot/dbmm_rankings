
-- --------------------------------------------------------

--
-- Table structure for table `match_participant`
--

CREATE TABLE `match_participant` (
  `match_id` int(11) NOT NULL DEFAULT 0,
  `competitor_id` int(11) NOT NULL DEFAULT 0,
  `side` enum('W','L') NOT NULL DEFAULT 'W',
  `army_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
