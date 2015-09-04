<?php

/**
 * Auto generated by MySQL Workbench Schema Exporter.
 * Version 2.1.6-dev (doctrine2-annotation) on 2015-09-04 09:08:59.
 * Goto https://github.com/johmue/mysql-workbench-schema-exporter for more
 * information.
 */

namespace LarpManager\Entities;

/**
 * LarpManager\Entities\Chronologie
 *
 * @Entity()
 * @Table(name="chronologie", indexes={@Index(name="fk_chronologie_zone_politique1_idx", columns={"zone_politique_id"})})
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="discr", type="string")
 * @DiscriminatorMap({"base":"BaseChronologie", "extended":"Chronologie"})
 */
class BaseChronologie
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Column(name="`date`", type="datetime")
     */
    protected $date;

    /**
     * @Column(type="text")
     */
    protected $description;

    /**
     * @ManyToOne(targetEntity="Territoire", inversedBy="chronologies")
     * @JoinColumn(name="zone_politique_id", referencedColumnName="id")
     */
    protected $territoire;

    public function __construct()
    {
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \LarpManager\Entities\Chronologie
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
     * Set the value of date.
     *
     * @param \DateTime $date
     * @return \LarpManager\Entities\Chronologie
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get the value of date.
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set the value of description.
     *
     * @param string $description
     * @return \LarpManager\Entities\Chronologie
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
     * Set Territoire entity (many to one).
     *
     * @param \LarpManager\Entities\Territoire $territoire
     * @return \LarpManager\Entities\Chronologie
     */
    public function setTerritoire(Territoire $territoire = null)
    {
        $this->territoire = $territoire;

        return $this;
    }

    /**
     * Get Territoire entity (many to one).
     *
     * @return \LarpManager\Entities\Territoire
     */
    public function getTerritoire()
    {
        return $this->territoire;
    }

    public function __sleep()
    {
        return array('id', 'date', 'description', 'zone_politique_id');
    }
}