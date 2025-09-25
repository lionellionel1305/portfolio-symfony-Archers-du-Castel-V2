<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CalendarController extends AbstractController
{
    #[Route('/calendrier??agenda', name: 'app_calendar')]
    public function index()
    {
        return $this->render('calendar/index.html.twig');
    }

    #[Route('/api/events', name: 'api_events', methods: ['GET'])]
    public function events(EventRepository $eventRepository): JsonResponse
    {
        $events = $eventRepository->findAll();
        $data = [];

        foreach ($events as $event) {
            // Couleur selon catégorie
            $color = match($event->getCategory() ?? '') {
                'Réunion' => '#0078D4',
                'Perso'   => '#107C10',
                'Urgent'  => '#D83B01',
                default   => '#605E5C',
            };

            $data[] = [
                'id'       => $event->getId(),
                'title'    => $event->getTitle(),
                'start'    => $event->getStart()->format('Y-m-d H:i:s'),
                'end'      => $event->getEnd()?->format('Y-m-d H:i:s'),
                'allDay'   => $event->isAllday(),
                'color'    => $color,
                'category' => $event->getCategory(), // <-- important !
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/api/events/add', name: 'api_event_add', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function add(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $event = new Event();
        $event->setTitle($data['title']);
        $event->setStart(new \DateTime($data['start']));
        if (!empty($data['end'])) {
            $event->setEnd(new \DateTime($data['end']));
        }
        $event->setAllday($data['allDay'] ?? false);
        $event->setCategory($data['category'] ?? null);

        $em->persist($event);
        $em->flush();

        return new JsonResponse(['status' => 'ok', 'id' => $event->getId()]);
    }

    #[Route('/api/events/update/{id}', name: 'api_event_update', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function update(int $id, Request $request, EventRepository $eventRepository, EntityManagerInterface $em): JsonResponse
    {
        $event = $eventRepository->find($id);
        if (!$event) {
            return new JsonResponse(['status' => 'error', 'message' => 'Event not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $event->setTitle($data['title'] ?? $event->getTitle());
        if (!empty($data['start'])) {
            $event->setStart(new \DateTime($data['start']));
        }
        if (!empty($data['end'])) {
            $event->setEnd(new \DateTime($data['end']));
        }
        if (isset($data['allDay'])) {
            $event->setAllday($data['allDay']);
        }
        $event->setCategory($data['category'] ?? $event->getCategory());

        $em->flush();

        return new JsonResponse(['status' => 'ok']);
    }

    #[Route('/api/events/delete/{id}', name: 'api_event_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(int $id, EventRepository $eventRepository, EntityManagerInterface $em): JsonResponse
    {
        $event = $eventRepository->find($id);
        if (!$event) {
            return new JsonResponse(['status' => 'error', 'message' => 'Event not found'], 404);
        }

        $em->remove($event);
        $em->flush();

        return new JsonResponse(['status' => 'ok']);
    }
}
