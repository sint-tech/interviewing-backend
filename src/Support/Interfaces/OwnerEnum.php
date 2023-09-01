<?php

namespace Support\Interfaces;

enum OwnerEnum: string
{
    case Admin = 'admin';

    case Organization = 'organization';
}
