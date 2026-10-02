<?php

namespace App\DataFixtures;

use App\Entity\CategoryDonnees;
use App\Entity\CategoryNews;
use App\Entity\Document;
use App\Entity\Event;
use App\Entity\News;
use App\Entity\Partner;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

/**
 * Demo data: categories, consortium partners, a few members (one admin),
 * news, events and documents. Every account uses the password "demo1234".
 */
class AppFixtures extends Fixture
{
	public const DEMO_PASSWORD = 'demo1234';

	private const PARTNERS = [
		['LOGOLIFAT.png', "LIFAT (Laboratoire d'informatique Fondamentale et Appliquée de Tours (LIFAT - EA 6300), Université de Tours)", 'https://lifat.univ-tours.fr'],
		['logo_ees.jpg', 'EES (Equipe Éducation Éthique Santé (EES - EA 7505), Université de Tours)', 'https://www.univ-tours.fr'],
		['LALIGUE_LOGO.jpg', 'Ligue (Ligue Contre le Cancer)', 'https://www.ligue-cancer.net'],
		['capensis_logo.jfif', 'Capensis (Capensis-Joué Lès Tours)', 'https://www.capensis.fr/'],
		['Logo-FranceAssosSante.png', 'F. A. Santé (France Assos Santé Centre Val de Loire)', 'https://www.france-assos-sante.org/'],
		['CHUTours.png', 'CHU Tours', 'https://www.chu-tours.fr/'],
		['logo-oncocentre.png', 'Oncocentre', 'https://oncocentre.org/'],
		['logo_VilleTours.png', 'V. Tours (Direction des sports de la ville de Tours, "Parcours Forme et Bien Etre")', 'https://www.tours.fr/'],
	];

	private $hasher;
	private $projectDir;
	private $slugger;

	public function __construct(UserPasswordHasherInterface $hasher, string $projectDir)
	{
		$this->hasher = $hasher;
		$this->projectDir = $projectDir;
		$this->slugger = new AsciiSlugger('fr');
	}

