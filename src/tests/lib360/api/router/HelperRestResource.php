<?php

/**
 *  This is Spoof.
 *  Copyright (C) 2011-2017  Spoof project.
 *
 *  Spoof is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Spoof is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with Spoof.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace spoof\tests\lib360\api\router;

use spoof\lib360\api\ForbiddenException;

class HelperRestResource
{

    public function submit(array $args)
    {
        return 'foo';
    }

    public function submitFoo(array $args)
    {
        return $args ?: 'foo submitted';
    }

    public function getArgs(array $args)
    {
        return $args;
    }

    public function getInvalidArgumentException(array $args)
    {
        throw new \InvalidArgumentException('Testing invalid argument exception');
    }

    public function getForbiddenException(array $args)
    {
        throw new ForbiddenException('Testing forbidden exception');
    }

}

?>
