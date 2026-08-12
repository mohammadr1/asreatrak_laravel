<?php

namespace App\Enums;

enum WatermarkType:string
{
    case None='none';

    case General='general';

    case Personal='personal';
}