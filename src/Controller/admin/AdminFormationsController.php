<?php

namespace App\Controller\admin;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Gère les routes de la page d'administration des formations
 *
 */
class AdminFormationsController extends AbstractController {
    
    /**
     * 
     * @var FormationRepository
     */
    private $formationRepository;
    
    /**
     * 
     * @var CategorieRepository
     */
    private $categorieRepository;
    
    /**
     * Constructeur
     * @param FormationRepository $formationRepository
     */
    function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository) {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository = $categorieRepository;
    }
    
    /**
     * Fonction pour initier une route vers la page admin
     * @Route("/admin", name="admin.formations")
     * @return Response
     */
    public function index(): Response{
        $formations = $this->formationRepository->findAllOrderBy('title', 'ASC');
        $categories = $this->categorieRepository->findAll();
        return $this->render("admin/admin.formations.html.twig", [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }
    
    /**
     * Fonction pour supprimer une formation
     * @Route("/admin/del.formation/{id}", name="admin.del.formation")
     * @param Formation $formations
     * @return Response
     */
    public function del(Formation $formations): Response{
        $this->formationRepository->remove($formations, true);
        return $this->redirectToRoute('admin.formations');
    }
    
    /**
     * Fonction pour editer une formation
     * @Route("/admin/edit/{id}", name="admin.edit.formations")
     * @param Formation $formations
     * @param Request $request
     * @return Response
     */
    public function edit(Formation $formations, Request $request): Response{
        $formFormation = $this->createForm(FormationType::class, $formations);
        
        $formFormation->handleRequest($request);
        if($formFormation->isSubmitted() && $formFormation->isValid()){
            $this->formationRepository->add($formations, true);
            return $this->redirectToRoute('admin.formations');
        }
        
        return $this->render("admin/admin.formation.edit.html.twig", [
            'formations' => $formations,
            'formformation' => $formFormation->createView()
        ]);
    }
    
    /**
     * Fonction pour ajouter d'une formation
     * @Route("/admin/add", name="admin.formations.add")
     * @param Request $request
     * @return Response
     */
    public function add(Request $request): Response{
        $formations = new Formation();
        $formFormation = $this->createForm(FormationType::class, $formations);
        
        $formFormation->handleRequest($request);
        if($formFormation->isSubmitted() && $formFormation->isValid()){
            $this->formationRepository->add($formations, true);
            return $this->redirectToRoute('admin.formations');
        }
        
        return $this->render("admin/admin.formation.add.html.twig", [
            'formations' => $formations,
            'formformation' => $formFormation->createView()                
        ]);
    }
    
     /**
     * Fonction de tri des formation
     * @Route("/admin/formations/tri/{champ}/{ordre}/{table}", name="admin.formations.sort")
     * @param type $champ
     * @param type $ordre
     * @param type $table
     * @return Response
     */
    public function sort($champ, $ordre, $table=""): Response{
        if($table == ""){
            $formations = $this->formationRepository->findAllOrderBy($champ, $ordre);
        }else{
            $formations = $this->formationRepository->findAllOrderByT($champ, $ordre, $table);
        }
        $categories = $this->categorieRepository->findAll();
        return $this->render('admin/admin.formations.html.twig', [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }
    
    /**
     * Fonction qui trouve les formations en fonction d'un champ
     * @Route("/admin/formations/recherche/{champ}/{table}", name="admin.formations.findallcontain")
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    public function findAllContain($champ, Request $request, $table=""): Response{
        $valeur = $request->get("recherche");
        if($table !=""){
            $formations = $this->formationRepository->findByContainValueTable($champ, $valeur, $table);
        }else{
            $formations = $this->formationRepository->findByContainValue($champ, $valeur);
        }
        $categories = $this->categorieRepository->findAll();
        return $this->render('admin/admin.formations.html.twig', [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }  
}