	public function load(ObjectManager $manager)
	{
		$eventCategories = [];
		foreach (['Séminaire', 'Réunion', 'Evénement'] as $name) {
			$eventCategories[$name] = (new CategoryNews())->setName($name);
			$manager->persist($eventCategories[$name]);
		}

		$documentCategories = [];
		foreach (['Article', 'Compte-Rendu', 'Rapport', 'Prototype'] as $name) {
			$documentCategories[$name] = (new CategoryDonnees())->setName($name);
			$manager->persist($documentCategories[$name]);
		}

		$partners = $this->loadPartners($manager);

		$admin = $this->createUser($manager, 'admin@demo.local', 'Alice', 'Martin', $partners[0], true, ['ROLE_ADMIN']);
		$bruno = $this->createUser($manager, 'b.durand@demo.local', 'Bruno', 'Durand', $partners[1], true);
		$claire = $this->createUser($manager, 'c.petit@demo.local', 'Claire', 'Petit', $partners[5], true);
		// Verified but not yet validated: they show up in the admin validation queue
		$this->createUser($manager, 'd.moreau@demo.local', 'David', 'Moreau', $partners[2], false);
		$this->createUser($manager, 'e.leroy@demo.local', 'Emma', 'Leroy', $partners[4], false);

		$news = [
			['Lancement officiel du projet SQVALD', 'Le consortium s\'est réuni à Tours pour la réunion de lancement du projet.', '2021-03-15', true],
			['Premier séminaire scientifique', 'Présentation des premiers travaux sur la recommandation de soins de support.', '2021-05-20', true],
			['Atelier patients et aidants', 'Un atelier de co-conception a été organisé avec des patients et leurs aidants.', '2021-06-10', true],
			['Publication du catalogue des structures', 'La première version du catalogue régional des soins de support est en ligne.', '2021-07-02', true],
			['Prototype de suivi par objets connectés', 'Les premiers tests avec des montres connectées ont débuté.', '2021-07-26', true],
			['Deuxième séminaire du consortium', 'Point d\'étape à mi-parcours avec l\'ensemble des partenaires.', '2021-09-03', true],
			['Ouverture de l\'espace membres', 'Les membres du consortium peuvent désormais partager documents et vidéos.', '2021-09-20', true],
			['Appel à participation : étude pilote', 'Recrutement de patients volontaires pour l\'étude pilote.', '2021-09-25', false],
		];
		foreach ($news as [$title, $resume, $date, $active]) {
			$item = (new News())
				->setTitle($title)
				->setResume($resume)
				->setContent('<p>' . $resume . '</p>')
				->setCreatedAt(new \DateTime($date))
				->setIsActive($active)
				->setSlug($this->slug($title))
				->addAuthor($admin);
			$manager->persist($item);
		}

		$events = [
			['Réunion de lancement', 'Réunion', 'Réunion de lancement du consortium SQVALD.', '2022-03-15', '2022-03-15', 'Université de Tours', true],
			['Séminaire : recommandation de soins de support', 'Séminaire', 'Présentation des premiers modèles de recommandation.', '2022-05-20', '2022-05-21', 'LIFAT, Tours', true],
			['Atelier de co-conception patients', 'Evénement', 'Atelier ouvert aux patients et aux aidants.', '2022-06-10', '2022-06-10', 'CHU de Tours', true],
			['Séminaire à mi-parcours', 'Séminaire', 'Bilan à mi-parcours du projet.', '2022-09-03', '2022-09-03', 'Blois', true],
			['Comité de pilotage', 'Réunion', 'Comité de pilotage annuel.', '2022-11-17', '2022-11-17', 'Orléans', true],
			['Séminaire de clôture', 'Séminaire', 'Présentation des résultats finaux.', '2022-12-08', '2022-12-08', 'Tours', false],
		];
		foreach ($events as [$title, $category, $resume, $begin, $end, $place, $active]) {
			$event = (new Event())
				->setTitle($title)
				->setCategory($eventCategories[$category])
				->setResume($resume)
				->setContent('<p>' . $resume . '</p>')
				->setDateBegin(new \DateTime($begin))
				->setDateEnd(new \DateTime($end))
				->setPlace($place)
				->setIsActive($active)
				->setCreatedAt(new \DateTime($begin . ' -1 month'))
				->setSlug($this->slug($title))
				->addAuthor($bruno);
			$manager->persist($event);
		}

		$documents = [
			['Compte rendu de la réunion de lancement', 'Compte-Rendu', 'Synthèse des échanges et décisions de la réunion de lancement.', '2021-03-18', true],
			['Rapport WP2 : état de l\'art', 'Rapport', 'État de l\'art sur la recommandation de soins de support.', '2021-04-30', true],
			['Prototype web de recommandation', 'Prototype', 'Maquettes et description du premier prototype.', '2021-06-25', true],
			['Soins de support et qualité de vie', 'Article', 'Article de synthèse sur les soins de support en ALD.', '2021-07-05', true],
			['Compte rendu du séminaire à mi-parcours', 'Compte-Rendu', 'Bilan des work packages et prochaines étapes.', '2021-09-06', true],
			['Rapport WP4 : suivi par objets connectés', 'Rapport', 'Indicateurs de qualité de vie mesurés par capteurs portatifs.', '2021-09-15', true],
			['Compte rendu du COPIL', 'Compte-Rendu', 'Brouillon du compte rendu du comité de pilotage.', '2021-09-26', false],
		];
		foreach ($documents as [$title, $category, $resume, $date, $active]) {
			$document = (new Document())
				->setTitle($title)
				->setCategorydonnees($documentCategories[$category])
				->setResume($resume)
				->setContent('<p>' . $resume . '</p>')
				->setCreatedAt(new \DateTime($date))
				->setIsActive($active)
				->setSlug($this->slug($title))
				->addAuthor($claire);
			$document->setUpdateAt(new \DateTime($date));
			$manager->persist($document);
		}

		$manager->flush();
	}

	/**
	 * @return Partner[]
	 */
	private function loadPartners(ObjectManager $manager): array
	{
		$filesystem = new Filesystem();
		$logos = $this->projectDir . '/public/assets/img/logos/';
		$uploads = $this->projectDir . '/public/uploads/partners/';

		$partners = [];
		foreach (self::PARTNERS as [$logo, $content, $url]) {
			// Logos are copied into the upload folder, as if an admin had uploaded them
			$filesystem->copy($logos . $logo, $uploads . $logo);
			$partner = (new Partner())
				->setIllustration($logo)
				->setContent($content)
				->setBtnUrl($url);
			$partner->setUpdateAt(new \DateTime());
			$manager->persist($partner);
			$partners[] = $partner;
		}

		return $partners;
	}

	private function createUser(ObjectManager $manager, string $email, string $firstname, string $lastname, Partner $partner, bool $validated, array $roles = []): User
	{
		$user = (new User())
			->setEmail($email)
			->setFirstname($firstname)
			->setLastname($lastname)
			->setRoles($roles)
			->setIsVerified(true)
			->setIsValide($validated)
			->setPlace('Tours')
			->setCreatedAt(new \DateTime('2021-03-01'))
			->setPartner($partner);
		$user->setPassword($this->hasher->hashPassword($user, self::DEMO_PASSWORD));
		$manager->persist($user);

		return $user;
	}

	private function slug(string $title): string
	{
		return $this->slugger->slug($title)->lower()->toString();
	}
}
