<?php
/**
 * Created by PhpStorm.
 * User: tino
 * Date: 2/03/17
 * Time: 18:07
 */

namespace AppBundle\Model;


class DeployPayload
{
    protected $branch;

    public function __construct()
    {
    }

    public function setBranch($branch)
    {
        $this->branch = $branch;
        return $this;
    }
    public function getBranch()
    {
        return $this->branch;
    }

}