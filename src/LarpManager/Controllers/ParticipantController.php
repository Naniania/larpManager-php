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
 

namespace LarpManager\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Silex\Application;
use Doctrine\Common\Collections\ArrayCollection;

use LarpManager\Form\JoueurForm;
use LarpManager\Form\FindJoueurForm;
use LarpManager\Form\RestaurationForm;
use LarpManager\Form\JoueurXpForm;
use LarpManager\Form\PersonnageReligionForm;
use LarpManager\Form\ParticipantPersonnageSecondaireForm;
use LarpManager\Form\GroupeInscriptionForm;
use LarpManager\Form\GroupeSecondairePostulerForm;
use LarpManager\Form\PersonnageOriginForm;
use LarpManager\Form\GroupePlaceAvailableForm;
use LarpManager\Form\ParticipantBilletForm;
use LarpManager\Form\ParticipantRestaurationForm;
use LarpManager\Form\ParticipantGroupeForm;


use LarpManager\Entities\Participant;
use LarpManager\Entities\ParticipantHasRestauration;
use LarpManager\Entities\SecondaryGroup;
use LarpManager\Entities\Priere;
use LarpManager\Entities\Potion;
use LarpManager\Entities\Sort;
use LarpManager\Entities\Competence;
use LarpManager\Entities\Groupe;
use LarpManager\Entities\Rule;

/**
 * LarpManager\Controllers\ParticipantController
 *
 * @author kevin
 *
 */
class ParticipantController
{
	/**
	 * Interface Joueur d'un jeu
	 *
	 * @param Application $app
	 * @param Request $request
	 * @param Gn $gn
	 */
	public function indexAction(Application $app, Request $request, Participant $participant)
	{
		return $app['twig']->render('public/participant/index.twig', array(
				'gn' => $participant->getGn(),
				'participant' => $participant,
		));
	}
	
	/**
	 * Affecte un participant à un groupe
	 * 
	 * @param Application $app
	 * @param Request $request
	 * @param Participant $participant
	 */
	public function groupeAction(Application $app, Request $request, Participant $participant)
	{
		$form = $app['form.factory']->createBuilder(new ParticipantGroupeForm(), $participant)
			->add('save','submit', array('label' => 'Sauvegarder'))
			->getForm();
		
		$form->handleRequest($request);
			
		if ( $form->isValid() )
		{
			$participant = $form->getData();
			$app['orm.em']->persist($participant);
			$app['orm.em']->flush();
		
			$app['session']->getFlashBag()->add('success', 'Vos modifications ont été enregistré.');
			return $app->redirect($app['url_generator']->generate('gn.participants', array('gn' => $participant->getGn()->getId())),301);
		}
		
		return $app['twig']->render('admin/participant/groupe.twig', array(
				'participant' => $participant,
				'form' => $form->createView(),
		));
	}
	
	/**
	 * Ajout d'un billet à un utilisateur. L'utilisateur doit participer au même jeu que celui du billet qui lui est affecté
	 *
	 * @param Application $app
	 * @param Request $request
	 * @param User $user
	 */
	public function billetAction(Application $app, Request $request, Participant $participant)
	{
		$form = $app['form.factory']->createBuilder(new ParticipantBilletForm(), $participant)
			->add('save','submit', array('label' => 'Sauvegarder'))
			->getForm();
	
		$form->handleRequest($request);
			
		if ( $form->isValid() )
		{
			$participant = $form->getData();
			$participant->setBilletDate(new \Datetime('NOW'));
			$app['orm.em']->persist($participant);
			$app['orm.em']->flush();
				
			$app['session']->getFlashBag()->add('success', 'Vos modifications ont été enregistré.');
			return $app->redirect($app['url_generator']->generate('gn.participants', array('gn' => $participant->getGn()->getId())),301);
		}
		
		return $app['twig']->render('admin/participant/billet.twig', array(
				'participant' => $participant,
				'form' => $form->createView(),
		));
	}
	
	/**
	 * Choix du lieu de restauration d'un utilisateur
	 *
	 * @param Application $app
	 * @param Request $request
	 * @param User $user
	 */
	public function restaurationAction(Application $app, Request $request, Participant $participant)
	{
		$originalParticipantHasRestaurations = new ArrayCollection();
		
		/**
		 *  Crée un tableau contenant les objets ParticipantHasRestauration du participant
		 */
		foreach ($participant->getParticipantHasRestaurations() as $participantHasRestauration)
		{
			$originalParticipantHasRestaurations->add($participantHasRestauration);
		}
		
		$form = $app['form.factory']->createBuilder(new ParticipantRestaurationForm(),  $participant)
			->add('save','submit', array('label' => 'Sauvegarder'))
			->getForm();
	
		$form->handleRequest($request);
			
		if ( $form->isValid() )
		{
			$participant = $form->getData();
			
			/**
			 * Pour toutes les restaurations du participant
			 */
			foreach ($participant->getParticipantHasRestaurations() as $participantHasRestauration)
			{
				$participantHasRestauration->setParticipant($participant);
			}
			
			/**
			 *  supprime la relation entre participantHasRestauration et le participant
			 */
			foreach ($originalParticipantHasRestaurations as $participantHasRestauration) {
				if ($participant->getParticipantHasRestaurations()->contains($participantHasRestauration) == false) {
					$app['orm.em']->remove($participantHasRestauration);
				}
			}

			$app['orm.em']->persist($participant);
			$app['orm.em']->flush();
	
			$app['session']->getFlashBag()->add('success', 'Vos modifications ont été enregistrés.');
			return $app->redirect($app['url_generator']->generate('gn.participants', array('gn' => $participant->getGn()->getId())),301);
		}
		
		return $app['twig']->render('admin/participant/restauration.twig', array(
				'participant' => $participant,
				'form' => $form->createView(),
		));
	}
	
	/**
	 * Affiche le détail d'un personnage
	 *
	 * @param Request $request
	 * @param Application $app
	 */
	public function personnageAction(Request $request, Application $app, Participant $participant)
	{
		$personnage = $participant->getPersonnage();
		
		if ( ! $personnage )
		{
			$app['session']->getFlashBag()->add('error','Vous n\'avez pas encore de personnage.');
			return $app->redirect($app['url_generator']->generate('participant.index', array('participant' => $participant->getId())),301);
		}
		
		return $app['twig']->render('public/personnage/detail.twig', array(
				'personnage' => $personnage,
				'participant' => $participant,
		));
	}
	
	/**
	 * Page listant les règles à télécharger
	 *
	 * @param Request $request
	 * @param Application $app
	 */
	public function regleListAction(Request $request, Application $app, Participant $participant)
	{
		$regles = $app['orm.em']->getRepository('LarpManager\Entities\Rule')->findAll();
	
		return $app['twig']->render('public/rule/list.twig', array(
				'regles' => $regles,
				'participant' => $participant,
		));
	}
	
