
-- --------------------------------------------------------

--
-- Table structure for table `umpire`
--

CREATE TABLE `umpire` (
  `umpire_id` int(11) NOT NULL,
  `competition_id` int(11) NOT NULL,
  `competitor_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
