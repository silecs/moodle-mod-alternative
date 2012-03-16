ALTER TABLE `mod_alternative_options` 
ADD FOREIGN KEY ( `alternative` ) REFERENCES `mod_alternative` (`id`) 
ON DELETE NO ACTION ON UPDATE NO ACTION ;

ALTER TABLE `mod_alternative_optionsgroup` 
ADD FOREIGN KEY ( `option` ) REFERENCES `mod_alternative` (`id`) 
ON DELETE NO ACTION ON UPDATE NO ACTION ;

ALTER TABLE `mod_alternative_registration` 
ADD FOREIGN KEY ( `option` ) REFERENCES `mod_alternative_options` (`id`) 
ON DELETE NO ACTION ON UPDATE NO ACTION ;


