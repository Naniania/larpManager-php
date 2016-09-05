<?php

/**
 * Auto generated by MySQL Workbench Schema Exporter.
 * Version 2.1.6-dev (doctrine2-annotation) on 2016-09-05 11:00:34.
 * Goto https://github.com/johmue/mysql-workbench-schema-exporter for more
 * information.
 */

namespace LarpManager\Entities;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * LarpManager\Entities\Construction
 *
 * @Entity()
 * @Table(name="construction")
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="discr", type="string")
 * @DiscriminatorMap({"base":"BaseConstruction", "extended":"Construction"})
 */
class BaseConstruction
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Column(type="string", length=45)
     */
    protected $label;

    /**
     * @Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @Column(type="integer")
     */
    protected $defense;

    /**
     * @ManyToMany(targetEntity="Territoire", mappedBy="constructions")
     */
    protected $territoires;

    public function __construct()
    {
        $this->territoires = new ArrayCollection();
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \LarpManager\Entities\Construction
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
     * Set the value of label.
     *
     * @param string $label
     * @return \LarpManager\Entities\Construction
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get the value of label.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set the value of description.
     *
     * @param string $description
     * @return \LarpManager\Entities\Construction
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
     * Set the value of defense.
     *
     * @param integer $defense
     * @return \LarpManager\Entities\Construction
     */
    public function setDefense($defense)
    {
        $this->defense = $defense;

        return $this;
    }

    /**
     * Get the value of defense.
     *
     * @return integer
     */
    public function getDefense()
    {
        return $this->defense;
    }

    /**
     * Add Territoire entity to collection.
     *
     * @param \LarpManager\Entities\Territoire $territoire
     * @return \LarpManager\Entities\Construction
     */
    public function addTerritoire(Territoire $territoire)
    {
        $this->territoires[] = $territoire;

        return $this;
    }

    /**
     * Remove Territoire entity from collection.
     *
     * @param \LarpManager\Entities\Territoire $territoire
     * @return \LarpManager\Entities\Construction
     */
    public function removeTerritoire(Territoire $territoire)
    {
        $this->territoires->removeElement($territoire);

        return $this;
    }

    /**
     * Get Territoire entity collection.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTerritoires()
    {
        return $this->territoires;
    }

    public function __sleep()
    {
        return array('id', 'label', 'description', 'defense');
    }
}