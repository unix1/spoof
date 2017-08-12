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

use spoof\lib360\api\Request;
use spoof\lib360\api\Response;
use spoof\tests\TestCase;
use spoof\tests\Util;

class HandlerTest extends TestCase
{

    /**
     * @covers \spoof\lib360\api\request\Handler::setAuthenticator
     */
    public function testSetAuthenticator()
    {
        $handler = new HelperHandler();
        $handler->setAuthenticator(new HelperAuthenticator());
        $authenticator = Util::getProtectedProperty($handler, 'authenticator');
        $this->assertInstanceOf('\spoof\tests\lib360\api\request\HelperAuthenticator', $authenticator);
    }

    /**
     * @covers \spoof\lib360\api\request\Handler::setRouter
     */
    public function testSetRouter()
    {
        $handler = new HelperHandler();
        $handler->setRouter(new HelperRouter());
        $router = Util::getProtectedProperty($handler, 'router');
        $this->assertInstanceOf('\spoof\lib360\api\IRouter', $router);
    }

    /**
     * @covers \spoof\lib360\api\request\Handler::authenticate
     */
    public function testAuthenticate_Reject()
    {
        $handler = new HelperHandler();
        $handler->setAuthenticator(new HelperAuthenticator(false));
        $authenticated = $handler->authenticate(new Request());
        $this->assertFalse($authenticated, "Failed to reject authentication request");
    }

    /**
     * @covers \spoof\lib360\api\request\Handler::authenticate
     */
    public function testAuthenticate_Allow()
    {
        $handler = new HelperHandler();
        $handler->setAuthenticator(new HelperAuthenticator(true));
        $authenticated = $handler->authenticate(new Request());
        $this->assertTrue($authenticated, "Failed to allow authentication request");
    }

    /**
     * @covers \spoof\lib360\api\request\Handler::handle
     */
    public function testHandle_FailRejectAuthentication()
    {
        $handler = new HelperHandler();
        $handler->setAuthenticator(new HelperAuthenticator(false));
        $response = $handler->handle();
        $this->assertEquals($response->status, Response::STATUS_AUTH_FAILED);
    }

    /**
     * @covers \spoof\lib360\api\request\Handler::handle
     */
    public function testHandle_FailBadMethodCallException()
    {
        $handler = new HelperHandler();
        $handler->setAuthenticator(new HelperAuthenticator());
        $handler->setRouter(new HelperRouter(HelperRouter::ERROR_BAD_METHOD));
        $response = $handler->handle();
        $this->assertEquals($response->status, Response::STATUS_METHOD_NOT_IMPLEMENTED);
    }

    /**
     * @covers \spoof\lib360\api\request\Handler::handle
     */
    public function testHandle_FailException()
    {
        $handler = new HelperHandler();
        $handler->setAuthenticator(new HelperAuthenticator());
        $handler->setRouter(new HelperRouter(HelperRouter::ERROR_GENERAL));
        $response = $handler->handle();
        $this->assertEquals($response->status, Response::STATUS_ERROR);
    }

    /**
     * @covers \spoof\lib360\api\request\Handler::handle
     */
    public function testHandle_Success()
    {
        $handler = new HelperHandler();
        $handler->setAuthenticator(new HelperAuthenticator());
        $handler->setRouter(new HelperRouter(HelperRouter::SUCCESS));
        $response = $handler->handle();
        $this->assertEquals($response->status, Response::STATUS_OK);
    }

}

?>
