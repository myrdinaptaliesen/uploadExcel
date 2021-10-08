<?php

namespace App\Controller;

use SimpleXLSX;
use App\Entity\Voitures;
use App\Entity\FileUpload;
use App\Form\FileUploadType;
use App\Repository\FileUploadRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * @Route("/file/upload")
 */
class FileUploadController extends AbstractController
{
    /**
     * @Route("/", name="file_upload_index", methods={"GET"})
     */
    public function index(FileUploadRepository $fileUploadRepository): Response
    {
        return $this->render('file_upload/index.html.twig', [
            'file_uploads' => $fileUploadRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="file_upload_new", methods={"GET","POST"})
     */
    public function new(Request $request, SluggerInterface $slugger, FileUploadRepository $fileUploadRepository): Response
    {
        $fileUpload = new FileUpload();
        $form = $this->createForm(FileUploadType::class, $fileUpload);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('upload_file')->getData();

                if ($file) {
                    $originalFilename = pathinfo(
                        $file->getClientOriginalName(),PATHINFO_FILENAME
                    );
                    // ceci est nécessaire pour inclure en toute sécurité le nom de fichier dans l'URL
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();
                    try {
                        $file->move(
                        $this->getParameter('files_directory'),
                        $newFilename
                        );
                        } catch (FileException $e) {
                       
                    // ... gérer l'exception si quelque chose se produit pendant le téléchargement du fichier
                }
                // met à jour la propriété 'photoEleve' pour stocker le nom du fichier PDF
                // au lieu de son contenu
                $fileUpload->setFile($newFilename);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($fileUpload);
            $entityManager->flush();

/****************************Remplissage de la base de données *****************************/
// $entityManager->remove($fileUploadRepository->findAll());
// $entityManager->flush();

$xlsxFile = $this->getParameter('files_directory') . '/'.$newFilename;

            if ($xlsx = \SimpleXLSX::parse($xlsxFile)) {

                foreach ($xlsx->rows() as $key => $r) {
                    $produit = new Voitures();
                    $produit->setMarque($r[0]);
                    $produit->SetModele($r[1]);
                    $produit->SetAnnee($r[1]);
    
                    $entityManager->persist($produit);
                }
                 $entityManager->flush();
            } else {
                echo \SimpleXLSX::parseError();
            }

            return $this->redirectToRoute('file_upload_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('file_upload/new.html.twig', [
            'file_upload' => $fileUpload,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="file_upload_show", methods={"GET"})
     */
    public function show(FileUpload $fileUpload): Response
    {
        return $this->render('file_upload/show.html.twig', [
            'file_upload' => $fileUpload,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="file_upload_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, FileUpload $fileUpload): Response
    {
        $form = $this->createForm(FileUpload1Type::class, $fileUpload);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('file_upload_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('file_upload/edit.html.twig', [
            'file_upload' => $fileUpload,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="file_upload_delete", methods={"POST"})
     */
    public function delete(Request $request, FileUpload $fileUpload): Response
    {
        if ($this->isCsrfTokenValid('delete' . $fileUpload->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($fileUpload);
            $entityManager->flush();
        }

        return $this->redirectToRoute('file_upload_index', [], Response::HTTP_SEE_OTHER);
    }
}
