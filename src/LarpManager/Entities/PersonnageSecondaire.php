<?php

/**
 * Auto generated by MySQL Workbench Schema Exporter.
 * Version 2.1.6-dev (doctrine2-annotation) on 2015-12-22 10:14:34.
 * Goto https://github.com/johmue/mysql-workbench-schema-exporter for more
 * information.
 */

namespace LarpManager\Entities;

use LarpManager\Entities\BasePersonnageSecondaire;

/**
 * LarpManager\Entities\PersonnageSecondaire
 *
 * @Entity()
 */
class PersonnageSecondaire extends BasePersonnageSecondaire
{
	/**
	 * Fourni la liste des compétences
	 */
	public function getCompetences()
	{
		$competences = array();
		$personnageSecondaireCompetences =  $this->getPersonnageSecondaireCompetences();
		foreach ( $personnageSecondaireCompetences as $personnageSecondaireCompetence)
		{
			$competences[] = $personnageSecondaireCompetence->getCompetence();
		}
		return $competences;
	}
	
	/**
	 * Fourni le label de la classe en guide de la label pour l'archétype
	 */
	public function getLabel()
	{
		return $this->getClasse()->getLabel();
	}
	
}