	/**
	 * Détail d'une règle
	 * 
	 * @param Request $request
	 * @param Application $app
	 * @param Participant $participant
	 * @param Rule $regle
	 */
	public function regleDetailAction(Request $request, Application $app, Participant $participant, Rule $rule)
	{
		return $app['twig']->render('public/rule/detail.twig', array(
				'regle' => $rule,
				'participant' => $participant,
		));
	}	
	
	/**
	 * Télécharger une règle
	 *
	 * @param Request $request
	 * @param Application $app
	 * @param Participant $participant
	 * @param Rule rule
	 */
	public function regleDocumentAction(Request $request, Application $app, Participant $participant, Rule $rule)
	{
		$filename = __DIR__.'/../../../private/rules/'.$rule->getUrl();
		return $app->sendFile($filename);
	}
	
	/**
	 * Liste des groupes
	 * 
	 * @param Request $request
	 * @param Application $app
	 * @param Participant $participant
	 */
	public function groupeListAction(Request $request, Application $app, Participant $participant)
	{
		$groupes = $app['orm.em']->getRepository('LarpManager\Entities\Groupe')->findAllPj();
	
		return $app['twig']->render('public/groupe/list.twig', array(
				'groupes' => $groupes,
				'participant' => $participant,
		));
	}
	
	/**
	 * Rejoindre un groupe
	 *
	 * @param Request $request
	 * @param Application $app
	 */
	public function groupeJoinAction(Request $request, Application $app, Participant $participant)
	{
		if ( ! $participant->getBillet() )
		{
			$app['session']->getFlashBag()->add('error','Désolé, vous devez obtenir un billet avant de pouvoir rejoindre un groupe');
			return $app->redirect($app['url_generator']->generate('participant.index', array('participant' => $participant->getId())),301);
		}
		$form = $app['form.factory']->createBuilder(new GroupeInscriptionForm(), array())
			->add('subscribe','submit', array('label' => 'S\'inscrire'))
			->getForm();
	
		$form->handleRequest($request);
				
		if ( $form->isValid() )
		{
				
		}
		
		return $app['twig']->render('public/groupe/join.twig', array(
				'form' => $form->createView(),
				'participant' => $participant,
		));
	}
	
	/**
	 * Détail d'un groupe
	 * 
	 * @param Request $request
	 * @param Application $app
	 * @param Participant $participant
	 * @param Groupe $groupe
	 */
	public function groupeDetailAction(Request $request, Application $app, Participant $participant, Groupe $groupe)
	{
		return $app['twig']->render('public/groupe/detail.twig', array(
				'groupe' => $groupe,
				'participant' => $participant,
		));
	}
	
	/**
	 * Permet au chef de groupe de modifier le nombre de place disponible
	 *
	 * @param Request $request
	 * @param Application $app
	 * @param Groupe $groupe
	 */
	public function groupePlaceAvailableAction(Request $request, Application $app, Participant $participant,  Groupe $groupe)
	{
		if ( ! $participant->getBillet() )
		{
			$app['session']->getFlashBag()->add('error','Désolé, vous devez obtenir un billet avant.');
			return $app->redirect($app['url_generator']->generate('participant.index', array('participant' => $participant->getId())),301);
		}
		
		if ( $participant->getUser() != $groupe->getResponsable() )
		{
			$app['session']->getFlashBag()->add('error','Désolé, cette action est réservé au chef de groupe.');
			return $app->redirect($app['url_generator']->generate('participant.index', array('participant' => $participant->getId())),301);
		}
		
		$form = $app['form.factory']->createBuilder(new GroupePlaceAvailableForm(), $groupe)
			->add('submit','submit', array('label' => 'Enregistrer'))
			->getForm();
	
		$form->handleRequest($request);
			
		if ( $form->isValid() )
		{
			$groupe = $form->getData();
			$app['orm.em']->persist($groupe);
			$app['orm.em']->flush();
	
			$app['session']->getFlashBag()->add('success', 'Vos modifications ont été enregistré.');
			return $app->redirect($app['url_generator']->generate('participant.groupe.detail', array('participant' => $participant->getId())));
		}
	
		return $app['twig']->render('public/groupe/placeAvailable', array(
				'form' => $form->createView(),
				'groupe' => $groupe,
				'participant' => $participant,
		));
	}
	
	/**
	 * Choix du personnage secondaire par un utilisateur
	 *
	 * @param Request $request
	 * @param Application $app
	 */
	public function personnageSecondaireAction(Request $request, Application $app, Participant $participant)
	{	
		$repo = $app['orm.em']->getRepository('\LarpManager\Entities\PersonnageSecondaire');
		$personnageSecondaires = $repo->findAll();
	
		$form = $app['form.factory']->createBuilder(new ParticipantPersonnageSecondaireForm(), $participant)
			->add('choice','submit', array('label' => 'Enregistrer'))
			->getForm();
			
		$form->handleRequest($request);
			
		if ( $form->isValid() )
		{
			$participant = $form->getData();
			$app['orm.em']->persist($participant);
			$app['orm.em']->flush();
	
			$app['session']->getFlashBag()->add('success','Le personnage secondaire a été enregistré.');
						
			return $app->redirect($app['url_generator']->generate('participant.index', array('participant' => $participant->getId())),301);
		}
	
		return $app['twig']->render('public/participant/personnageSecondaire.twig', array(
				'participant' => $participant,
				'personnageSecondaires' => $personnageSecondaires,
				'form' => $form->createView(),
		));
			
	}
	
	/**
	 * Liste des background pour le joueur
	 *
	 * @param Request $request
	 * @param Application $app
	 */
	public function backgroundAction(Request $request, Application $app, Participant $participant)
	{
		// l'utilisateur doit avoir un personnage
		$personnage = $participant->getPersonnage();
		if ( ! $personnage )
		{
			$app['session']->getFlashBag()->add('error','Désolé, Vous devez faire votre personnage pour pouvoir consulter votre background.');
			return $app->redirect($app['url_generator']->generate('participant.index', array('participant' => $participant->getId())),301);
		}
	
		$backsGroupe = new ArrayCollection();
		$backsJoueur = new ArrayCollection();
	
		// recherche les backgrounds liés au personnage (visibilité == OWNER)
		$backsJoueur = $personnage->getBackgrounds('OWNER');
	
		// recherche les backgrounds liés au groupe (visibilité == PUBLIC)
		$backsGroupe = new ArrayCollection(array_merge(
			$personnage->getGroupe()->getBacks('PUBLIC')->toArray(),
			$backsGroupe->toArray()
		));
	
		// recherche les backgrounds liés au groupe (visibilité == GROUP_MEMBER)
		$backsGroupe = new ArrayCollection(array_merge(
			$personnage->getGroupe()->getBacks('GROUPE_MEMBER')->toArray(),
			$backsGroupe->toArray()
		));
	
		// recherche les backgrounds liés au groupe (visibilité == GROUP_OWNER)
		if ( $app['user'] == $personnage->getGroupe()->getUserRelatedByResponsableId() )
		{
			$backsGroupe = new ArrayCollection(array_merge(
					$personnage->getGroupe()->getBacks('GROUPE_OWNER')->toArray(),
					$backsGroupe->toArray()
					));
		}
	
		return $app['twig']->render('public/participant/background.twig', array(
				'backsJoueur' => $backsJoueur,
				'backsGroupe' => $backsGroupe,
				'personnage' => $personnage,
				'participant' => $participant,
		));
	}
	
