
-- --------------------------------------------------------

--
-- Table structure for table `ranking`
--

CREATE TABLE `ranking` (
  `competitor_id` int(11) NOT NULL DEFAULT 0,
  `ranking` decimal(8,2) NOT NULL DEFAULT 0.00,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
