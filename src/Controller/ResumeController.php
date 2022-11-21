<?php

namespace App\Controller;

use App\Entity\Resume;
use App\Form\ResumeType;
use App\Repository\ResumeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/resume')]
class ResumeController extends AbstractController
{
    #[Route('/', name: 'app_resume_index', methods: ['GET'])]
    public function index(ResumeRepository $resumeRepository): Response
    {
        return $this->render('resume/index.html.twig', [
            'resumes' => $resumeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_resume_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ResumeRepository $resumeRepository, SluggerInterface $slugger): Response
    {
        $resume = new Resume();
        $form = $this->createForm(ResumeType::class, $resume);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $resumeFile = $form->get('filePath')->getData();
            if ($resumeFile) {
                $originalFilename = pathinfo($resumeFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid('', true) . '.' . $resumeFile->guessExtension();
                try {
                    $resumeFile->move(
                        $this->getParameter('resumes_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $resume->setFilePath($newFilename);
            }

            $resumeRepository->save($resume, true);
            return $this->redirectToRoute('app_resume_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('resume/new.html.twig', [
            'resume' => $resume,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_resume_show', methods: ['GET'])]
    public function show(Resume $resume): Response
    {
        $isFilePath = $resume->getFilePath();
        $fileExist = file_exists('uploads/resumes/' . $resume->getFilePath());
        $showFileLink = true;
        if (!$isFilePath || !$fileExist) {
            $showFileLink = false;
        }
        return $this->render('resume/show.html.twig', [
            'resume' => $resume,
            'showFileLink' => $showFileLink
        ]);
    }

    #[Route('/{id}/edit', name: 'app_resume_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Resume $resume,
        ResumeRepository $resumeRepository,
        SluggerInterface $slugger
    ): Response {
        $form = $this->createForm(ResumeType::class, $resume);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $resumeFile = $form->get('filePath')->getData();
            if ($resumeFile) {
                $originalFilename = pathinfo($resumeFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid('', true) . '.' . $resumeFile->guessExtension();
                try {
                    $resumeFile->move(
                        $this->getParameter('resumes_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $resume->setFilePath($newFilename);
            }
            $resumeRepository->save($resume, true);

            return $this->redirectToRoute('app_resume_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('resume/edit.html.twig', [
            'resume' => $resume,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_resume_delete', methods: ['POST'])]
    public function delete(Request $request, Resume $resume, ResumeRepository $resumeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $resume->getId(), $request->request->get('_token'))) {
            $resumeRepository->remove($resume, true);
        }

        return $this->redirectToRoute('app_resume_index', [], Response::HTTP_SEE_OTHER);
    }
}
