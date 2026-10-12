
-- --------------------------------------------------------

--
-- Table structure for table `glicko_settings`
--

CREATE TABLE `glicko_settings` (
  `juristiction_id` int(11) NOT NULL DEFAULT 0,
  `reliability_a` int(11) NOT NULL DEFAULT 0,
  `reliability_b` int(11) NOT NULL DEFAULT 0,
  `reliability_c` int(11) NOT NULL DEFAULT 0,
  `reliability_d` int(11) NOT NULL DEFAULT 0,
  `reliability_e` int(11) NOT NULL DEFAULT 0,
  `max_difference` int(11) NOT NULL DEFAULT 0,
  `pq_factor` int(11) NOT NULL DEFAULT 0,
  `starting_rating` int(11) NOT NULL DEFAULT 0,
  `starting_reliability` decimal(8,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