	/**
	 * Mise à jour de l'origine d'un personnage.
	 * Impossible si le personnage dispose déjà d'une origine.
	 *
	 * @param Request $request
	 * @param Application $app
	 */
	public function origineAction(Request $request, Application $app, Participant $participant)
	{
		$personnage = $participant->getPersonnage();
	
		if ( ! $personnage )
		{
			$app['session']->getFlashBag()->add('error','Désolé, vous devez créer un personnage avant de choisir son origine.');
			return $app->redirect($app['url_generator']->generate('participant.index', array('participant' => $participant->getId())),301);
		}
		
		if ( $personnage->getGroupe()->getLock() == true)
		{
			$app['session']->getFlashBag()->add('error','Désolé, il n\'est plus possible de modifier ce personnage. Le groupe est verouillé. Contacter votre scénariste si vous pensez que cela est une erreur');
			return $app->redirect($app['url_generator']->generate('participant.personnage', array('participant' => $participant->getId())),301);
		}
	
		if ( $personnage->getTerritoire() )
		{
			$app['session']->getFlashBag()->add('error','Désolé, il n\'est pas possible de modifier votre origine. Veuillez contacter votre orga pour exposer votre problème.');
			return $app->redirect($app['url_generator']->generate('participant.personnage', array('participant' => $participant->getId())),301);
		}
	
		$form = $app['form.factory']->createBuilder(new PersonnageOriginForm(), $personnage)
			->add('save','submit', array('label' => 'Valider votre origine'))
			->getForm();
	
		$form->handleRequest($request);
			
		if ( $form->isValid() )
		{
			$personnage = $form->getData();
			$app['orm.em']->persist($personnage);
			$app['orm.em']->flush();
	
			$app['session']->getFlashBag()->add('success','Votre personnage a été sauvegardé.');
			return $app->redirect($app['url_generator']->generate('participant.personnage', array('participant' => $participant->getId())),301);
		}
	
		return $app['twig']->render('public/participant/origine.twig', array(
				'form' => $form->createView(),
				'personnage' => $personnage,
				'participant' => $participant,
		));
	}

	/**
	 * Liste des religions
	 *
	 * @param Request $request
	 * @param Application $app
	 */
	public function religionListAction(Request $request, Application $app, Participant $participant)
	{
		$repo = $app['orm.em']->getRepository('\LarpManager\Entities\Religion');
		$religions = $repo->findAllOrderedByLabel();
	
		return $app['twig']->render('public/participant/religion.twig', array(
				'religions' => $religions,
				'participant' => $participant,
		));
	}

	/**
	 * Ajoute une religion au personnage
	 *
	 * @param Request $request
	 * @param Application $app
	 */
	public function religionAddAction(Request $request, Application $app, Participant $participant)
	{
		$personnage = $participant->getPersonnage();
		
		if ( ! $personnage )
		{
			$app['session']->getFlashBag()->add('error', 'Vous devez avoir créer un personnage avant de choisir une religion !');
			return $app->redirect($app['url_generator']->generate('participant.index', array('participant' => $participant->getId())),301);
		}
		
	
		if ( $personnage->getGroupe()->getLock() == true)
		{
			$app['session']->getFlashBag()->add('error','Désolé, il n\'est plus possible de modifier ce personnage. Le groupe est verouillé. Contacter votre scénariste si vous pensez que cela est une erreur');
			return $app->redirect($app['url_generator']->generate('participant.personnage', array('participant' => $participant->getId())),301);
		}
	
	
		// refuser la demande si le personnage est Fanatique
		if ( $personnage->isFanatique() )
		{
			$app['session']->getFlashBag()->add('error','Désolé, vous êtes un Fanatique, il vous est impossible de choisir une nouvelle religion. Veuillez contacter votre orga en cas de problème.');
			return $app->redirect($app['url_generator']->generate('participant.personnage', array('participant' => $participant->getId())),301);
		}
	
		$personnageReligion = new \LarpManager\Entities\PersonnagesReligions();
		$personnageReligion->setPersonnage($personnage);
	
		// ne proposer que les religions que le personnage ne pratique pas déjà ...
		$availableReligions = $app['personnage.manager']->getAvailableReligions($personnage);
	
		if ( $availableReligions->count() == 0 )
		{
			$app['session']->getFlashBag()->add('error','Désolé, il n\'y a plus de religion disponibles ( Sérieusement ? vous êtes éclectique, c\'est bien, mais ... faudrait savoir ce que vous voulez non ? L\'heure n\'est-il pas venu de faire un choix parmi tous ces dieux ?)');
			return $app->redirect($app['url_generator']->generate('participant.personnage', array('participant' => $participant->getId())),301);
		}
	
		// construit le tableau de choix
		$choices = array();
		foreach ( $availableReligions as $religion)
		{
			$choices[] = $religion;
		}
	
		$form = $app['form.factory']->createBuilder(new PersonnageReligionForm(), $personnageReligion)
			->add('religion','entity', array(
					'required' => true,
					'label' => 'Votre religion',
					'class' => 'LarpManager\Entities\Religion',
					'choices' => $availableReligions,
					'property' => 'label',
			))
			->add('save','submit', array('label' => 'Valider votre religion'))
			->getForm();
	
		$form->handleRequest($request);
	
		if ( $form->isValid() )
		{
			$personnageReligion = $form->getData();
				
			// supprimer toutes les autres religions si l'utilisateur à choisi fanatique
			// n'autoriser que un Fervent que si l'utilisateur n'a pas encore Fervent.
			if ( $personnageReligion->getReligionLevel()->getIndex() == 3 )
			{
				$personnagesReligions = $personnage->getPersonnagesReligions();
				foreach ( $personnagesReligions as $oldReligion)
				{
					$app['orm.em']->remove($oldReligion);
				}
			}
			else if ( $personnageReligion->getReligionLevel()->getIndex() == 2 )
			{
				if ( $personnage->isFervent() )
				{
					$app['session']->getFlashBag()->add('error','Désolé, vous êtes déjà Fervent d\'une autre religion, il vous est impossible de choisir une nouvelle religion en tant que Fervent. Veuillez contacter votre orga en cas de problème.');
					return $app->redirect($app['url_generator']->generate('participant.personnage', array('participant' => $participant->getId())),301);
				}
			}
	
			$app['orm.em']->persist($personnageReligion);
			$app['orm.em']->flush();
	
			$app['session']->getFlashBag()->add('success','Votre personnage a été sauvegardé.');
			return $app->redirect($app['url_generator']->generate('participant.personnage', array('participant' => $participant->getId())),301);
		}
	
		return $app['twig']->render('public/personnage/religion_add.twig', array(
				'form' => $form->createView(),
				'personnage' => $personnage,
				'participant' => $participant,
				'religions' => $availableReligions,
		));
	}
	
