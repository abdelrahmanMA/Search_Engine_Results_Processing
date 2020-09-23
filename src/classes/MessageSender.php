<?php

namespace Serp;

class MessageSender
{
    private $_currentindex = 0;
    private $_ratio = 0;

    function __construct(int $count)
    {
        $this->_ratio = (1 / $count) * 100;
        $this->send(['progress' => 0]);
    }

    public function incrementCurrent()
    {
        $this->_currentindex++;
    }

    public function send(array $msg)
    {
        if(array_key_exists('progress', $msg)){
            $progress = $this->_currentindex * $this->_ratio;
            $progress += $msg['progress'] * $this->_ratio;
            $msg['progress'] = $progress;
        }
        file_put_contents(TMPDIR . 'result.txt', json_encode($msg));
    }

}