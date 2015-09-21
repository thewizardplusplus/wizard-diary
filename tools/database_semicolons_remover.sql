START TRANSACTION;

UPDATE `diary_points`
SET `text` = TRIM(TRAILING ';' FROM `text`)
WHERE RIGHT(`text`, 1) = ';';

UPDATE `diary_daily_points`
SET `text` = TRIM(TRAILING ';' FROM `text`)
WHERE RIGHT(`text`, 1) = ';';

COMMIT;
