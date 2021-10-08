<?php

namespace App\Controller;

use SimpleXLSX;
use App\Entity\Voitures;
use App\Form\VoituresType;
use App\Repository\VoituresRepository;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/voitures")
 */
class VoituresController extends AbstractController
{
    /**
     * @Route("/", name="voitures_index", methods={"GET"})
     */
    public function index(VoituresRepository $voituresRepository): Response
    {

        $dir = $this->getParameter('upload_directory') . '/voiture.xlsx';
        var_dump($dir);
        $em = $this->getDoctrine()->getManager();
        $file = $this->getParameter('upload_directory') . '/voiture.xlsx';
        var_dump($dir);


        if ($xlsx = \SimpleXLSX::parse($file)) {

            foreach ($xlsx->rows() as $key => $r) {
                $produit = new Voitures();
                $produit->setMarque($r[0]);
                $produit->SetModele($r[1]);
                $produit->SetAnnee($r[1]);

                $em->persist($produit);
            }
            $em->flush();
        } else {
            echo \SimpleXLSX::parseError();
        }


        // return new JsonResponse();
        return $this->redirectToRoute('voitures_index', [], Response::HTTP_SEE_OTHER);
        return $this->render('voitures/index.html.twig', [
            'voitures' => $voituresRepository->findAll(),

        ]);
    }

    /**
     * @Route("/new", name="voitures_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $voiture = new Voitures();
        $form = $this->createForm(VoituresType::class, $voiture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($voiture);
            $entityManager->flush();

            return $this->redirectToRoute('voitures_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('voitures/new.html.twig', [
            'voiture' => $voiture,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="voitures_show", methods={"GET"})
     */
    public function show(Voitures $voiture): Response
    {
        return $this->render('voitures/show.html.twig', [
            'voiture' => $voiture,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="voitures_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Voitures $voiture): Response
    {
        $form = $this->createForm(VoituresType::class, $voiture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('voitures_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('voitures/edit.html.twig', [
            'voiture' => $voiture,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="voitures_delete", methods={"POST"})
     */
    public function delete(Request $request, Voitures $voiture): Response
    {
        if ($this->isCsrfTokenValid('delete' . $voiture->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($voiture);
            $entityManager->flush();
        }

        return $this->redirectToRoute('voitures_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/update", name="voitures_update", methods={"GET","POST"})
     */
    public function Excel1(Request $request)
    {
        $dir = $this->getParameter('upload_directory') . '/voiture.xlsx';
        var_dump($dir);
        $em = $this->getDoctrine()->getManager();
        $file = $this->getParameter('upload_directory') . '/voiture.xlsx';
        var_dump($dir);


        if ($xlsx = \SimpleXLSX::parse($file)) {

            foreach ($xlsx->rows() as $key => $r) {
                $produit = new Voitures();
                $produit->setMarque($r[0]);
                $produit->SetModele($r[1]);
                $produit->SetAnnee($r[1]);

                $em->persist($produit);
            }
            $em->flush();
        } else {
            echo \SimpleXLSX::parseError();
        }


        // return new JsonResponse();
        return $this->redirectToRoute('voitures_index', [], Response::HTTP_SEE_OTHER);
    }
}
