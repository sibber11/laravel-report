<?php

namespace BlinkerBoy\Report;

class Reporter
{
    public function name(string $name, string $className): Type
    {
        $object = new Type($name, $className);
        $object->setReporter($this);


        return app(Service::class)->addReport($object);
    }
}
