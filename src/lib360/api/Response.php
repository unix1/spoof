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

namespace spoof\lib360\api;

/**
 * Standard response object
 */
class Response
{

    /**
     * Handy constants for various response statuses
     */
    const STATUS_OK = 1;
    const STATUS_OK_CREATED = 2;
    const STATUS_METHOD_NOT_ALLOWED = 3;
    const STATUS_METHOD_NOT_IMPLEMENTED = 4;
    const STATUS_RESOURCE_NOT_FOUND = 5;
    const STATUS_ERROR = 6;
    const STATUS_AUTH_FAILED = 7;
    const STATUS_BAD_REQUEST = 8;
    const STATUS_FORBIDDEN = 9;

    /**
     * Status of the response
     */
    public $status;
    /**
     * Body of the response
     */
    public $body;

}

?>
