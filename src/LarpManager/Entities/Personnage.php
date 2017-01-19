<?php

/**
 * LarpManager - A Live Action Role Playing Manager
 * Copyright (C) 2016 Kevin Polez
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Auto generated by MySQL Workbench Schema Exporter.
 * Version 2.1.6-dev (doctrine2-annotation) on 2015-06-30 20:35:19.
 * Goto https://github.com/johmue/mysql-workbench-schema-exporter for more
 * information.
 */

namespace LarpManager\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use LarpManager\Entities\BasePersonnage;

/**
 * LarpManager\Entities\Personnage
 *
 * @Entity(repositoryClass="LarpManager\Repository\PersonnageRepository")
 */
class Personnage extends BasePersonnage
{
	/**
	 * Constructeur
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setXp(0);
		$this->setVivant(true);
	}
	
	/**
	 * Détermine si le personnage participe à un GN
	 * 
	 * @param Gn $gn
	 */
	public function participeTo(Gn $gn)
	{
		if ( $this->getUser() )
		{
			if ( $participant = $this->getUser()->getParticipant($gn))
			{
				if ( $participant->getBillet())
				{
					return true;
				}
			}
		}
		return false;
	}
	
	/**
	 * Set Participant entity (one to one).
	 *
	 * @return \LarpManager\Entities\Personnage
	 */
	public function setParticipantNull()
	{
		$this->participant = null;
	
		return $this;
	}
	
	/**
	 * Retire le personnage de son groupe
	 */
	public function setGroupeNull()
	{
		$this->groupe = null;
		
		return $this;
	}
	
	
	/**
	 * Affichage
	 */
	public function __toString()
	{
		return $this->getPublicName();
	}
	
	/**
	 * Fourni l'origine du personnage
	 */
	public function getOrigine()
	{
		return $this->getTerritoire();
	}
	
	/**
	 * Détermine si du matériel est necessaire pour ce personnage
	 */
	public function hasMateriel()
	{
		if ( $this->getRenomme() > 0 ) return true;
		
		foreach ( $this->getCompetences() as $competence)
		{
			if ( $competence->getMateriel() ) return true;
		}		
		return false;
					
	}
	
	/**
	 * Fourni les backgrounds du personnage en fonction de la visibilitée
	 * @param unknown $visibility
	 */
	public function getBackgrounds($visibility = null)
	{
		$backgrounds = new ArrayCollection();
		foreach ( $this->getPersonnageBackgrounds() as $background)
		{
			if ( $visibility != null )
			{
				if ( $background->getVisibility() == $visibility )
				{
					$backgrounds[] = $background;
				}
			}
			else
			{
				$backgrounds[] = $background;
			}
				
		}
		return $backgrounds;
	}
	
	/**
	 * Vérifie si le personnage connait cette priere
	 * 
	 * @param Priere $priere
	 * @return boolean
	 */
	public function isKnownPriere(Priere $p)
	{
		foreach ( $this->getPrieres() as $priere)
		{
			if ( $priere == $p ) return true;
		}
		return false;
	}
			
	/**
	 * Vérifie si le personnage connait cette potion
	 * 
	 * @param Potion $priere
	 * @return boolean
	 */
	public function isKnownPotion(Potion $p)
	{
		foreach ( $this->getPotions() as $potion)
		{
			if ( $potion == $p ) return true;
		}
		return false;
	}	

	/**
	 * Vérifie si le personnage connait ce sort
	 * 
	 * @param Sort $sort
	 * @return boolean
	 */
	public function isKnownSort(Sort $s)
	{
		foreach ( $this->getSorts() as $sort)
		{
			if ( $sort == $s ) return true;
		}
		return false;
	}
	
	/**
	 * Vérifie si le personnage connait ce domaine de magie
	 * 
	 * @param Domaine $d
	 */
	public function isKnownDomaine(Domaine $d)
	{
		foreach ( $this->getDomaines() as $domaine)
		{
			if ( $domaine == $d ) return true;
		}
		return false;
	}
	
