SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `onboarding` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `onboarding` ;

-- -----------------------------------------------------
-- Table `onboarding`.`session`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `onboarding`.`session` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `onboarding`.`usr2session`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `onboarding`.`usr2session` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  `session_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_usr2session_session_idx` (`session_id` ASC) ,
  CONSTRAINT `fk_usr2session_session`
    FOREIGN KEY (`session_id` )
    REFERENCES `onboarding`.`session` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `onboarding`.`rank`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `onboarding`.`rank` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  `rank_value` INT NULL ,
  `score_start` INT NULL ,
  `score_end` INT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `onboarding`.`usr_status`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `onboarding`.`usr_status` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `usr_id` INT NULL ,
  `score` INT NULL ,
  `rank_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_usr_status_rank1_idx` (`rank_id` ASC) ,
  CONSTRAINT `fk_usr_status_rank1`
    FOREIGN KEY (`rank_id` )
    REFERENCES `onboarding`.`rank` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `onboarding`.`content_type`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `onboarding`.`content_type` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `onboarding`.`content`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `onboarding`.`content` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  `body` TEXT NULL ,
  `url` VARCHAR(255) NULL ,
  `session_id` INT NOT NULL ,
  `content_type_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_video_session1_idx` (`session_id` ASC) ,
  INDEX `fk_content_content_type1_idx` (`content_type_id` ASC) ,
  CONSTRAINT `fk_video_session1`
    FOREIGN KEY (`session_id` )
    REFERENCES `onboarding`.`session` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_content_content_type1`
    FOREIGN KEY (`content_type_id` )
    REFERENCES `onboarding`.`content_type` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `onboarding`.`activity_type`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `onboarding`.`activity_type` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  `description` TEXT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `onboarding`.`activity_status`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `onboarding`.`activity_status` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL ,
  `activity_value` INT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `onboarding`.`activity`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `onboarding`.`activity` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  `session_id` INT NOT NULL ,
  `activity_type_id` INT NOT NULL ,
  `activity_status_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_activity_session1_idx` (`session_id` ASC) ,
  INDEX `fk_activity_activity_type1_idx` (`activity_type_id` ASC) ,
  INDEX `fk_activity_activity_status1_idx` (`activity_status_id` ASC) ,
  CONSTRAINT `fk_activity_session1`
    FOREIGN KEY (`session_id` )
    REFERENCES `onboarding`.`session` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_activity_activity_type1`
    FOREIGN KEY (`activity_type_id` )
    REFERENCES `onboarding`.`activity_type` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_activity_activity_status1`
    FOREIGN KEY (`activity_status_id` )
    REFERENCES `onboarding`.`activity_status` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `onboarding`.`question`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `onboarding`.`question` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  `description` VARCHAR(45) NULL ,
  `activity_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_question_activity1_idx` (`activity_id` ASC) ,
  CONSTRAINT `fk_question_activity1`
    FOREIGN KEY (`activity_id` )
    REFERENCES `onboarding`.`activity` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `onboarding`.`question_option`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `onboarding`.`question_option` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NULL ,
  `question_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_question_option_question1_idx` (`question_id` ASC) ,
  CONSTRAINT `fk_question_option_question1`
    FOREIGN KEY (`question_id` )
    REFERENCES `onboarding`.`question` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `onboarding`.`answer`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `onboarding`.`answer` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `answercol` VARCHAR(45) NULL ,
  `question_option_id` INT NOT NULL ,
  `question_id` INT NOT NULL ,
  `usr_id` VARCHAR(45) NULL ,
  `score` INT NULL ,
  `answer` VARCHAR(255) NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_answer_question_option1_idx` (`question_option_id` ASC) ,
  INDEX `fk_answer_question1_idx` (`question_id` ASC) ,
  CONSTRAINT `fk_answer_question_option1`
    FOREIGN KEY (`question_option_id` )
    REFERENCES `onboarding`.`question_option` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_answer_question1`
    FOREIGN KEY (`question_id` )
    REFERENCES `onboarding`.`question` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

USE `onboarding` ;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

