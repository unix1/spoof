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

namespace spoof\lib360\auth;

use spoof\lib360\api\Request;

/**
 * Request authenticator that allows all requests
 */
class AllowAll implements IAuthenticator
{

    /**
     * Authenticate a request
     *
     * @param \lib\Web\Request $request Request object
     *
     * @return boolean TRUE
     */
    public function authenticate(Request $request)
    {
        return true;
    }

}

?>
