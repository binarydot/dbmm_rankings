
-- --------------------------------------------------------

--
-- Table structure for table `position`
--

CREATE TABLE `position` (
  `position_id` int(11) NOT NULL,
  `competition_id` int(11) NOT NULL DEFAULT 0,
  `competitor_id` int(11) NOT NULL DEFAULT 0,
  `position` int(11) DEFAULT 0,
  `score` decimal(12,2) DEFAULT 0.00,
  `army_id` int(11) DEFAULT NULL,
  `type` char(1) NOT NULL DEFAULT 'P',
  `bonus` int(11) NOT NULL,
  `team` varchar(100) NOT NULL,
  `pairing` int(11) NOT NULL DEFAULT 0,
  `army_region` varchar(100) DEFAULT NULL,
  `army_period` varchar(30) DEFAULT NULL,
  `army_general` varchar(50) DEFAULT NULL,
  `club` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
