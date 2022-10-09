
CREATE TABLE `{{prefix}}cemeteries` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `iframe` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


ALTER TABLE `{{prefix}}cemeteries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `index_cemeteries` (`name`);

ALTER TABLE `{{prefix}}cemeteries`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;

