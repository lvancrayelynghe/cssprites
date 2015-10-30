<?php

namespace CSSPrites\SpriteProcessor;

class HorizontalSpriteProcessor extends AbstractSpriteProcessor
{
    public function process()
    {
        $totalW = $this->spaces;
        $totalH = 0;
        foreach ($this->images->get() as $image) {
            $totalW += $image->getWidth() + $this->spaces;
            $totalH = max($image->getHeight(), $totalH);
        }
        $totalH += $this->spaces * 2;

        $this->image = $this->imageProcessor->create($totalW, $totalH, $this->background);

        $pointerX = $this->spaces;
        foreach ($this->images->sort('biggest')->get() as $image) {
            $image->setX($pointerX)->setY($this->spaces);

            $img = $this->imageProcessor->load($image->getFilepath());
            $this->image->insert($img, $image->getX(), $image->getY());

            $pointerX += $image->getWidth() + $this->spaces;
        }

        return $this;
    }
}
