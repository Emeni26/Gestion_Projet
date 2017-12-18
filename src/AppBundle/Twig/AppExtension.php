<?php

namespace AppBundle\Twig;


class AppExtension extends \Twig_Extension
{
    /**
     * @inheritDoc
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('cast_to_array', array($this, 'castToArray'))
        );
    }

    public function castToArray($stdClassObject) {
        $response = array();
        foreach ($stdClassObject as $key => $value) {
            $response[] = array($key, $value);
        }
        return $response;
    }


    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'array_extension';
    }
}