	/**
	 * Vérifie si le personnage connait cette competence
	 * 
	 * @param Competence $competence
	 * @return boolean
	 */
	public function isKnownCompetence($competence)
	{
		foreach ( $this->getCompetences() as $c)
		{
			if ( $competence == $c ) return true;
		}
		return false;
	}
	
	
	/**
	 * Vérifie si le personnage connait cette langue
	 * @param unknown $langue
	 */
	public function isKnownLanguage($langue)
	{
		foreach ( $this->getPersonnageLangues() as $personnageLangue)
		{
			if ( $personnageLangue->getLangue() == $langue ) return true;
		}
		return false;
	}
	
	/**
	 * Fourni la liste des langues connues
	 */
	public function getLanguages()
	{
		$languages = new ArrayCollection();
		foreach ( $this->getPersonnageLangues() as $personnageLangue)
		{
			$languages[] = $personnageLangue->getLangue();
		}
		return $languages;
	}
	
	/**
	 * Fourni le language
	 * @param unknown $langue
	 */
	public function getPersonnageLangue($langue)
	{
		foreach ( $this->getPersonnageLangues() as $personnageLangue)
		{
			if ( $personnageLangue->getLangue() == $langue ) return $personnageLangue;
		}
		return null;
	}
	
	
	/**
	 * Vérifie si le personnage dispose d'un trigger
	 * @param unknown $tag
	 */
	public function hasTrigger($tag)
	{
		foreach ( $this->getPersonnageTriggers() as $personnageTrigger)
		{
			if ( $personnageTrigger->getTag() == $tag)
			{
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Vérifie si le personnage dispose d'une compétence (quelque soit son niveau)
	 * @param unknown $label
	 */
	public function hasCompetence($label)
	{
		foreach ( $this->getCompetences() as $competence)
		{
			if ( $competence->getCompetenceFamily()->getLabel() == $label)
			{
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Fourni le niveau d'une compétence d'un personnage
	 * @param unknown $label
	 */
	public function getCompetenceNiveau($label)
	{
		$niveau = 0;
		foreach ( $this->getCompetences() as $competence)
		{
			if ( $competence->getCompetenceFamily()->getLabel() == $label)
			{
				if ( $niveau < $competence->getLevel()->getIndex() )
					$niveau = $competence->getLevel()->getIndex();
			}
		}
		return $niveau;
	}
	
	/**
	 * Fourni le niveau d'une compétence pour le score de pugilat
	 * @param unknown $label
	 */
	public function getCompetencePugilat($label)
	{
		$niveau = 0;
		foreach ( $this->getCompetences() as $competence)
		{
			if ( $competence->getCompetenceFamily()->getLabel() == $label)
			{
					$niveau += $competence->getLevel()->getIndex();
			}
		}
		return $niveau;
	}
	
	/**
	 * Fourni le score de pugilat
	 */
	public function getPugilat()
	{
		$pugilat = 0;
		$pugilat = $this->getCompetencePugilat('Agilité')
			+ $this->getCompetencePugilat('Armes à distance')
			+ $this->getCompetencePugilat('Armes à 1 main')
			+ $this->getCompetencePugilat('Armes à 2 mains')
			+ $this->getCompetencePugilat('Armes d\'hast')
			+ $this->getCompetencePugilat('Armure')
			+ $this->getCompetencePugilat('Attaque sournoise')
			+ $this->getCompetencePugilat('Protection')
			+ $this->getCompetencePugilat('Résistance')
			+ $this->getCompetencePugilat('Sauvagerie')
			+ $this->getCompetencePugilat('Stratégie')
			+ $this->getCompetencePugilat('Survie');
		
		// armurerie au niveau initié double le score de pugilat
		if ( $this->getCompetenceNiveau('Armurerie') >= 2 )
		{
			$pugilat = $pugilat * 2;
		}
		
		return $pugilat;
	}
	
	/**
	 * Fourni le trigger correspondant au tag
	 * @param unknown $tag
	 */
	public function getTrigger($tag)
	{
		foreach ( $this->getPersonnageTriggers() as $personnageTrigger)
		{
			if ( $personnageTrigger->getTag() == $tag)
			{
				return $personnageTrigger;
			}
		}
		return null;
	}
	
	/**
	 * Fourni le surnom si celui-ci a été précisé
	 * sinon fourni le nom
	 */
	public function getPublicName()
	{
		if ( $this->getSurnom() ) return $this->getSurnom();
		return $this->getNom();
	}
	
	/**
	 * Fourni l'identité complete d'un personnage
	 */
	public function getIdentity()
	{
		$groupe = $this->getGroupe();
		
		$identity = $this->getNom().' '.$this->getSurnom().' (';
		if ( $groupe ) $identity .= $groupe->getNom();
		$identity .= ')';
		return $identity;
	}
	
	/**
	 * Indique si le personnage est un Fanatique
	 */
	public function isFanatique()
	{
		$personnagesReligions = $this->getPersonnagesReligions();
		foreach ( $personnagesReligions as $personnageReligion )
		{
			if ( $personnageReligion->getReligionLevel()->getIndex() == 3 )
			{
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Indique si le personnage est un Fervent
	 */
	public function isFervent()
	{
		$personnagesReligions = $this->getPersonnagesReligions();
		foreach ( $personnagesReligions as $personnageReligion )
		{
			if ( $personnageReligion->getReligionLevel()->getIndex() == 2 )
			{
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Fourni la religion principale du personnage
	 */
	public function getMainReligion()
	{
		$religion = null;
		$index = 0;
		$personnagesReligions = $this->getPersonnagesReligions();
		foreach ( $personnagesReligions as $personnageReligion )
		{
			if ( ! $religion )
			{
				$religion = $personnageReligion->getReligion();
				$index = $personnageReligion->getReligionLevel()->getIndex();
			}
			else
			{
				if ( $index < $personnageReligion->getReligionLevel()->getIndex() )
				{
					$religion = $personnageReligion->getReligion();
					$index = $personnageReligion->getReligionLevel()->getIndex();
				}
			}
		}
		
		return $religion;
		
	}
	
	/**
	 * Fourni la liste des groupes secondaires pour lesquel ce personnage est chef
	 */
	public function getSecondaryGroupsAsChief()
	{
		return $this->getSecondaryGroups();
	}
	
	/**
	 * Fourni la description du membre correspondant au groupe passé en paramètre
	 * @param SecondaryGroup $groupe
	 */
	public function getMembre(SecondaryGroup $groupe)
	{
		foreach ($this->getMembres() as $membre)
		{
			if ( $membre->getSecondaryGroup() == $groupe)
				return $membre;
		}
		return false;
	}
	
	/**
	 * Ajoute des points d'experience à un personnage
	 *  
	 * @param integer $xp
	 */
	public function addXp($xp)
	{
		$this->setXp($this->getXp() + $xp);
		return $this;
	}
	
	/**
	 * Retire des points d'expérience à un personnage
	 * @param integer $xp
	 */
	public function removeXp($xp)
	{
		$this->setXp($this->getXp() - $xp);
		return $this;
	}
	
	public function getXpTotal()
	{
		$total = 0;
		foreach ( $this->getExperienceGains() as $gain)
		{
			$pos = strpos($gain->getExplanation(),"Suppression de la compétence");
			if (  $pos === FALSE )
			{
				$total = $total + $gain->getXpGain();
			}
		}
		return $total;
		
	}
	
	/**
	 * Ajoute des points de renomme à un personnage
	 * @param unknown $renomme
	 */
	public function addRenomme($renomme)
	{
		$this->setRenomme($this->getRenomme() + $renomme);
		return $this;
	}
	
	/**
	 * Retire des points de renomme à un personnage
	 * @param unknown $renomme
	 */
	public function removeRenomme($renomme)
	{
		$this->setRenomme($this->getRenomme() - $renomme);
		return $this;
	}
	
	/**
	 * Recupère le nom de classe genrifié du personnage 
	 * @todo : Evoluer vers un modèle de données ou les libélés de ressource sont variable en fonction du genre
	 */
	public function getClasseName()
	{
		$lGenreMasculin = true;
		if($this->getGenre() != null)
		{
			$lGenreMasculin = $this->getGenre()->getLabel() == "Masculin";
		}
		
		if($this->getClasse() == null) 
		{
			return '';
		}
		else if($lGenreMasculin)
		{
			return $this->getClasse()->getLabelMasculin();
		}
		else 
		{
			return $this->getClasse()->getLabelFeminin();
		}
		
	}
	
	/**
	 * Retire un personnage d'un groupe
	 * 
	 * @param Groupe $groupe
	 */
	public function removeGroupe(Groupe $groupe)
	{
		$groupe->removePersonnage($this);
		$this->setGroupe(null);
	}
				
}