	/**
	 * Detail d'une priere
	 * 
	 * @param Request $request
	 * @param Application $app
	 * @param Participant $participant
	 * @param Priere $priere
	 */
	public function priereDetailAction(Request $request, Application $app, Participant $participant, Priere $priere)
	{
		$personnage = $participant->getPersonnage();
		
		if ( ! $personnage )
		{
			$app['session']->getFlashBag()->add('error', 'Vous devez avoir créer un personnage !');
			return $app->redirect($app['url_generator']->generate('participant.index', array('participant' => $participant->getId())),301);
		}
		
		if ( ! $personnage->isKnownPriere($priere) )
		{
			$app['session']->getFlashBag()->add('error', 'Vous ne connaissez pas cette prière !');
			return $app->redirect($app['url_generator']->generate('participant.personnage', array('participant' => $participant->getId())),301);
		}
		
		return $app['twig']->render('public/priere/detail.twig', array(
				'priere' => $priere,
				'participant' => $participant,
				
		));
	}
	
	/**
	 * Obtenir le document lié à une priere
	 * 
	 * @param Request $request
	 * @param Application $app
	 * @param Participant $participant
	 * @param Priere $priere
	 */
	public function priereDocumentAction(Request $request, Application $app, Participant $participant, Priere $priere)
	{
		$personnage = $participant->getPersonnage();
		
		if ( ! $personnage )
		{
			$app['session']->getFlashBag()->add('error', 'Vous devez avoir créer un personnage !');
			return $app->redirect($app['url_generator']->generate('participant.index', array('participant' => $participant->getId())),301);
		}
		
		if ( ! $personnage->isKnownPriere($priere) )
		{
			$app['session']->getFlashBag()->add('error', 'Vous ne connaissez pas cette prière !');
			return $app->redirect($app['url_generator']->generate('participant.personnage', array('participant' => $participant->getId())),301);
		}
			
		$file = __DIR__.'/../../../private/doc/'.$priere->getDocumentUrl();
		return $app->sendFile($file);
	}
	
	
	/**
	 * Detail d'une potion
	 *
	 * @param Request $request
	 * @param Application $app
	 * @param Participant $participant
	 * @param Potion $potion
	 */
	public function potionDetailAction(Request $request, Application $app, Participant $participant, Potion $potion)
	{
		$personnage = $participant->getPersonnage();
	
		if ( ! $personnage )
		{
			$app['session']->getFlashBag()->add('error', 'Vous devez avoir créer un personnage !');
			return $app->redirect($app['url_generator']->generate('participant.index', array('participant' => $participant->getId())),301);
		}
	
		if ( ! $personnage->isKnownPotion($potion) )
		{
			$app['session']->getFlashBag()->add('error', 'Vous ne connaissez pas cette potion !');
			return $app->redirect($app['url_generator']->generate('participant.personnage', array('participant' => $participant->getId())),301);
		}
	
		return $app['twig']->render('public/potion/detail.twig', array(
				'potion' => $potion,
				'participant' => $participant,
	
		));
	}
	
	/**
	 * Obtenir le document lié à une potion
	 *
	 * @param Request $request
	 * @param Application $app
	 * @param Participant $participant
	 * @param Potion $potion
	 */
	public function potionDocumentAction(Request $request, Application $app, Participant $participant, Potion $potion)
	{
		$personnage = $participant->getPersonnage();
	
		if ( ! $personnage )
		{
			$app['session']->getFlashBag()->add('error', 'Vous devez avoir créer un personnage !');
			return $app->redirect($app['url_generator']->generate('participant.index', array('participant' => $participant->getId())),301);
		}
	
		if ( ! $personnage->isKnownPotion($potion) )
		{
			$app['session']->getFlashBag()->add('error', 'Vous ne connaissez pas cette potion !');
			return $app->redirect($app['url_generator']->generate('participant.personnage', array('participant' => $participant->getId())),301);
		}
			
		$file = __DIR__.'/../../../private/doc/'.$potion->getDocumentUrl();
		return $app->sendFile($file);
	}

	/**
	 * Detail d'un sort
	 *
	 * @param Request $request
	 * @param Application $app
	 * @param Participant $participant
	 * @param Sort $potion
	 */
	public function sortDetailAction(Request $request, Application $app, Participant $participant, Sort $sort)
	{
		$personnage = $participant->getPersonnage();
	
		if ( ! $personnage )
		{
			$app['session']->getFlashBag()->add('error', 'Vous devez avoir créer un personnage !');
			return $app->redirect($app['url_generator']->generate('participant.index', array('participant' => $participant->getId())),301);
		}
	
		if ( ! $personnage->isKnownSort($sort) )
		{
			$app['session']->getFlashBag()->add('error', 'Vous ne connaissez pas ce sort !');
			return $app->redirect($app['url_generator']->generate('participant.personnage', array('participant' => $participant->getId())),301);
		}
	
		return $app['twig']->render('public/sort/detail.twig', array(
				'sort' => $sort,
				'participant' => $participant,
	
		));
	}
	
	/**
	 * Obtenir le document lié à un sort
	 *
	 * @param Request $request
	 * @param Application $app
	 * @param Participant $participant
	 * @param Potion $potion
	 */
	public function sortDocumentAction(Request $request, Application $app, Participant $participant, Sort $sort)
	{
		$personnage = $participant->getPersonnage();
	
		if ( ! $personnage )
		{
			$app['session']->getFlashBag()->add('error', 'Vous devez avoir créer un personnage !');
			return $app->redirect($app['url_generator']->generate('participant.index', array('participant' => $participant->getId())),301);
		}
	
		if ( ! $personnage->isKnownSort($sort) )
		{
			$app['session']->getFlashBag()->add('error', 'Vous ne connaissez pas ce sort !');
			return $app->redirect($app['url_generator']->generate('participant.personnage', array('participant' => $participant->getId())),301);
		}
			
		$file = __DIR__.'/../../../private/doc/'.$sort->getDocumentUrl();
		return $app->sendFile($file);
	}
	
