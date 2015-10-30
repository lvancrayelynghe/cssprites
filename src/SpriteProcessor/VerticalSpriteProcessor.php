<?php

namespace CSSPrites\SpriteProcessor;

class VerticalSpriteProcessor extends AbstractSpriteProcessor
{
    public function process()
    {
        $totalW = 0;
        $totalH = $this->spaces;
        foreach ($this->images->get() as $image) {
            $totalW = max($image->getWidth(), $totalW);
            $totalH += $image->getHeight() + $this->spaces;
        }
        $totalW += $this->spaces * 2;

        $this->image = $this->imageProcessor->create($totalW, $totalH, $this->background);

        $pointerY = $this->spaces;
        foreach ($this->images->sort('biggest')->get() as $image) {
            $image->setX($this->spaces)->setY($pointerY);

            $img = $this->imageProcessor->load($image->getFilepath());
            $this->image->insert($img, $image->getX(), $image->getY());

            $pointerY += $image->getHeight() + $this->spaces;
        }

        return $this;
    }
}
