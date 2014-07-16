<?php
/**
 * @author Tim Lytle <tim@timlytle.net>
 */
namespace Translate\Service\Translate;
interface TranslateInterface
{
    public function requestTranslation($text, $source, $target);

    public function getTargets($source = null);

    public function getSources($target = null);
} 