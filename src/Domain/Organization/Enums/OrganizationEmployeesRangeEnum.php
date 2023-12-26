<?php

namespace Domain\Organization\Enums;

enum OrganizationEmployeesRangeEnum: string
{
    case OneTo10 = '1-10';

    case ElvenTo50 = '11-50';

    case FiftyOneTo200 = '51-200';

    case TwoHundredTo500 = '201-500';

    case FiveHundredTo1000 = '501-1000';

    case OneThousandTo5000 = '1001-5000';

    case FiveThousandTo10000 = '5001-10000';

    case MoreThan10000Thousand = '+10000';
}
