<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use App\Repository\PopupSettingsRepository;

class AppExtension extends AbstractExtension
{
    private PopupSettingsRepository $popupRepo;

    public function __construct(PopupSettingsRepository $popupRepo)
    {
        $this->popupRepo = $popupRepo;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('popup', [$this, 'getPopup']),
        ];
    }

    public function getPopup()
{
    $popup = $this->popupRepo->find(1);

    if (!$popup) {
        // créer un objet temporaire pour éviter l'erreur Twig
        $popup = new \App\Entity\PopupSettings();
        $popup->setEnabled(false);
        $popup->setContent('');
    }

    return $popup;
}
}
