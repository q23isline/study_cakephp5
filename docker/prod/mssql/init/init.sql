IF NOT EXISTS(
    SELECT
        *
    FROM
        sys.databases
    WHERE
        name = 'StudyCakePHP'
) BEGIN CREATE DATABASE StudyCakePHP;

END
GO
