ALTER TABLE `ades_eleves` 
CHANGE COLUMN `memo` `memo` BLOB NOT NULL DEFAULT "",
CHANGE COLUMN `nom` `nom` VARCHAR(255) NOT NULL DEFAULT '' ,
CHANGE COLUMN `prenom` `prenom` VARCHAR(255) NOT NULL DEFAULT '' ;

