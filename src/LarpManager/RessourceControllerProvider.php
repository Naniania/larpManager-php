<?php

namespace LarpManager;

use Silex\Application;
use Silex\ControllerProviderInterface;

/**
 * LarpManager\RessourceControllerProvider
 * 
 * @author kevin
 *
 */
class RessourceControllerProvider implements ControllerProviderInterface
{
	/**
	 * Initialise les routes pour les ressources
	 * Routes :
	 * 	- ressource
	 * 	- ressource.add
	 *  - ressource.update
	 *  - ressource.detail
	 *  
	 * @param Application $app
	 * @return Controllers $controllers
	 */
	public function connect(Application $app)
	{
		$controllers = $app['controllers_factory'];
		
		/**
		 * Liste des ressources
		 */
		$controllers->match('/','LarpManager\Controllers\RessourceController::indexAction')
			->bind("ressource")
			->method('GET');
		
		/**
		 * Ajoute une ressource
		 */
		$controllers->match('/add','LarpManager\Controllers\RessourceController::addAction')
			->bind("ressource.add")
			->method('GET|POST');
		
		/**
		 * Mise à jour d'une ressource
		 */
		$controllers->match('/{index}/update','LarpManager\Controllers\RessourceController::updateAction')
			->assert('index', '\d+')
			->bind("ressource.update")
			->method('GET|POST');
		
		/**
		 * Detail d'une ressource
		 */
		$controllers->match('/{index}','LarpManager\Controllers\RessourceController::detailAction')
			->assert('index', '\d+')
			->bind("ressource.detail")
			->method('GET');
		
		/**
		 * Export d'une ressource au format CSV
		 */
		$controllers->match('/{index}/export','LarpManager\Controllers\RessourceController::detailExportAction')
			->assert('index', '\d+')
			->bind("ressource.detail.export")
			->method('GET');
		
		/**
		 * Export de la liste des ressources au format CSV
		 */
		$controllers->match('/export','LarpManager\Controllers\NiveauController::exportAction')
			->bind("ressource.export")
			->method('GET');
					
		return $controllers;
	}
}