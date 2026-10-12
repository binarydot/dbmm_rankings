
-- --------------------------------------------------------

--
-- Table structure for table `user_series`
--

CREATE TABLE `user_series` (
  `user_id` int(11) NOT NULL DEFAULT 0,
  `competition_series_id` int(11) NOT NULL DEFAULT 0,
  `value` int(11) NOT NULL DEFAULT 0,
  `updated` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
