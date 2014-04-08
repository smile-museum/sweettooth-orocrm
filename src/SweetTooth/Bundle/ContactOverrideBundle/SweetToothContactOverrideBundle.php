<?php

namespace SweetTooth\Bundle\ContactOverrideBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SweetToothContactOverrideBundle extends Bundle
{
    public function getParent()
    {
        return 'OroCRMContactBundle';
    }
}