	/**
	 * Découverte de la magie, des domaines et sortilèges
	 *
	 * @param Request $request
	 * @param Application $app
	 * @param Participant $participant
	 */
	public function magieAction(Request $request, Application $app, Participant $participant)
	{
		$personnage = $participant->getPersonnage();

		if ( ! $personnage )
		{
			$app['session']->getFlashBag()->add('error', 'Vous devez avoir créer un personnage !');
			return $app->redirect($app['url_generator']->generate('participant.index', array('participant' => $participant->getId())),301);
		}
		
		$domaines = $app['orm.em']->getRepository('\LarpManager\Entities\Domaine')->findAll();
		
		return $app['twig']->render('public/magie/index.twig', array(
				'domaines' => $domaines,
				'personnage' => $personnage,
				'participant' => $participant,
		));
	}
	
	/**
	 * Liste des compétences pour les joueurs
	 *
	 * @param Request $request
	 * @param Application $app
	 */
	public function competenceListAction(Request $request, Application $app, Participant $participant)
	{
		$competences = $app['larp.manager']->getRootCompetences();
		
		return $app['twig']->render('public/competence/list.twig', array(
				'competences' => $competences,
				'participant' => $participant,
		));
	}
	
	/**
	 * Detail d'une competence
	 *
	 * @param Request $request
	 * @param Application $app
	 * @param Participant $participant
	 * @param Competence $competence
	 */
	public function competenceDetailAction(Request $request, Application $app, Participant $participant, Competence $competence)
	{
		$personnage = $participant->getPersonnage();
	
		if ( ! $personnage )
		{
			$app['session']->getFlashBag()->add('error', 'Vous devez avoir créer un personnage !');
			return $app->redirect($app['url_generator']->generate('participant.index', array('participant' => $participant->getId())),301);
		}
	
		if ( ! $personnage->isKnownCompetence($competence) )
		{
			$app['session']->getFlashBag()->add('error', 'Vous ne connaissez pas cette competence !');
			return $app->redirect($app['url_generator']->generate('participant.personnage', array('participant' => $participant->getId())),301);
		}
	
		return $app['twig']->render('public/competence/detail.twig', array(
				'competence' => $competence,
				'participant' => $participant,
	
		));
	}


	/**
	 * Liste des classes pour le joueur
	 *
	 * @param Request $request
	 * @param Application $app
	 */
	public function classeListAction(Request $request, Application $app, Participant $participant)
	{
		$repo = $app['orm.em']->getRepository('\LarpManager\Entities\Classe');
		$classes = $repo->findAllOrderedByLabel();
		return $app['twig']->render('public/classe/list.twig', array(
				'classes' => $classes,
				'participant' => $participant
		));
	}
	
	/**
	 * Obtenir le document lié à une competence
	 *
	 * @param Request $request
	 * @param Application $app
	 * @param Participant $participant
	 * @param Competence $competence
	 */
	public function competenceDocumentAction(Request $request, Application $app, Participant $participant, Competence $competence)
	{
		$personnage = $participant->getPersonnage();
	
		if ( ! $personnage )
		{
			$app['session']->getFlashBag()->add('error', 'Vous devez avoir créer un personnage !');
			return $app->redirect($app['url_generator']->generate('participant.index', array('participant' => $participant->getId())),301);
		}
	
		if ( ! $personnage->isKnownCompetence($competence) )
		{
			$app['session']->getFlashBag()->add('error', 'Vous ne connaissez pas cette competence !');
			return $app->redirect($app['url_generator']->generate('participant.personnage', array('participant' => $participant->getId())),301);
		}
			
		$file = __DIR__.'/../../../private/doc/'.$competence->getDocumentUrl();
		return $app->sendFile($file);
	}
	
	/**
	 * Liste des groupes secondaires public (pour les joueurs)
	 *
	 * @param Request $request
	 * @param Application $app
	 */
	public function groupeSecondaireListAction(Request $request, Application $app, Participant $participant)
	{
		$repo = $app['orm.em']->getRepository('\LarpManager\Entities\SecondaryGroup');
		$groupeSecondaires = $repo->findAll();
	
		return $app['twig']->render('public/groupeSecondaire/list.twig', array(
				'groupeSecondaires' => $groupeSecondaires,
				'participant' => $participant,
		));
	}
	
	/**
	 * Postuler à un groupe secondaire
	 *
	 * @param Request $request
	 * @param Application $app
	 */
	public function groupeSecondairePostulerAction(Request $request, Application $app, Participant $participant, SecondaryGroup $groupeSecondaire)
	{	
		/**
		 * L'utilisateur doit avoir un personnage
		 * @var Personnage $personnage
		 */
		$personnage = $participant->getPersonnage();
	
		if ( ! $personnage )
		{
			$app['session']->getFlashBag()->add('error', 'Vous devez avoir créer un personnage avant de postuler à un groupe secondaire!');
			return $app->redirect($app['url_generator']->generate('participant.index', array('participant' => $participant->getId())),301);
		}
	
		/**
		 * Si le joueur est déjà postulant dans ce groupe, refuser la demande
		 */
		if ( $groupeSecondaire->isPostulant($personnage) )
		{
			$app['session']->getFlashBag()->add('error', 'Votre avez déjà postulé dans ce groupe. Inutile d\'en refaire la demande.');
			return $app->redirect($app['url_generator']->generate('participant.index', array('participant' => $participant->getId())),301);
		}
	
		/**
		 * Si le joueur est déjà membre de ce groupe, refuser la demande
		 */
		if ( $groupeSecondaire->isMembre($personnage) )
		{
			$app['session']->getFlashBag()->add('error', 'Votre êtes déjà membre de ce groupe. Inutile d\'en refaire la demande.');
			return $app->redirect($app['url_generator']->generate('participant.index', array('participant' => $participant->getId())),301);
		}
	
		/**
		 * Création du formulaire
		 * @var unknown $form
		 */
		$form = $app['form.factory']->createBuilder(new GroupeSecondairePostulerForm())
			->add('postuler','submit', array('label' => "Postuler"))
			->getForm();
	
		$form->handleRequest($request);
	
		if ( $form->isValid() )
		{
			$data = $form->getData();
				
			$postulant = new \LarpManager\Entities\Postulant();
			$postulant->setPersonnage($personnage);
			$postulant->setSecondaryGroup($groupeSecondaire);
			$postulant->setExplanation($data['explanation']);
			$postulant->setWaiting(false);
	
			$app['orm.em']->persist($postulant);
			$app['orm.em']->flush();
				
				
			// envoi d'un mail au chef du groupe secondaire
			if ( $groupeSecondaire->getResponsable() )
			{
				$message = "Nouvelle candidature";
				$message = \Swift_Message::newInstance()
				->setSubject('[LarpManager] Nouvelle candidature')
				->setFrom(array('noreply@eveoniris.com'))
				->setTo(array($groupeSecondaire->getResponsable()->getParticipant()->getUser()->getEmail()))
				->setBody($message);
					
				$app['mailer']->send($message);
			}
				
			$app['session']->getFlashBag()->add('success', 'Votre candidature a été enregistrée, et transmise au chef de groupe.');
	
			return $app->redirect($app['url_generator']->generate('participant.index', array('participant' => $participant->getId())),301);
		}
	
		return $app['twig']->render('public/groupeSecondaire/postuler.twig', array(
				'groupeSecondaire' => $groupeSecondaire,
				'participant' => $participant,
				'form' => $form->createView(),
		));
	}

