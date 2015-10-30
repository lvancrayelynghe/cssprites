<?php

namespace CSSPrites\ImageProcessor;

interface ImageProcessorInterface
{
    public function setConfig($config);

    public function setImage($image);

    public function getImage();

    public function create($width, $height, $background = null);

    public function load($path);

    public function save($path);

    public function insert(ImageProcessorInterface $image, $x = 0, $y = 0);

    public function getWidth();

    public function getHeight();
}
