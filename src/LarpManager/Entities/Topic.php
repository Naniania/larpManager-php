<?php

/**
 * Auto generated by MySQL Workbench Schema Exporter.
 * Version 2.1.6-dev (doctrine2-annotation) on 2015-09-01 09:25:47.
 * Goto https://github.com/johmue/mysql-workbench-schema-exporter for more
 * information.
 */

namespace LarpManager\Entities;

use LarpManager\Entities\BaseTopic;

/**
 * LarpManager\Entities\Topic
 *
 * @Entity(repositoryClass="LarpManager\Repository\TopicRepository")
 */
class Topic extends BaseTopic
{
	public function __construct()
	{
		parent::__construct();
		$this->setCreationDate(new \Datetime('NOW'));
		$this->setUpdateDate(new \Datetime('NOW'));
	}
	
	public function __toString()
	{
		return $this->getTitle();
	}
}