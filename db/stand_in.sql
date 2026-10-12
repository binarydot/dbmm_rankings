
-- --------------------------------------------------------

--
-- Table structure for table `stand_in`
--

CREATE TABLE `stand_in` (
  `stand_in_id` int(11) NOT NULL,
  `competition_id` int(11) NOT NULL,
  `competitor_id` int(11) NOT NULL,
  `stood_in_for` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
