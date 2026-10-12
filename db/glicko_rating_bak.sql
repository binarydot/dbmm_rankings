
-- --------------------------------------------------------

--
-- Table structure for table `glicko_rating_bak`
--

CREATE TABLE `glicko_rating_bak` (
  `rating_id` int(11) NOT NULL,
  `competitor_id` int(11) NOT NULL DEFAULT 0,
  `match_id` int(11) NOT NULL DEFAULT 0,
  `rating` int(11) NOT NULL DEFAULT 0,
  `reliability` decimal(6,2) NOT NULL DEFAULT 0.00,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('A','S') NOT NULL DEFAULT 'A',
  `rating_date` int(11) NOT NULL DEFAULT 0,
  `match_date` date NOT NULL,
  `rating_before` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
