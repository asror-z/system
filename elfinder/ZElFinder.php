<?php

namespace zetsoft\system\elfinder;

require Root . '/vendori/fileapp/ALL/vendor/autoload.php';

use elFinder;


class ZElFinder extends elFinder
{
    /**
     * Constructor
     *
     * @param  array  elFinder and roots configurations
     *
     * @author Dmitry (dio) Levashov
     */
    public function __construct($opts)
    {
       parent::__construct($opts);
    }

    protected function search($args)
    {
        $q = trim($args['q']);
        $target = !empty($args['target']) ? $args['target'] : null;
        $type = !empty($args['type']) ? $args['type'] : null;
        $result = array();
        $errors = array();

        if ($target) {
            if ($volume = $this->volume($target)) {
                $result = $volume->search($q, $mimes, $target, $type);
                $errors = array_merge($errors, $volume->error());
            }
        } else {
            foreach ($this->volumes as $volume) {
                $result = array_merge($result, $volume->search($q, $mimes, null, $type));
                $errors = array_merge($errors, $volume->error());
            }
        }

        $result = array('files' => $result);
        if ($errors) {
            $result['warning'] = $errors;
        }
        return $result;
    }



}
