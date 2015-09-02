<?php

namespace LarpManager\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Silex\Application;
use LarpManager\Form\GroupeInscriptionForm;
use LarpManager\Form\GnInscriptionForm;

/**
 * LarpManager\Controllers\HomepageController
 *
 * @author kevin
 *
 */
class HomepageController
{
	/**
	 * affiche la page d'acceuil.
	 * 
	 * Si l'utilisateur a enregistrer ses informations de joueur, on lui propose la liste
	 * des GNs auquel il peux s'inscrire.
	 * 
	 * Si l'utilisateur n'a pas enregistré ses informations de joueur, on lui propose la liste
	 * de tous les GNs actifs.
	 * 
	 * @param Request $request
	 * @param Application $app
	 */
	public function indexAction(Request $request, Application $app) 
	{	
		$form = $app['form.factory']->createBuilder(new GroupeInscriptionForm(), array())
			->add('subscribe','submit', array('label' => 'S\'inscrire'))
			->getForm();
		
		$joueur = $app['user']->getJoueur();
		
		$repoGn = $app['orm.em']->getRepository('LarpManager\Entities\Gn');
		
		if ( $joueur )
		{
			$gnNotSubscribes = $repoGn->findByActiveWhereNotSubscribe($joueur);
			$gnSubscribes = $repoGn->findByActiveWhereSubscribe($joueur);
		}

		$gns = $repoGn->findByActive();
				
		return $app['twig']->render('homepage/index.twig', array(
				'form_groupe' => $form->createView(),
				'gns' => $gns,
				'gnSubscribes' => $gnSubscribes,
				'gnNotSubscribes' =>  $gnNotSubscribes,
		));
	}
	
	/**
	 * Affiche une carte du monde
	 * 
	 * @param Request $request
	 * @param Application $app
	 */
	public function worldAction(Request $request, Application $app)
	{	
		return $app['twig']->render('homepage/world.twig');		
	}
	
	/**
	 * Formulaire d'inscription d'un joueur dans un gn
	 * 
	 * @param Request $request
	 * @param Application $app
	 */
	public function inscriptionGnFormAction(Request $request, Application $app)
	{
		$idGn = $request->get('idGn');
		
		$repo = $app['orm.em']->getRepository("LarpManager\Entities\Gn");
		$gn = $repo->findOneById($idGn);
		
		/** Erreur, le gn n'existe pas ou n'est pas actif */
		if ( ! $gn || $gn->getActif() == false )
		{
			throw new LarpManager\Exception\RequestInvalidException();
		}
		
		$form = $app['form.factory']->createBuilder(new GnInscriptionForm(), array('idGn' => $idGn))
			->add('subscribe','submit', array('label' => 'S\'inscrire'))
			->getForm();
		
		return $app['twig']->render('homepage/inscriptionGn.twig', array(
				'form' => $form->createView(),
				'gn' => $gn,
		));
	}
	
	/**
	 * Traitement du formulaire d'inscription d'un joueur dans un gn
	 * 
	 * @param Request $request
	 * @param Application $app
	 * @throws LarpManager\Exception\RequestInvalidException
	 */
	public function inscriptionGnAction(Request $request, Application $app)
	{
		$user = $app['user'];
		
		/** Erreur, l'utilisateur n'est pas encore un joueur */
		if ( ! $user->getJoueur() )
		{
			throw new LarpManager\Exception\RequestInvalidException();
		}
		
		$form = $app['form.factory']->createBuilder(new GnInscriptionForm())
			->add('subscribe','submit', array('label' => 'S\'inscrire'))
			->getForm();
		
		$form->handleRequest($request);
		
		/** Erreur si la requête est invalide */
		if (  ! $form->isValid() )
		{
			throw new LarpManager\Exception\RequestInvalidException();
		}
		
		$data = $form->getData();

		$repo = $app['orm.em']->getRepository("LarpManager\Entities\Gn");
		$gn = $repo->findOneById($data['idGn']);
		
		/** Erreur si le gn n'est pas actif (l'utilisateur à bidouillé la requête */
		if ( ! $gn || ! $gn->getActif() )
		{
			throw new LarpManager\Exception\RequestInvalidException();
		}
		
		/** Enregistre en base de données la nouvelle relation entre le joueur et le gn */
		$joueurGn = new \LarpManager\Entities\JoueurGn();
		$joueurGn->setJoueur($app['user']->getJoueur());
		$joueurGn->setGn($gn);
		$joueurGn->setSubscriptionDate(new \Datetime('NOW'));
		
		$app['orm.em']->persist($joueurGn);
		$app['orm.em']->flush();
		
		/** redirige vers la page principale avec un message de succés */
		$app['session']->getFlashBag()->add('success', 'Vous êtes maintenant inscrit au gn '. $gn->getLabel());
		return $app->redirect($app['url_generator']->generate('homepage'),301);
		
	}
	
	/**
	 * Inscription dans un groupe
	 * 
	 * @param Request $request
	 * @param Application $app
	 */
	public function inscriptionAction(Request $request, Application $app)
	{
		$form = $app['form.factory']->createBuilder(new GroupeInscriptionForm(), array())
			->add('subscribe','submit', array('label' => 'S\'inscrire'))
			->getForm();
		
		$form->handleRequest($request);
		
		if ( $form->isValid() )
		{
			$data = $form->getData();
			
			$repo = $app['orm.em']->getRepository('\LarpManager\Entities\Groupe');
			$groupe = $repo->findOneByCode($data['code']);
			
			if ( $groupe )
			{
				$joueur = $app['user']->getJoueur();
				$joueur->addGroupe($groupe);
												
				$app['orm.em']->persist($joueur);
				$app['orm.em']->flush();
				
				$app['session']->getFlashBag()->add('success', 'Vous êtes maintenant inscrit au groupe.');
				return $app->redirect($app['url_generator']->generate('groupe.joueur',array('index' => $groupe->getId())),301);
			}
			else
			{
				$app['session']->getFlashBag()->add('error', 'Désolé, le code fourni n\'est pas valide. Veuillez vous rapprocher de votre chef de groupe pour le vérifier.');
				return $app->redirect($app['url_generator']->generate('homepage'),301);
			}
		}
	}
}