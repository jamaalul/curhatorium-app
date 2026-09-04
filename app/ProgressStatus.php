<?php

namespace App;

enum ProgressStatus: string
{
    case InProgress = 'in_progress';
    case Completed = 'completed';
}
