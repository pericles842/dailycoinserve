--crear procedimiento
CREATE PROCEDURE create_timestamps(IN nombre_tabla VARCHAR(50)) BEGIN
SET
    @sql = CONCAT(
        'ALTER TABLE ',
        nombre_tabla,
        ' ADD created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, ADD updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;'
    );

PREPARE stmt
FROM
    @sql;

EXECUTE stmt;

DEALLOCATE PREPARE stmt;

END;