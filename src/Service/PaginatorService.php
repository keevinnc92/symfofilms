<?php 
	namespace App\Service;

	use Doctrine\ORM\Tools\Pagination\Paginator;
	use Doctrine\ORM\EntityManagerInterface;

	class PaginatorService{

		// propiedades que necesitaré
		private $limit, $entityManager, $entityType = '';
		private $paginaActual = 1, $total = 0;


		// CONSTRUCTOR
		//como usaré autowiring indicaré los valores por defecto en services.yaml
		public function __construct(int $limit, EntityManagerInterface $entityManager){
			$this->limit = $limit;
			$this->entityManager = $entityManager;
		}

		public function setEntityType(string $entityType){
			$this->entityType = $entityType;
		}

		public function setLimit(int $limit){
			$this->limit = $limit;
		}

		public function getPaginaActual():int{
			return $this->paginaActual;
		}

		public function getTotal():int{
			return $this->total;
		}

		public function getTotalPages():int{
			return ceil($this->total / $this->limit);
		}


		// MÉTODOS

		public function paginate($dql, $page = 1):Paginator{
			$paginator = new Paginator($dql);

			$paginator->getQuery()
				->setFirstResult($this->limit * ($page - 1))
				->setMAxResults($this->limit);

			$this->paginaActual = $page;
			$this->total = $paginator->count();
			
			return $paginator;
		}

		public function findAllEntities(int $paginaActual = 1):Paginator{
			$consulta = $this->entityManager->createQuery(
				"SELECT p FROM $this->entityType p 
				ORDER BY p.id DESC");

			return $this->paginate($consulta, $paginaActual);
		}

	}