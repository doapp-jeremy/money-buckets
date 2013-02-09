SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `moneybuckets` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `moneybuckets` ;

-- -----------------------------------------------------
-- Table `moneybuckets`.`users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `moneybuckets`.`users` ;

CREATE  TABLE IF NOT EXISTS `moneybuckets`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `email` VARCHAR(255) NOT NULL ,
  `facebook_id` BIGINT(20) NULL ,
  `created` DATETIME NULL ,
  `modified` DATETIME NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `email_UNIQUE` (`email` ASC) ,
  UNIQUE INDEX `facebook_id_UNIQUE` (`facebook_id` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `moneybuckets`.`buckets`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `moneybuckets`.`buckets` ;

CREATE  TABLE IF NOT EXISTS `moneybuckets`.`buckets` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `account_id` INT NULL ,
  `name` VARCHAR(255) NOT NULL ,
  `description` TEXT NULL ,
  `opening_balance` DECIMAL(20,2) NULL DEFAULT 0 ,
  `available_balance` DECIMAL(20,2) NULL DEFAULT 0 ,
  `actual_balance` DECIMAL(20,2) NULL DEFAULT 0 ,
  `created` DATETIME NULL ,
  `modified` DATETIME NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_buckets_account` (`account_id` ASC) ,
  CONSTRAINT `fk_buckets_account`
    FOREIGN KEY (`account_id` )
    REFERENCES `moneybuckets`.`accounts` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `moneybuckets`.`accounts`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `moneybuckets`.`accounts` ;

CREATE  TABLE IF NOT EXISTS `moneybuckets`.`accounts` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `user_id` INT NOT NULL ,
  `name` VARCHAR(255) NOT NULL ,
  `unallocated_bucket_id` INT NULL ,
  `created` DATETIME NULL ,
  `modified` DATETIME NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_accounts_unallocated_bucket` (`unallocated_bucket_id` ASC) ,
  INDEX `fk_accounts_user` (`user_id` ASC) ,
  CONSTRAINT `fk_accounts_unallocated_bucket`
    FOREIGN KEY (`unallocated_bucket_id` )
    REFERENCES `moneybuckets`.`buckets` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_accounts_user`
    FOREIGN KEY (`user_id` )
    REFERENCES `moneybuckets`.`users` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `moneybuckets`.`accounts_users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `moneybuckets`.`accounts_users` ;

CREATE  TABLE IF NOT EXISTS `moneybuckets`.`accounts_users` (
  `account_id` INT NOT NULL ,
  `user_id` INT NOT NULL ,
  PRIMARY KEY (`account_id`, `user_id`) ,
  INDEX `fk_accounts_users_account` (`account_id` ASC) ,
  INDEX `fk_accounts_users_user` (`user_id` ASC) ,
  CONSTRAINT `fk_accounts_users_account`
    FOREIGN KEY (`account_id` )
    REFERENCES `moneybuckets`.`accounts` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_accounts_users_user`
    FOREIGN KEY (`user_id` )
    REFERENCES `moneybuckets`.`users` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `moneybuckets`.`bank_accounts`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `moneybuckets`.`bank_accounts` ;

CREATE  TABLE IF NOT EXISTS `moneybuckets`.`bank_accounts` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `account_id` INT NOT NULL ,
  `name` VARCHAR(255) NOT NULL ,
  `opening_balance` DECIMAL(20,2) NULL DEFAULT 0 ,
  `current_balance` DECIMAL(20,2) NULL DEFAULT 0 ,
  `created` DATETIME NULL ,
  `modified` DATETIME NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_bank_accounts_account` (`account_id` ASC) ,
  CONSTRAINT `fk_bank_accounts_account`
    FOREIGN KEY (`account_id` )
    REFERENCES `moneybuckets`.`accounts` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `moneybuckets`.`accounts_bank_accounts`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `moneybuckets`.`accounts_bank_accounts` ;

CREATE  TABLE IF NOT EXISTS `moneybuckets`.`accounts_bank_accounts` (
  `account_id` INT NOT NULL ,
  `bank_account_id` INT NULL ,
  PRIMARY KEY (`account_id`) ,
  INDEX `fk_accounts_bank_accounts_account` (`account_id` ASC) ,
  INDEX `fk_accounts_bank_accounts_bank_account` (`bank_account_id` ASC) ,
  CONSTRAINT `fk_accounts_bank_accounts_account`
    FOREIGN KEY (`account_id` )
    REFERENCES `moneybuckets`.`accounts` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_accounts_bank_accounts_bank_account`
    FOREIGN KEY (`bank_account_id` )
    REFERENCES `moneybuckets`.`bank_accounts` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `moneybuckets`.`transactions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `moneybuckets`.`transactions` ;

CREATE  TABLE IF NOT EXISTS `moneybuckets`.`transactions` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `bank_account_id` INT NOT NULL ,
  `user_id` INT NOT NULL ,
  `creator_id` INT NOT NULL ,
  `date` DATE NOT NULL ,
  `where` VARCHAR(255) NULL ,
  `amount` DECIMAL(20,2) NOT NULL DEFAULT 0 ,
  `created` DATETIME NULL ,
  `modified` DATETIME NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_transactions_bank_account` (`bank_account_id` ASC) ,
  INDEX `fk_transactions_user` (`user_id` ASC) ,
  INDEX `fk_transactions_creator` (`creator_id` ASC) ,
  INDEX `bank_account_date` (`date` ASC, `bank_account_id` ASC) ,
  INDEX `where` (`where` ASC) ,
  CONSTRAINT `fk_transactions_bank_account`
    FOREIGN KEY (`bank_account_id` )
    REFERENCES `moneybuckets`.`bank_accounts` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_transactions_user`
    FOREIGN KEY (`user_id` )
    REFERENCES `moneybuckets`.`users` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_transactions_creator`
    FOREIGN KEY (`creator_id` )
    REFERENCES `moneybuckets`.`users` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `moneybuckets`.`transaction_entries`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `moneybuckets`.`transaction_entries` ;

CREATE  TABLE IF NOT EXISTS `moneybuckets`.`transaction_entries` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `transaction_id` INT NOT NULL ,
  `bucket_id` INT NOT NULL ,
  `user_id` INT NOT NULL ,
  `amount` DECIMAL(20,2) NULL DEFAULT 0 ,
  `created` DATETIME NULL ,
  `modified` DATETIME NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_transaction_entries_transcation` (`transaction_id` ASC) ,
  INDEX `fk_transaction_entries_bucket` (`bucket_id` ASC) ,
  INDEX `fk_transaction_entries_user` (`user_id` ASC) ,
  CONSTRAINT `fk_transaction_entries_transcation`
    FOREIGN KEY (`transaction_id` )
    REFERENCES `moneybuckets`.`transactions` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_transaction_entries_bucket`
    FOREIGN KEY (`bucket_id` )
    REFERENCES `moneybuckets`.`buckets` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_transaction_entries_user`
    FOREIGN KEY (`user_id` )
    REFERENCES `moneybuckets`.`users` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

