<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Reaction;
use App\Entity\Resume;
use App\Entity\SendResume;
use App\Form\ResumeType;
use App\Repository\CompanyRepository;
use App\Repository\ReactionRepository;
use App\Repository\ResumeRepository;
use App\Repository\SendResumeRepository;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\FileUploader;

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

    /**
     * @throws Exception
     */
    #[Route('/statistic', name: 'app_resume_statistic', methods: ['GET'])]
    public function statistic(ResumeRepository $resumeRepository): Response
    {
        $sentResumeStat = $resumeRepository->sentResumeStat();
        $dataSentResume = [];
        $dataPosition = [];

        foreach ($sentResumeStat as $item) {
            $dataSentResume[] = $item['numberOfCompanies'];
            $dataPosition[] = $item['position'];
        }

        return $this->render('resume/statistic.html.twig', [
            'resumes' => $resumeRepository->findAll(),
            'sentResumeStat' => $resumeRepository->sentResumeStat(),
            'dataSentResume' => $dataSentResume,
            'dataPosition' => $dataPosition,
            'likesStat' => $resumeRepository->likesDislikeStat(true),
            'dislikesStat' => $resumeRepository->likesDislikeStat(false)
        ]);
    }

    #[Route('/new', name: 'app_resume_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ResumeRepository $resumeRepository, FileUploader $fileUploader): Response
    {
        $resume = new Resume();
        $form = $this->createForm(ResumeType::class, $resume);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $resumeFile = $form->get('filePath')->getData();
            if ($resumeFile) {
                $newFilename = $fileUploader->upload($resumeFile);
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
    public function show(
        Resume $resume,
        ReactionRepository $reactionRepository,
        CompanyRepository $companyRepository,
    ): Response {
        $isFilePath = $resume->getFilePath();
        $fileExist = file_exists('uploads/resumes/' . $resume->getFilePath());
        $showFileLink = true;
        if (!$isFilePath || !$fileExist) {
            $showFileLink = false;
        }

        return $this->render('resume/show.html.twig', [
            'resume' => $resume,
            'showFileLink' => $showFileLink,
            'like' => $this->numberOfLikes($reactionRepository, $resume->getId(), true),
            'dislike' => $this->numberOfLikes($reactionRepository, $resume->getId(), false),
            'companies' => $companyRepository->findAll(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_resume_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Resume $resume,
        ResumeRepository $resumeRepository,
        FileUploader $fileUploader
    ): Response {
        $form = $this->createForm(ResumeType::class, $resume);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $resumeFile = $form->get('filePath')->getData();
            if ($resumeFile) {
                $newFilename = $fileUploader->upload($resumeFile);
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

    #[Route('/new-up-reaction/{resume}', name: 'app_reaction_new', methods: ['POST'])]
    public function reactionUpAdd(ReactionRepository $reactionRepository, Request $request, Resume $resume): Response
    {
        $reaction = new Reaction();
        $reaction->setResume($resume);
        $reaction->setUser(1);
        $reaction->setReactionValue($request->request->get('reactionValue'));
        $reaction->setDateCreate(new \DateTime());
        $reactionRepository->save($reaction, true);
        return $this->redirectToRoute('app_resume_show', ['id' => $resume->getId()], Response::HTTP_SEE_OTHER);
    }

    public function numberOfLikes(ReactionRepository $reactionRepository, $resume, $reactionValue): int
    {
        $reactions = $reactionRepository->findBy(['resume' => $resume, 'reactionValue' => $reactionValue]);
        return count($reactions);
    }

    /**
     * @throws Exception
     */
    #[Route('/sent_resume_save/{resume}', name: 'sent_resume_save', methods: ['POST'])]
    public function sentResumeSave(
        Resume $resume,
        Request $request,
        SendResumeRepository $sendResumeRepository
    ): Response {
        $selectedCountry = $request->request->get('selectedCompany');
        $rowIsset = $sendResumeRepository->findBy(["resume" => $resume, 'company' => $selectedCountry]);
        if ($rowIsset) {
            return $this->render(
                'bundles/TwigBundle/Exception/error.html.twig',
                ['route' => 'app_resume_show', 'id' => $resume->getId(), 'message' => 'Company alredy saved']
            );
        }
        $sendResumeRepository->sentResumeSave($resume->getId(), $selectedCountry);
        return $this->redirectToRoute('app_resume_show', ['id' => $resume->getId()], Response::HTTP_SEE_OTHER);
    }
}
