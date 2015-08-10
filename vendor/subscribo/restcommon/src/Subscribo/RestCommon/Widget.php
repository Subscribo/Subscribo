<?php

namespace Subscribo\RestCommon;

use Subscribo\RestCommon\ServerRequest;

class Widget extends ServerRequest
{
    const TYPE = 'widget';

    public $content = '';

    public function import(array $data)
    {
        if (isset($data['content'])) {
            $this->content = $data['content'];
        }
        return parent::import($data);
    }


    public function export()
    {
        $result = parent::export();
        $result['content'] = $this->content;

        return $result;
    }
}