	/**
	 * Affichage à destination d'un membre du groupe secondaire
	 * 
	 * @param Request $request
	 * @param Application $app
	 */
	public function groupeSecondaireDetailAction(Request $request, Application $app, Participant $participant, SecondaryGroup $groupeSecondaire)
	{
		$personnage = $participant->getPersonnage();
		$membre = $personnage->getMembre($groupeSecondaire);
		
		if ( ! $personnage )
		{
			$app['session']->getFlashBag()->add('error', 'Vous devez avoir créer un personnage !');
			return $app->redirect($app['url_generator']->generate('participant.index', array('participant' => $participant->getId())),301);
		}
		
		if ( ! $membre )
		{
			$app['session']->getFlashBag()->add('error', 'Votre n\'êtes pas membre de ce groupe.');
			return $app->redirect($app['url_generator']->generate('participant.personnage', array('participant' => $participant->getId())),301);
		}
	
		return $app['twig']->render('public/groupeSecondaire/detail.twig', array(
				'groupeSecondaire' => $groupeSecondaire,
				'membre' => $membre,
				'participant' => $participant,
		));
	}
	
	
	/**
	 * Ajoute une compétence au personnage
	 *
	 * @param Request $request
	 * @param Application $app
	 */
	public function competenceAddAction(Request $request, Application $app, Participant $participant)
	{
		$personnage = $participant->getPersonnage();
	
		if ( ! $personnage )
		{
			$app['session']->getFlashBag()->add('error', 'Vous devez avoir créer un personnage !');
			return $app->redirect($app['url_generator']->generate('participant.index', array('participant' => $participant->getId())),301);
		}
		
		if ( $personnage->getGroupe()->getLock() == true)
		{
			$app['session']->getFlashBag()->add('error','Désolé, il n\'est plus possible de modifier ce personnage. Le groupe est verouillé. Contacter votre scénariste si vous pensez que cela est une erreur');
			return $app->redirect($app['url_generator']->generate('participant.personnage', array('participant' => $participant->getId())),301);
		}
	
		$availableCompetences = $app['personnage.manager']->getAvailableCompetences($personnage);
	
		if ( $availableCompetences->count() == 0 )
		{
			$app['session']->getFlashBag()->add('error','Désolé, il n\'y a plus de compétence disponible (Bravo !).');
			return $app->redirect($app['url_generator']->generate('participant.personnage', array('participant' => $participant->getId())),301);
		}
	
		// construit le tableau de choix
		$choices = array();
		foreach ( $availableCompetences as $competence)
		{
			$choices[$competence->getId()] = $competence->getLabel() . ' (cout : '.$app['personnage.manager']->getCompetenceCout($personnage, $competence).' xp)';
		}
	
		$form = $app['form.factory']->createBuilder()
			->add('competenceId','choice', array(
					'label' =>  'Choisissez une nouvelle compétence',
					'choices' => $choices,
			))
			->add('save','submit', array('label' => 'Valider la compétence'))
			->getForm();
	
		$form->handleRequest($request);
			
		if ( $form->isValid() )
		{
			$data = $form->getData();
				
			$competenceId = $data['competenceId'];
			$competence = $app['orm.em']->find('\LarpManager\Entities\Competence', $competenceId);
	
			$cout = $app['personnage.manager']->getCompetenceCout($personnage, $competence);
			$xp = $personnage->getXp();
				
			if ( $xp - $cout < 0 )
			{
				$app['session']->getFlashBag()->add('error','Vos n\'avez pas suffisement de point d\'expérience pour acquérir cette compétence.');
				return $app->redirect($app['url_generator']->generate('homepage'),301);
			}
			$personnage->setXp($xp - $cout);
			$personnage->addCompetence($competence);
			$competence->addPersonnage($personnage);
				
			// cas special noblesse
			// noblesse apprentit +2 renomme
			// noblesse initie  +3 renomme
			// noblesse expert +2 renomme
			// TODO : trouver un moyen pour ne pas implémenter les règles spéciales de ce type dans le code.
			if ( $competence->getCompetenceFamily()->getLabel() == "Noblesse")
			{
				switch ($competence->getLevel()->getId())
				{
					case 1:
						$personnage->addRenomme(2);
						break;
					case 2:
						$personnage->addRenomme(3);
						break;
					case 3:
						$personnage->addRenomme(2);
						break;
				}
			}
				
			// cas special prêtrise
			if ( $competence->getCompetenceFamily()->getLabel() == "Prêtrise")
			{
				// le personnage doit avoir une religion au niveau fervent ou fanatique
				if ( $personnage->isFervent() || $personnage->isFanatique() )
				{
					// ajoute toutes les prières de niveau de sa compétence liés aux sphère de sa religion fervente ou fanatique
					$religion = $personnage->getMainReligion();
					foreach ( $religion->getSpheres() as $sphere)
					{
						foreach ( $sphere->getPrieres() as $priere)
						{
							if ( $priere->getNiveau() == $competence->getLevel()->getId() )
							{
								$priere->addPersonnage($personnage);
								$personnage->addPriere($priere);
							}
						}
					}
				}
				else
				{
					$app['session']->getFlashBag()->add('error','Pour obtenir la compétence Prêtrise, vous devez être FERVENT ou FANATIQUE');
					return $app->redirect($app['url_generator']->generate('participant.personnage', array('participant' => $participant->getId())),301);
				}
			}
				
				
			// cas special alchimie
			if ( $competence->getCompetenceFamily()->getLabel() == "Alchimie")
			{
				switch ($competence->getLevel()->getId())
				{
					case 1: // le personnage doit choisir 2 potions de niveau apprenti
						$trigger = new \LarpManager\Entities\PersonnageTrigger();
						$trigger->setPersonnage($personnage);
						$trigger->setTag('ALCHIMIE APPRENTI');
						$trigger->setDone(false);
						$app['orm.em']->persist($trigger);
						$app['orm.em']->flush();
	
						$trigger = new \LarpManager\Entities\PersonnageTrigger();
						$trigger->setPersonnage($personnage);
						$trigger->setTag('ALCHIMIE APPRENTI');
						$trigger->setDone(false);
						$app['orm.em']->persist($trigger);
						$app['orm.em']->flush();
						break;
					case 2: // le personnage doit choisir 1 potion de niveau initie et 1 potion de niveau apprenti
						$trigger = new \LarpManager\Entities\PersonnageTrigger();
						$trigger->setPersonnage($personnage);
						$trigger->setTag('ALCHIMIE INITIE');
						$trigger->setDone(false);
						$app['orm.em']->persist($trigger);
						$app['orm.em']->flush();
	
						$trigger = new \LarpManager\Entities\PersonnageTrigger();
						$trigger->setPersonnage($personnage);
						$trigger->setTag('ALCHIMIE APPRENTI');
						$trigger->setDone(false);
						$app['orm.em']->persist($trigger);
						$app['orm.em']->flush();
						break;
					case 3: // le personnage doit choisir 1 potion de niveau expert
						$trigger = new \LarpManager\Entities\PersonnageTrigger();
						$trigger->setPersonnage($personnage);
						$trigger->setTag('ALCHIMIE EXPERT');
						$trigger->setDone(false);
						$app['orm.em']->persist($trigger);
						$app['orm.em']->flush();
						break;
					case 4: // le personnage doit choisir 1 potion de niveau maitre
						$trigger = new \LarpManager\Entities\PersonnageTrigger();
						$trigger->setPersonnage($personnage);
						$trigger->setTag('ALCHIMIE MAITRE');
						$trigger->setDone(false);
						$app['orm.em']->persist($trigger);
						$app['orm.em']->flush();
						break;
				}
			}
				
			// cas special magie
			if ( $competence->getCompetenceFamily()->getLabel() == "Magie")
			{
				switch ($competence->getLevel()->getId())
				{
					case 1: // le personnage doit choisir un domaine de magie
						$trigger = new \LarpManager\Entities\PersonnageTrigger();
						$trigger->setPersonnage($personnage);
						$trigger->setTag('MAGIE APPRENTI');
						$trigger->setDone(false);
						$app['orm.em']->persist($trigger);
						$app['orm.em']->flush();
	
						// il obtient aussi la possibilité de choisir un sort de niveau 1
						$trigger = new \LarpManager\Entities\PersonnageTrigger();
						$trigger->setPersonnage($personnage);
						$trigger->setTag('MAGIE APPRENTI SORT');
						$trigger->setDone(false);
						$app['orm.em']->persist($trigger);
						$app['orm.em']->flush();
						break;
					case 2:
						// il obtient aussi la possibilité de choisir un sort de niveau 2
						$trigger = new \LarpManager\Entities\PersonnageTrigger();
						$trigger->setPersonnage($personnage);
						$trigger->setTag('MAGIE INITIE SORT');
						$trigger->setDone(false);
						$app['orm.em']->persist($trigger);
						$app['orm.em']->flush();
						break;
					case 3: // le personnage peut choisir un nouveau domaine de magie
						$trigger = new \LarpManager\Entities\PersonnageTrigger();
						$trigger->setPersonnage($personnage);
						$trigger->setTag('MAGIE EXPERT');
						$trigger->setDone(false);
						$app['orm.em']->persist($trigger);
						$app['orm.em']->flush();
	
						// il obtient aussi la possibilité de choisir un sort de niveau 3
						$trigger = new \LarpManager\Entities\PersonnageTrigger();
						$trigger->setPersonnage($personnage);
						$trigger->setTag('MAGIE EXPERT SORT');
						$trigger->setDone(false);
						$app['orm.em']->persist($trigger);
						$app['orm.em']->flush();
					case 4:
						break;
						// il obtient aussi la possibilité de choisir un sort de niveau 4
						$trigger = new \LarpManager\Entities\PersonnageTrigger();
						$trigger->setPersonnage($personnage);
						$trigger->setTag('MAGIE MAITRE SORT');
						$trigger->setDone(false);
						$app['orm.em']->persist($trigger);
						$app['orm.em']->flush();
				}
			}
				
			// cas special littérature
			if ( $competence->getCompetenceFamily()->getLabel() == "Littérature")
			{
				switch ($competence->getLevel()->getId())
				{
					case 1: // le personnage obtient toutes les langues "très répandus"
						$repo = $app['orm.em']->getRepository('\LarpManager\Entities\Langue');
						$langues = $repo->findAll();
	
						foreach ( $langues as $langue)
						{
							if ( $langue->getDiffusion() == 2 )
							{
								if ( ! $personnage->isKnownLanguage($langue) )
								{
									$personnageLangue = new \LarpManager\Entities\PersonnageLangues();
									$personnageLangue->setPersonnage($personnage);
									$personnageLangue->setLangue($langue);
									$personnageLangue->setSource('LITTERATURE APPRENTI');
										
									$app['orm.em']->persist($personnageLangue);
									$app['orm.em']->flush();
								}
							}
						}
						break;
					case 2: // le personnage peux choisir trois languages supplémentaire (sauf parmi les anciens)
						$trigger = new \LarpManager\Entities\PersonnageTrigger();
						$trigger->setPersonnage($personnage);
						$trigger->setTag('LITTERATURE INITIE');
						$trigger->setDone(false);
						$app['orm.em']->persist($trigger);
						$app['orm.em']->flush();
						break;
					case 3: // le personnage peux choisir trois languages supplémentaire (dont un ancien)
						$trigger = new \LarpManager\Entities\PersonnageTrigger();
						$trigger->setPersonnage($personnage);
						$trigger->setTag('LITTERATURE EXPERT');
						$trigger->setDone(false);
						$app['orm.em']->persist($trigger);
						$app['orm.em']->flush();
						break;
					case 4: // le personnage obtient tous les languages courants
						$repo = $app['orm.em']->getRepository('\LarpManager\Entities\Langue');
						$langues = $repo->findAll();
	
						foreach ( $langues as $langue)
						{
							if ( $langue->getDiffusion() > 0 )
							{
								if ( ! $personnage->isKnownLanguage($langue) )
								{
									$personnageLangue = new \LarpManager\Entities\PersonnageLangues();
									$personnageLangue->setPersonnage($personnage);
									$personnageLangue->setLangue($langue);
									$personnageLangue->setSource('LITTERATURE MAITRE');
	
									$app['orm.em']->persist($personnageLangue);
								}
							}
						}
						break;
				}
			}
				
			// historique
			$historique = new \LarpManager\Entities\ExperienceUsage();
			$historique->setOperationDate(new \Datetime('NOW'));
			$historique->setXpUse($cout);
			$historique->setCompetence($competence);
			$historique->setPersonnage($personnage);
				
			$app['orm.em']->persist($competence);
			$app['orm.em']->persist($personnage);
			$app['orm.em']->persist($historique);
			$app['orm.em']->flush();
				
			$app['session']->getFlashBag()->add('success','Votre personnage a été sauvegardé.');
			return $app->redirect($app['url_generator']->generate('participant.personnage', array('participant' => $participant->getId())),301);
		}
	
		return $app['twig']->render('public/personnage/competence.twig', array(
				'form' => $form->createView(),
				'personnage' => $personnage,
				'competences' =>  $availableCompetences,
				'participant' => $participant
		));
	}
	
