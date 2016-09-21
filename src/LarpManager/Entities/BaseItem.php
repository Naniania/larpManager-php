<?php

/**
 * Auto generated by MySQL Workbench Schema Exporter.
 * Version 2.1.6-dev (doctrine2-annotation) on 2016-09-21 09:47:55.
 * Goto https://github.com/johmue/mysql-workbench-schema-exporter for more
 * information.
 */

namespace LarpManager\Entities;

/**
 * LarpManager\Entities\Item
 *
 * @Entity()
 * @Table(name="item", indexes={@Index(name="fk_item_qualite1_idx", columns={"qualite_id"}), @Index(name="fk_item_statut1_idx", columns={"statut_id"})})
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="discr", type="string")
 * @DiscriminatorMap({"base":"BaseItem", "extended":"Item"})
 */
class BaseItem
{
    /**
     * @Id
     * @Column(type="integer", options={"unsigned":true})
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Column(type="string", length=45, nullable=true)
     */
    protected $label;

    /**
     * @Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @Column(type="integer")
     */
    protected $numero;

    /**
     * @Column(type="integer")
     */
    protected $identification;

    /**
     * @Column(type="text", nullable=true)
     */
    protected $special;

    /**
     * @Column(type="string", length=45)
     */
    protected $couleur;

    /**
     * @Column(type="datetime")
     */
    protected $date_creation;

    /**
     * @Column(type="datetime")
     */
    protected $date_update;

    /**
     * @ManyToOne(targetEntity="Qualite", inversedBy="items")
     * @JoinColumn(name="qualite_id", referencedColumnName="id", nullable=false)
     */
    protected $qualite;

    /**
     * @ManyToOne(targetEntity="Statut", inversedBy="items")
     * @JoinColumn(name="statut_id", referencedColumnName="id", nullable=false)
     */
    protected $statut;

    public function __construct()
    {
    }

    /**
     * Set the value of id.
     *
     * @param integer $id
     * @return \LarpManager\Entities\Item
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
     * @return \LarpManager\Entities\Item
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
     * @return \LarpManager\Entities\Item
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
     * Set the value of numero.
     *
     * @param integer $numero
     * @return \LarpManager\Entities\Item
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get the value of numero.
     *
     * @return integer
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set the value of identification.
     *
     * @param integer $identification
     * @return \LarpManager\Entities\Item
     */
    public function setIdentification($identification)
    {
        $this->identification = $identification;

        return $this;
    }

    /**
     * Get the value of identification.
     *
     * @return integer
     */
    public function getIdentification()
    {
        return $this->identification;
    }

    /**
     * Set the value of special.
     *
     * @param string $special
     * @return \LarpManager\Entities\Item
     */
    public function setSpecial($special)
    {
        $this->special = $special;

        return $this;
    }

    /**
     * Get the value of special.
     *
     * @return string
     */
    public function getSpecial()
    {
        return $this->special;
    }

    /**
     * Set the value of couleur.
     *
     * @param string $couleur
     * @return \LarpManager\Entities\Item
     */
    public function setCouleur($couleur)
    {
        $this->couleur = $couleur;

        return $this;
    }

    /**
     * Get the value of couleur.
     *
     * @return string
     */
    public function getCouleur()
    {
        return $this->couleur;
    }

    /**
     * Set the value of date_creation.
     *
     * @param \DateTime $date_creation
     * @return \LarpManager\Entities\Item
     */
    public function setDateCreation($date_creation)
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    /**
     * Get the value of date_creation.
     *
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->date_creation;
    }

    /**
     * Set the value of date_update.
     *
     * @param \DateTime $date_update
     * @return \LarpManager\Entities\Item
     */
    public function setDateUpdate($date_update)
    {
        $this->date_update = $date_update;

        return $this;
    }

    /**
     * Get the value of date_update.
     *
     * @return \DateTime
     */
    public function getDateUpdate()
    {
        return $this->date_update;
    }

    /**
     * Set Qualite entity (many to one).
     *
     * @param \LarpManager\Entities\Qualite $qualite
     * @return \LarpManager\Entities\Item
     */
    public function setQualite(Qualite $qualite = null)
    {
        $this->qualite = $qualite;

        return $this;
    }

    /**
     * Get Qualite entity (many to one).
     *
     * @return \LarpManager\Entities\Qualite
     */
    public function getQualite()
    {
        return $this->qualite;
    }

    /**
     * Set Statut entity (many to one).
     *
     * @param \LarpManager\Entities\Statut $statut
     * @return \LarpManager\Entities\Item
     */
    public function setStatut(Statut $statut = null)
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * Get Statut entity (many to one).
     *
     * @return \LarpManager\Entities\Statut
     */
    public function getStatut()
    {
        return $this->statut;
    }

    public function __sleep()
    {
        return array('id', 'label', 'description', 'numero', 'identification', 'qualite_id', 'special', 'couleur', 'date_creation', 'date_update', 'statut_id');
    }
}