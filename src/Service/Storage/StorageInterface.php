<?php
/**
 * @author Tim Lytle <tim@timlytle.net>
 */

namespace Translate\Service\Storage;


interface StorageInterface
{
    public function set($number, $source, $target);

    public function get($nunber);

    public function log($number, $text, $translation);

    public function last($number);

    public function count($number, \DateTime $date = null);
} 