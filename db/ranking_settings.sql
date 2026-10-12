
-- --------------------------------------------------------

--
-- Table structure for table `ranking_settings`
--

CREATE TABLE `ranking_settings` (
  `rar_id` int(11) NOT NULL,
  `year` smallint(6) NOT NULL,
  `comps` smallint(6) NOT NULL,
  `max_score` smallint(6) NOT NULL,
  `calc_type` char(1) NOT NULL DEFAULT 'B'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci COMMENT='A - averaged rankings, B = best of.  RAR -1 applies if no rar is specified.';
