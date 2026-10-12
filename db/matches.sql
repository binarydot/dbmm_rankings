
-- --------------------------------------------------------

--
-- Table structure for table `matches`
--

CREATE TABLE `matches` (
  `match_id` int(11) NOT NULL,
  `competition_id` int(11) NOT NULL DEFAULT 0,
  `round` int(11) NOT NULL DEFAULT 0,
  `winner_score` decimal(12,2) NOT NULL DEFAULT 0.00,
  `loser_score` decimal(12,2) NOT NULL DEFAULT 0.00,
  `glicko_eligible` tinyint(1) NOT NULL DEFAULT 0,
  `glicko_processed` tinyint(1) NOT NULL DEFAULT 0,
  `match_date` date NOT NULL DEFAULT '0000-00-00',
  `match_seqno` varchar(16) NOT NULL DEFAULT '0',
  `pred_winner_score` decimal(10,0) NOT NULL DEFAULT 0,
  `pred_loser_score` decimal(10,0) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
