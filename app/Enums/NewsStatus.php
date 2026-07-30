<?php

namespace App\Enums;

enum NewsStatus: string
{
    case Draft = 'draft';

    case Pending = 'pending';

    case Approved = 'approved';

    case Rejected = 'rejected';

    case Published = 'published';
    
    case Scheduled = 'scheduled';
    }