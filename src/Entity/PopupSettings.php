<?php
// src/Entity/PopupSettings.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class PopupSettings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    private $id;

    #[ORM\Column(type:"boolean")]
    private $enabled = false;

    #[ORM\Column(type:"text", nullable:true)]
    private $content;

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;
        return $this;
    }
    #[ORM\Column(type:"string", length:20, nullable:true)]
    private $fontSize; // ex: '1rem', '16px', '1.2rem'
    
    #[ORM\Column(type:"string", length:20, nullable:true)]
    private $fontColor; // ex: '#333', 'red', 'blue'
    
    public function getFontSize(): ?string
    {
        return $this->fontSize;
    }
    
    public function setFontSize(?string $fontSize): self
    {
        $this->fontSize = $fontSize;
        return $this;
    }
    
    public function getFontColor(): ?string
    {
        return $this->fontColor;
    }
    
    public function setFontColor(?string $fontColor): self
    {
        $this->fontColor = $fontColor;
        return $this;
    }
    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;
        return $this;
    }
}
