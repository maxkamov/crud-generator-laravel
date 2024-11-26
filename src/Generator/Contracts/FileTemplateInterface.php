<?php

namespace Maxkamov48\CrudGeneratorLaravel\Generator\Contracts;

interface FileTemplateInterface
{
    public function getReplacements();

    public function getStub();

    public function _getPath();
}