	/**
	 * Affiche le formulaire d'ajout d'un joueur
	 * 
	 * @param Request $request
	 * @param Application $app
	 */
	public function addAction(Request $request, Application $app)
	{
		$joueur = new \LarpManager\Entities\Joueur();
	
		$form = $app['form.factory']->createBuilder(new JoueurForm(), $joueur)
			->add('save','submit', array('label' => "Sauvegarder"))
			->getForm();
	
		$form->handleRequest($request);
	
		if ( $form->isValid() )
		{
			$joueur = $form->getData();
			$app['user']->setJoueur($joueur);
			
			$app['orm.em']->persist($app['user']);
			$app['orm.em']->persist($joueur);
			$app['orm.em']->flush();
	
			$app['session']->getFlashBag()->add('success', 'Vos informations ont été enregistrés.');
	
			return $app->redirect($app['url_generator']->generate('homepage'),301);
		}
	
		return $app['twig']->render('joueur/add.twig', array(
				'form' => $form->createView(),
		));
	}
	
	/**
	 * Recherche d'un joueur
	 * 
	 * @param Request $request
	 * @param Application $app
	 */
	public function searchAction(Request $request, Application $app)
	{
		$form = $app['form.factory']->createBuilder(new FindJoueurForm(), array())
			->add('submit','submit', array('label' => 'Rechercher'))
			->getForm();
		
		$form->handleRequest($request);
		
		if ( $form->isValid() )
		{
			$data = $form->getData();
			
			$type = $data['type'];
			$search = $data['search'];

			$repo = $app['orm.em']->getRepository('\LarpManager\Entities\Joueur');
			
			$joueurs = null;
			
			switch ($type)
			{
				case 'lastName' :
					$joueurs = $repo->findByLastName($search);
					break;
				case 'firstName' :
					$joueurs = $repo->findByFirstName($search);
					break;
				case 'numero' :
					// TODO
					break;
			}
			
			if ( $joueurs != null )
			{
				if ( $joueurs->count() == 0 )
				{
					$app['session']->getFlashBag()->add('error', 'Le joueur n\'a pas été trouvé.');
					return $app->redirect($app['url_generator']->generate('homepage'), 301);
				}
				else if ( $joueurs->count() == 1 )
				{
					$app['session']->getFlashBag()->add('success', 'Le joueur a été trouvé.');
					return $app->redirect($app['url_generator']->generate('joueur.detail.orga', array('index'=> $joueurs->first()->getId())),301);
				}
				else
				{
					$app['session']->getFlashBag()->add('success', 'Il y a plusieurs résultats à votre recherche.');
					return $app['twig']->render('joueur/search_result.twig', array(
						'joueurs' => $joueurs,
					));
				}
			}
			
			$app['session']->getFlashBag()->add('error', 'Désolé, le joueur n\'a pas été trouvé.');
		}
		
		return $app['twig']->render('joueur/search.twig', array(
				'form' => $form->createView(),
		));
	}
	
