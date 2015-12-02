<?php
namespace Genkgo\Push;

interface RecipientInterface {

    /**
     * @return string
     */
    public function getToken();

}