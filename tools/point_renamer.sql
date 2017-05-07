SET @old_point = _utf8'' COLLATE utf8_general_ci;
SET @new_point = _utf8'' COLLATE utf8_general_ci;

UPDATE `diary_points`
SET `text` = IF(`text` = @old_point, @new_point, `text`);
UPDATE `diary_daily_points`
SET `text` = IF(`text` = @old_point, @new_point, `text`);
