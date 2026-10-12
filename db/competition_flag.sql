
-- --------------------------------------------------------

--
-- Table structure for table `competition_flag`
--

CREATE TABLE `competition_flag` (
  `competition_id` int(11) NOT NULL DEFAULT 0,
  `flag_value` varchar(20) NOT NULL DEFAULT '',
  `cr_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