	/**
	 * Detail d'un joueur
	 * 
	 * @param Request $request
	 * @param Application $app
	 */
	public function adminDetailAction(Request $request, Application $app)
	{
		$id = $request->get('index');
	
		$joueur = $app['orm.em']->find('\LarpManager\Entities\Joueur',$id);
	
		if ( $joueur )
		{
			return $app['twig']->render('joueur/admin/detail.twig', array('joueur' => $joueur));
		}
		else
		{
			$app['session']->getFlashBag()->add('error', 'Le joueur n\'a pas été trouvé.');
			return $app->redirect($app['url_generator']->generate('homepage'));
		}	
	}
	
	/**
	 * Met a jours les points d'expérience des joueurs
	 *
	 * @param Application $app
	 * @param Request $request
	 */
	public function adminXpAction(Application $app, Request $request)
	{
		$id = $request->get('index');
	
		$joueur = $app['orm.em']->find('\LarpManager\Entities\Joueur',$id);
	
		$form = $app['form.factory']->createBuilder(new JoueurXpForm(), $joueur)
			->add('update','submit', array('label' => "Sauvegarder"))
			->getForm();
	
		$form->handleRequest($request);
			
		if ( $request->getMethod() == 'POST')
		{
			$newXps = $request->get('xp');
			$explanation = $request->get('explanation');
	
			$personnage = $joueur->getPersonnage();
			if ( $personnage->getXp() != $newXps)
			{
				$oldXp = $personnage->getXp();
				$gain = $newXps - $oldXp;
						
				$personnage->setXp($newXps);
				$app['orm.em']->persist($personnage);
						
				// historique
				$historique = new \LarpManager\Entities\ExperienceGain();
				$historique->setExplanation($explanation);
				$historique->setOperationDate(new \Datetime('NOW'));
				$historique->setPersonnage($personnage);
				$historique->setXpGain($gain);
				$app['orm.em']->persist($historique);
				$app['orm.em']->flush();
				
				$app['session']->getFlashBag()->add('success','Les points d\'expérience ont été sauvegardés');
			}
			
		}
	
		return $app['twig']->render('joueur/admin/xp.twig', array(
				'joueur' => $joueur,
		));
	}
	
	/**
	 * Detail d'un joueur (pour les orgas)
	 *
	 * @param Request $request
	 * @param Application $app
	 */
	public function detailOrgaAction(Request $request, Application $app)
	{
		$id = $request->get('index');
	
		$joueur = $app['orm.em']->find('\LarpManager\Entities\Joueur',$id);
	
		if ( $joueur )
		{
			return $app['twig']->render('joueur/admin/detail.twig', array('joueur' => $joueur));
		}
		else
		{
			$app['session']->getFlashBag()->add('error', 'Le joueur n\'a pas été trouvé.');
			return $app->redirect($app['url_generator']->generate('homepage'));
		}
	}
	
	/**
	 * Met à jour les informations d'un joueur
	 *
	 * @param Request $request
	 * @param Application $app
	 */
	public function updateAction(Request $request, Application $app)
	{
		$id = $request->get('index');
	
		$joueur = $app['orm.em']->find('\LarpManager\Entities\Joueur',$id);
	
		$form = $app['form.factory']->createBuilder(new JoueurForm(), $joueur)
			->add('update','submit', array('label' => "Sauvegarder"))
			->getForm();
	
		$form->handleRequest($request);
	
		if ( $form->isValid() )
		{
			$joueur = $form->getData();
	
			$app['orm.em']->persist($joueur);
			$app['orm.em']->flush();
			$app['session']->getFlashBag()->add('success', 'Le joueur a été mis à jour.');
	
			return $app->redirect($app['url_generator']->generate('joueur.detail', array('index'=> $id)));
		}
	
		return $app['twig']->render('joueur/update.twig', array(
				'joueur' => $joueur,
				'form' => $form->createView(),
		));
	}
}
