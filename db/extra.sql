
--
-- Indexes for dumped tables
--

--
-- Indexes for table `armies`
--
ALTER TABLE `armies`
  ADD PRIMARY KEY (`army_id`),
  ADD UNIQUE KEY `name` (`name`,`system_id`,`version`),
  ADD UNIQUE KEY `book_number` (`book_number`,`list_number`,`system_id`,`version`);

--
-- Indexes for table `army_books`
--
ALTER TABLE `army_books`
  ADD PRIMARY KEY (`book_id`),
  ADD KEY `system_id` (`system_id`);

--
-- Indexes for table `competition`
--
ALTER TABLE `competition`
  ADD PRIMARY KEY (`competition_id`),
  ADD KEY `system_id` (`system_id`),
  ADD KEY `rar_id` (`rar_id`);

--
-- Indexes for table `competition_flag`
--
ALTER TABLE `competition_flag`
  ADD PRIMARY KEY (`competition_id`,`flag_value`);

--
-- Indexes for table `competition_series`
--
ALTER TABLE `competition_series`
  ADD PRIMARY KEY (`competition_series_id`);

--
-- Indexes for table `competitor`
--
ALTER TABLE `competitor`
  ADD PRIMARY KEY (`competitor_id`),
  ADD KEY `iwf_id` (`iwf_id`),
  ADD KEY `rar_id` (`rar_id`);

--
-- Indexes for table `format`
--
ALTER TABLE `format`
  ADD PRIMARY KEY (`format_id`);

--
-- Indexes for table `glicko_rating`
--
ALTER TABLE `glicko_rating`
  ADD PRIMARY KEY (`rating_id`),
  ADD UNIQUE KEY `enforce_uniqueness` (`rating_date`,`competitor_id`) COMMENT 'Stop you inserting a player twice on the same date and round',
  ADD KEY `competitor_id` (`competitor_id`,`status`),
  ADD KEY `rating_date` (`rating_date`),
  ADD KEY `match_date` (`match_date`);

--
-- Indexes for table `glicko_rating_bak`
--
ALTER TABLE `glicko_rating_bak`
  ADD PRIMARY KEY (`rating_id`),
  ADD KEY `competitor_id` (`competitor_id`,`status`),
  ADD KEY `rating_date` (`rating_date`),
  ADD KEY `match_date` (`match_date`);

--
-- Indexes for table `glicko_settings`
--
ALTER TABLE `glicko_settings`
  ADD PRIMARY KEY (`juristiction_id`);

--
-- Indexes for table `matches`
--
ALTER TABLE `matches`
  ADD PRIMARY KEY (`match_id`),
  ADD KEY `glicko_eligible` (`glicko_eligible`,`glicko_processed`),
  ADD KEY `match_seqno` (`match_seqno`);

--
-- Indexes for table `match_participant`
--
ALTER TABLE `match_participant`
  ADD PRIMARY KEY (`match_id`,`competitor_id`),
  ADD KEY `army_id` (`army_id`),
  ADD KEY `wdbi_competitor_id_match_id_f998be4dd539` (`competitor_id`,`match_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`permission_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `position`
--
ALTER TABLE `position`
  ADD PRIMARY KEY (`position_id`),
  ADD UNIQUE KEY `competition_id` (`competition_id`,`competitor_id`),
  ADD KEY `army_id` (`army_id`);

--
-- Indexes for table `ranking`
--
ALTER TABLE `ranking`
  ADD KEY `competitor_id` (`competitor_id`,`date`);

--
-- Indexes for table `ranking_settings`
--
ALTER TABLE `ranking_settings`
  ADD PRIMARY KEY (`rar_id`,`year`);

--
-- Indexes for table `rar`
--
ALTER TABLE `rar`
  ADD PRIMARY KEY (`rar_id`),
  ADD UNIQUE KEY `rar_tag` (`rar_tag`);

--
-- Indexes for table `rar_group`
--
ALTER TABLE `rar_group`
  ADD PRIMARY KEY (`rar_group_id`);

--
-- Indexes for table `rar_group_link`
--
ALTER TABLE `rar_group_link`
  ADD PRIMARY KEY (`rar_group_id`,`rar_id`);

--
-- Indexes for table `scale`
--
ALTER TABLE `scale`
  ADD PRIMARY KEY (`scale_id`);

--
-- Indexes for table `scoring_system`
--
ALTER TABLE `scoring_system`
  ADD PRIMARY KEY (`scoring_system_id`);

--
-- Indexes for table `stand_in`
--
ALTER TABLE `stand_in`
  ADD PRIMARY KEY (`stand_in_id`),
  ADD UNIQUE KEY `competition_id` (`competition_id`,`competitor_id`);

--
-- Indexes for table `system`
--
ALTER TABLE `system`
  ADD PRIMARY KEY (`system_id`);

--
-- Indexes for table `theme`
--
ALTER TABLE `theme`
  ADD PRIMARY KEY (`theme_id`);

--
-- Indexes for table `umpire`
--
ALTER TABLE `umpire`
  ADD PRIMARY KEY (`umpire_id`),
  ADD UNIQUE KEY `competition_id` (`competition_id`,`competitor_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_name` (`user_name`);

--
-- Indexes for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD PRIMARY KEY (`user_id`,`permission_id`);

--
-- Indexes for table `user_series`
--
ALTER TABLE `user_series`
  ADD PRIMARY KEY (`user_id`,`competition_series_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `armies`
--
ALTER TABLE `armies`
  MODIFY `army_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `army_books`
--
ALTER TABLE `army_books`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `competition`
--
ALTER TABLE `competition`
  MODIFY `competition_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `competition_series`
--
ALTER TABLE `competition_series`
  MODIFY `competition_series_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `competitor`
--
ALTER TABLE `competitor`
  MODIFY `competitor_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `format`
--
ALTER TABLE `format`
  MODIFY `format_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `glicko_rating`
--
ALTER TABLE `glicko_rating`
  MODIFY `rating_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `glicko_rating_bak`
--
ALTER TABLE `glicko_rating_bak`
  MODIFY `rating_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `matches`
--
ALTER TABLE `matches`
  MODIFY `match_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `position`
--
ALTER TABLE `position`
  MODIFY `position_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rar`
--
ALTER TABLE `rar`
  MODIFY `rar_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rar_group`
--
ALTER TABLE `rar_group`
  MODIFY `rar_group_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `scale`
--
ALTER TABLE `scale`
  MODIFY `scale_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `scoring_system`
--
ALTER TABLE `scoring_system`
  MODIFY `scoring_system_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stand_in`
--
ALTER TABLE `stand_in`
  MODIFY `stand_in_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system`
--
ALTER TABLE `system`
  MODIFY `system_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `theme`
--
ALTER TABLE `theme`
  MODIFY `theme_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `umpire`
--
ALTER TABLE `umpire`
  MODIFY `umpire_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;
