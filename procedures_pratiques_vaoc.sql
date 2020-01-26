//nettoyage une fois le bilan affiché
SET @partie=1;
DELETE FROM `vaoc`.`tab_vaoc_bataille` WHERE `tab_vaoc_bataille`.`ID_PARTIE` = @partie;
DELETE FROM `vaoc`.`tab_vaoc_bataille_pions` WHERE `tab_vaoc_bataille_pions`.`ID_PARTIE` = @partie;
DELETE FROM `vaoc`.`tab_vaoc_message` WHERE `tab_vaoc_message`.`ID_PARTIE` = @partie;
DELETE FROM `vaoc`.`tab_vaoc_forum` WHERE `tab_vaoc_forum`.`ID_PARTIE` = @partie;
DELETE FROM `vaoc`.`tab_vaoc_noms_carte` WHERE `tab_vaoc_noms_carte`.`ID_PARTIE` = @partie;
DELETE FROM `vaoc`.`tab_vaoc_ordre` WHERE `tab_vaoc_ordre`.`ID_PARTIE` = @partie;
DELETE FROM `vaoc`.`tab_vaoc_role` WHERE `tab_vaoc_role`.`ID_PARTIE` = @partie;

//suppression totale complementaire
SET @partie=1;
DELETE FROM `vaoc`.`tab_vaoc_objectifs`  WHERE `tab_vaoc_objectifs` .`ID_PARTIE` = @partie;
DELETE FROM `vaoc`.`tab_vaoc_nation` WHERE `tab_vaoc_nation`.`ID_PARTIE` = @partie;
DELETE FROM `vaoc`.`tab_vaoc_pion` WHERE `tab_vaoc_pion`.`ID_PARTIE` = @partie;
DELETE FROM `vaoc`.`tab_vaoc_modele_pion` WHERE `tab_vaoc_modele_pion`.`ID_PARTIE` = @partie;

//recopie des images
UPDATE `vaoc`.`tab_vaoc_modele_pion` SET `S_IMAGE` = `S_IMAGE` 
WHERE `tab_vaoc_modele_pion`.`ID_MODELE_PION` =10 AND `tab_vaoc_modele_pion`.`ID_PARTIE` =2;

UPDATE `tab_vaoc_modele_pion` as modele1, `tab_vaoc_modele_pion` as modele2
SET
modele1.`S_IMAGE`  = modele2.`S_IMAGE` 
WHERE modele1.ID_MODELE_PION = modele2.ID_MODELE_PION
and modele1.ID_PARTIE=3
and modele2.ID_PARTIE=2;

UPDATE `tab_vaoc_role` as role1, `tab_vaoc_role` as role2
SET
role1.`S_IMAGE`  = role2.`S_IMAGE` 
WHERE role1.ID_ROLE = role2.ID_ROLE
and role1.ID_PARTIE=3
and role2.ID_PARTIE=2;
