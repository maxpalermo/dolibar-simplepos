CREATE TABLE IF NOT EXISTS `#__simplepos_settings` (
    `rowid` INT NOT NULL AUTO_INCREMENT , 
    `fk_customer` INT NOT NULL , 
    `fk_warehouse` INT NOT NULL , 
    `fk_pricelevel` INT NOT NULL , 
    `ticket_path` varchar(255) NOT NULL , 
    `cash_register` VARCHAR(255) NULL , 
    `serial_port` VARCHAR(255) NULL , 
    `baudrate` INT NULL , 
    `parity` VARCHAR(1) NULL , 
    `charlength` INT NULL , 
    `stopbits` INT NULL , 
    `flowcontrol` VARCHAR(255) NULL , 
    PRIMARY KEY (`rowid`)
) ENGINE = InnoDB COMMENT = 'simplePOS module settings';

