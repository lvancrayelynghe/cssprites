<?php

namespace CSSPrites\SpriteProcessor;

/**
 * Based on https://github.com/jakesgordon/bin-packing/blob/master/js/packer.js.
 */
class SimpleBinPackingSpriteProcessor extends AbstractSpriteProcessor
{
    protected $root = null;

    protected $working = null;

    public function process()
    {
        // Prepare working array
        $this->prepare();

        $maxW = 0;
        $maxH = 0;
        foreach ($this->working as $working) {
            $maxW += $working->w;
            $maxH += $working->h;
        }
        $sum = (int) (min($maxW, $maxH) / 8);

        while (!$this->checkAllFits()) {
            $sum = (int) ($sum * 1.05);
            $this->rePrepare($sum)->tryToFit();
        }

        // Intert into the final Image
        $this->insert();

        return $this;
    }

    public function prepare()
    {
        $this->images->sort('biggest');

        foreach ($this->images->get() as $idx => $image) {
            $this->working[$idx] = [
                'used' => false,
                'fit'  => false,
                'x'    => 0,
                'y'    => 0,
                'w'    => $image->getWidth() + $this->spaces,
                'h'    => $image->getHeight() + $this->spaces,
            ];
            $this->working[$idx] = (object) $this->working[$idx];
        }

        return $this;
    }

    public function rePrepare($size)
    {
        $this->root = [
            'used' => false,
            'x'    => 0,
            'y'    => 0,
            'w'    => $size,
            'h'    => $size,
        ];

        $this->root = (object) $this->root;

        foreach ($this->working as $idx => $working) {
            $this->working[$idx] = [
                'used' => false,
                'fit'  => false,
                'x'    => 0,
                'y'    => 0,
                'w'    => $working->w,
                'h'    => $working->h,
            ];
            $this->working[$idx] = (object) $this->working[$idx];
        }

        return $this;
    }

    public function tryToFit()
    {
        foreach ($this->working as $idx => $working) {
            $node = $this->find($this->root, $working->w, $working->h);
            if (!is_null($node)) {
                $fit                 = $this->split($node, $working->w, $working->h);
                $working->used       = true;
                $working->fit        = true;
                $working->x          = $fit->x;
                $working->y          = $fit->y;
                $this->working[$idx] = $working;
                unset($fit);
            }
        }

        return $this;
    }

    public function find($node, $w, $h)
    {
        if ($node->used === true) {
            $right = $this->find($node->right, $w, $h);
            $down  = $this->find($node->down, $w, $h);
            if (!is_null($right)) {
                return $right;
            } elseif (!is_null($down)) {
                return $down;
            }
        } elseif ($w <= $node->w && $h <= $node->h) {
            return $node;
        }

        return;
    }

    public function split($node, $w, $h)
    {
        $node->used  = true;

        $node->down = [
            'used' => false,
            'x'    => $node->x,
            'y'    => $node->y + $h,
            'w'    => $node->w,
            'h'    => $node->h - $h,
        ];

        $node->right = [
            'used' => false,
            'x'    => $node->x + $w,
            'y'    => $node->y,
            'w'    => $node->w - $w,
            'h'    => $h,
        ];

        $node->down  = (object) $node->down;
        $node->right = (object) $node->right;

        return $node;
    }

    // Check if all images fits
    public function checkAllFits()
    {
        foreach ($this->working as $working) {
            if ($working->fit === false) {
                return false;
            }
        }

        return true;
    }

    // Reduce the final image size
    public function reduce()
    {
        $width  = 0;
        $height = 0;
        foreach ($this->working as $working) {
            $width  = max($width, $working->x + $working->w);
            $height = max($height, $working->y + $working->h);
        }
        $this->root->w = $width - $this->spaces;
        $this->root->h = $height - $this->spaces;

        return $this;
    }

    // Create the final image
    public function insert()
    {
        $this->reduce();

        $this->image = $this->imageProcessor->create($this->root->w, $this->root->h, $this->background);
        foreach ($this->working as $idx => $working) {
            $image = $this->images->get($idx);
            $image->setX($working->x)->setY($working->y);

            $img = $this->imageProcessor->load($image->getFilepath());
            $this->image->insert($img, $image->getX(), $image->getY());
        }

        return $this;
    }
}
