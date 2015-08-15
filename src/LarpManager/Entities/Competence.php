<?php

/**
 * Auto generated by MySQL Workbench Schema Exporter.
 * Version 2.1.6-dev (doctrine2-annotation) on 2015-08-15 13:14:22.
 * Goto https://github.com/johmue/mysql-workbench-schema-exporter for more
 * information.
 */

namespace LarpManager\Entities;

use LarpManager\Entities\BaseCompetence;
use LarpManager\Entities\Classe;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * LarpManager\Entities\Competence
 *
 * @Entity()
 */
class Competence extends BaseCompetence
{
	
	/**
	 * @ManyToMany(targetEntity="Classe", mappedBy="competencesFavorites")
	 */
	protected $classeFavorites;
	
	/**
	 * @ManyToMany(targetEntity="Classe", mappedBy="competenceNormales")
	 */
	protected $classeNormales;
	
	/**
	 * @ManyToMany(targetEntity="Classe", mappedBy="competenceCreations")
	 */
	protected $classeCreations;
	
	/**
	 * Permet de définir la date de création
	 */
	public function __construct()
	{
		$this->setCreationDate(new \DateTime('NOW'));
		$this->setUpdateDate(new \DateTime('NOW'));
		
		$this->classeFavorites = new ArrayCollection();
		$this->classeNormales = new ArrayCollection();
		$this->classeCreations = new ArrayCollection();
		
		parent::__construct();
	}
	
	public function __toString()
	{
		return $this->getNom();	
	}
	
	/**
	 * Helper pour récupérer l'utilisateur qui a créé la compétence
	 */
	public function getCreator()
	{
		$creator = $this->getUser();
	
		return $creator;
	}
	
	/**
	 * Helper pour définir l'utilisateur qui a créé la compétence
	 * @param User $user
	 */
	public function setCreator($user)
	{
		return $this->setUser($user);
	}
	
	/**
	 * Helper pour récupérer tous les niveaux d'une compétence
	 */
	public function getNiveaux()
	{
		return $this->getCompetenceNiveaus();
	}
	
	/**
	 * Add Classe entity to collection.
	 *
	 * @param \LarpManager\Entities\Classe $classe
	 * @return \LarpManager\Entities\Competence
	 */
	public function addClasseFavorite(Classe $classe)
	{
		$this->classeFavorites[] = $classe;
	
		return $this;
	}
	
	/**
	 * Remove Classe entity from collection.
	 *
	 * @param \LarpManager\Entities\Classe $classe
	 * @return \LarpManager\Entities\Competence
	 */
	public function removeClasseFavorite(Classe $classe)
	{
		$this->classeFavorites->removeElement($classe);
	
		return $this;
	}
	
	/**
	 * Get Objet entity collection.
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getClasseFavorites()
	{
		return $this->classeFavorites;
	}
	
	/**
	 * Add Classe entity to collection.
	 *
	 * @param \LarpManager\Entities\Classe $classe
	 * @return \LarpManager\Entities\Competence
	 */
	public function addClasseNormale(Classe $classe)
	{
		$this->classeNormales[] = $classe;
	
		return $this;
	}
	
	/**
	 * Remove Classe entity from collection.
	 *
	 * @param \LarpManager\Entities\Classe $classe
	 * @return \LarpManager\Entities\Competence
	 */
	public function removeClasseNormale(Classe $classe)
	{
		$this->classeNormales->removeElement($classe);
	
		return $this;
	}
	
	/**
	 * Get Objet entity collection.
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getClasseNormales()
	{
		return $this->classeNormales;
	}
	
	/**
	 * Add Classe entity to collection.
	 *
	 * @param \LarpManager\Entities\Classe $classe
	 * @return \LarpManager\Entities\Competence
	 */
	public function addClasseCreation(Classe $classe)
	{
		$this->classeCreations[] = $classe;
	
		return $this;
	}
	
	/**
	 * Remove Classe entity from collection.
	 *
	 * @param \LarpManager\Entities\Classe $classe
	 * @return \LarpManager\Entities\Competence
	 */
	public function removeClasseCreation(Classe $classe)
	{
		$this->classeCreations->removeElement($classe);
	
		return $this;
	}
	
	/**
	 * Get Objet entity collection.
	 *
	 * @return \Doctrine\Common\Collections\Collection
	 */
	public function getClasseCreations()
	{
		return $this->classeCreations;
	}
}