<?php

namespace App;

enum QuizQuestionType: string
{
    case MultipleChoice = 'multiple_choice';
    case ShortAnswer = 'short_answer';
}
