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

namespace spoof\tests\lib360\api\request;

use spoof\lib360\api\IRouter;
use spoof\lib360\api\Request;
use spoof\lib360\api\Response;

class HelperRouter implements IRouter
{

    const SUCCESS = 1;
    const ERROR_BAD_METHOD = 2;
    const ERROR_GENERAL = 3;

    protected $action;

    public function __construct($action = 1)
    {
        $this->action = $action;
    }

    public function handleRequest(Request $request)
    {
        $response = null;
        switch ($this->action) {
            case self::ERROR_BAD_METHOD:
                throw new \BadMethodCallException();
                break;
            case self::ERROR_GENERAL:
                throw new \Exception();
                break;
            case self::SUCCESS:
            default:
                $response = new Response();
                $response->status = Response::STATUS_OK;
        }
        return $response;
    }

}

?>
