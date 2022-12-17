<?php

namespace App\Controller\admin;

use App\Entity\Formation;
use App\Entity\Playlist;
use App\Form\PlaylistType;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Description of AdminPlaylistController
 *
 * @author Kalwin
 */
class AdminPlaylistController extends AbstractController {

  /**
     * 
     * @var PlaylistRepository
     */
    private $playlistRepository;

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

    private const RETOURNEPPLAYLIST= "admin/admin.playlist.html.twig";
    private const RETOURNEADMINPLAYLIST = "admin.playlist";

    function __construct(PlaylistRepository $playlistRepository,CategorieRepository $categorieRepository, FormationRepository $formationRespository) 
    {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRespository;
    }

    /**
     * @Route("/admin/playlist", name="admin.playlist")
     * @return Response
     */
    public function index(): Response
    {
        $playlist = $this->playlistRepository->findAllOrderByName('ASC');
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::RETOURNEPPLAYLIST, [
            'playlist' => $playlist,
            'categories' => $categories            
        ]);
    }

    /**
     * @Route("/admin/playlist/tri/{champ}/{ordre}", name="admin.playlist.sort")
     * @param type $champ
     * @param type $ordre
     * @return Response
     */
    public function sort($champ, $ordre): Response
    {
        switch ($champ) {
            case "name":
                $playlist = $this->playlistRepository->findAllOrderByName($ordre);
                break;
            case "nbformations":
                $playlist = $this->playlistRepository->findAllOrderByNbFormations($ordre);
                break;
            default:
                error();
                break;
        }
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::RETOURNEPPLAYLIST, [
            'playlist' => $playlist,
            'categories' => $categories
        ]);
    }        

    /**
     * @Route("/admin/playlist/recherche/{champ}/{table}", name="admin.playlist.findallcontain")
     * @param type $champ
     * @param Request $request
     * @param type $table
     * @return Response
     */
    public function findAllContain($champ, Request $request, $table=""): Response
    {
        $valeur = $request->get("recherche");
        $playlist = $this->playlistRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::RETOURNEPPLAYLIST, [
            'playlist' => $playlist ,
            'categories' => $categories,            
            'valeur' => $valeur,
            'table' => $table
        ]);
    }  



     /**
     * @Route("/admin/playlist/ajout", name="admin.playlist.ajout")
     * @param Request $request
     * @return Response
     */
     public function ajout( Request $request): Response
    {
        $playlist = new Playlist();
        $formPlaylist = $this->createForm(PlaylistType::class, $playlist);

        $formPlaylist->handleRequest($request);
        if ($formPlaylist->isSubmitted()&& $formPlaylist->isValid()){
            $this->playlistRepository->add($playlist,true);
            return $this->redirectToRoute(self::RETOURNEADMINPLAYLIST);
        }

        return $this->render("admin/admin.playlist.ajout.html.twig", [
            'playlist' => $playlist,
            'formPlaylist' => $formPlaylist->createView()
        ]);


    }

      /**
     * @Route("/admin/playlist/edit/{id}", name="admin.playlist.edit")
     * @param Formation $playlist
     * @param Request $request
     * @return Response
     */
     public function edit(Playlist $playlist, Request $request): Response
    {
        $formPlaylist = $this->createForm(PlaylistType::class, $playlist);

        $formPlaylist->handleRequest($request);
        if ($formPlaylist->isSubmitted()&& $formPlaylist->isValid()){
            $this->playlistRepository->add($playlist,true);
            return $this->redirectToRoute(self::RETOURNEADMINPLAYLIST);
        }

        return $this->render("admin/admin.playlist.edit.html.twig", [
            'playlist' => $playlist,
            'formPlaylist' => $formPlaylist->createView()
        ]);  
    }


    /**
     * @Route("/admin/playlist/suppr/{id}", name="admin.playlist.suppr")
     * @param Playlist playlist
     * @return Response
     */
    public function suppr(Playlist $playlist): Response
    {
        $this->playlistRepository->remove($playlist,true);
        return $this->redirectToRoute(self::RETOURNEADMINPLAYLIST);

    }
 }