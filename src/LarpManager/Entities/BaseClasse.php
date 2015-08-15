<?php

/**
 * Auto generated by MySQL Workbench Schema Exporter.
 * Version 2.1.6-dev (doctrine2-annotation) on 2015-08-15 23:21:42.
 * Goto https://github.com/johmue/mysql-workbench-schema-exporter for more
 * information.
 */

namespace LarpManager\Entities;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * LarpManager\Entities\Classe
 *
 * @Entity()
 * @Table(name="classe")
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="discr", type="string")
 * @DiscriminatorMap({"base":"BaseClasse", "extended":"Classe"})
 */
class BaseClasse
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Column(type="string", length=45, nullable=true)
     */
    protected $label_masculin;

    /**
     * @Column(type="string", length=45, nullable=true)
     */
    protected $label_feminin;

    /**
     * @Column(type="string", length=450, nullable=true)
     */
    protected $description;

    /**
     * @OneToMany(targetEntity="Personnage", mappedBy="classe")
     * @JoinColumn(name="id", referencedColumnName="archetype_id")
     */
    protected $personnages;

    /**
     * @ManyToMany(targetEntity="Groupe", mappedBy="classes")
     */
    protected $groupes;

    public function __construct()
    {
        $this->personnages = new ArrayCollection();
        $this->groupes = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \LarpManager\Entities\Classe
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of label_masculin.
     *
     * @param string $label_masculin
     * @return \LarpManager\Entities\Classe
     */
    public function setLabelMasculin($label_masculin)
    {
        $this->label_masculin = $label_masculin;

        return $this;
    }

    /**
     * Get the value of label_masculin.
     *
     * @return string
     */
    public function getLabelMasculin()
    {
        return $this->label_masculin;
    }

    /**
     * Set the value of label_feminin.
     *
     * @param string $label_feminin
     * @return \LarpManager\Entities\Classe
     */
    public function setLabelFeminin($label_feminin)
    {
        $this->label_feminin = $label_feminin;

        return $this;
    }

    /**
     * Get the value of label_feminin.
     *
     * @return string
     */
    public function getLabelFeminin()
    {
        return $this->label_feminin;
    }

    /**
     * Set the value of description.
     *
     * @param string $description
     * @return \LarpManager\Entities\Classe
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Add Personnage entity to collection (one to many).
     *
     * @param \LarpManager\Entities\Personnage $personnage
     * @return \LarpManager\Entities\Classe
     */
    public function addPersonnage(Personnage $personnage)
    {
        $this->personnages[] = $personnage;

        return $this;
    }

    /**
     * Remove Personnage entity from collection (one to many).
     *
     * @param \LarpManager\Entities\Personnage $personnage
     * @return \LarpManager\Entities\Classe
     */
    public function removePersonnage(Personnage $personnage)
    {
        $this->personnages->removeElement($personnage);

        return $this;
    }

    /**
     * Get Personnage entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPersonnages()
    {
        return $this->personnages;
    }

    /**
     * Add Groupe entity to collection.
     *
     * @param \LarpManager\Entities\Groupe $groupe
     * @return \LarpManager\Entities\Classe
     */
    public function addGroupe(Groupe $groupe)
    {
        $this->groupes[] = $groupe;

        return $this;
    }

    /**
     * Remove Groupe entity from collection.
     *
     * @param \LarpManager\Entities\Groupe $groupe
     * @return \LarpManager\Entities\Classe
     */
    public function removeGroupe(Groupe $groupe)
    {
        $this->groupes->removeElement($groupe);

        return $this;
    }

    /**
     * Get Groupe entity collection.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGroupes()
    {
        return $this->groupes;
    }

    public function __sleep()
    {
        return array('id', 'label_masculin', 'label_feminin', 'description');
    }
}