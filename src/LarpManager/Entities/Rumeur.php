<?php

/**
 * Auto generated by MySQL Workbench Schema Exporter.
 * Version 2.1.6-dev (doctrine2-annotation) on 2017-01-09 10:51:56.
 * Goto https://github.com/johmue/mysql-workbench-schema-exporter for more
 * information.
 */

namespace LarpManager\Entities;

use LarpManager\Entities\BaseRumeur;

/**
 * LarpManager\Entities\Rumeur
 *
 * @Entity(repositoryClass="LarpManager\Repository\RumeurRepository")
 */
class Rumeur extends BaseRumeur
{
	/**
	 * Constructeur. Met en place la date de création et de mise à jour de la rumeur
	 */
	function __construct()
	{
		$this->setCreationDate(new \Datetime('NOW'));
		$this->setUpdateDate(new \Datetime('NOW'));
		parent::__construct();
	}
	
	function getVisibility()
	{
		switch (parent::getVisibility())
		{
			case 'disponible' : return 'Non disponible';
			case 'non_disponible' :
			default : return "Brouillon";
		}
	}
}