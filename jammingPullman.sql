CREATE TABLE `jammingPullman` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_tracker` varchar(15) DEFAULT NULL,
  `patente` varchar(15) DEFAULT NULL,
  `evento` varchar(45) DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `lat` varchar(15) DEFAULT NULL,
  `lng` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_patente` (`patente`),
  KEY `idx_fecha` (`fecha`)
) ENGINE=InnoDB AUTO_INCREMENT=37205 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
