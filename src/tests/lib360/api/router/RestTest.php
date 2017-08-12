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

use spoof\lib360\api\Request;
use spoof\lib360\api\Response;
use spoof\lib360\api\router\Rest;
use spoof\tests\TestCase;
use spoof\tests\Util;

class RestTest extends TestCase
{

    /**
     * @covers \spoof\lib360\api\router\Rest::__construct
     */
    public function testConstruct()
    {
        $rest = new Rest('spoof\tests\lib360\api\router');
        $this->assertEquals(
            array('spoof\tests\lib360\api\router', null),
            array(
                Util::getProtectedProperty($rest, 'namespace'),
                Util::getProtectedProperty($rest, 'config'),
            )
        );
    }

    /**
     * @covers \spoof\lib360\api\router\Rest::handleRequest
     * @covers \spoof\lib360\api\router\Rest::translateRequest
     */
    public function testHandleRequest_Fail_NoClass()
    {
        $rest = new Rest('spoof\tests\lib360\api\router');
        $request = new Request();
        $response = $rest->handleRequest($request);
        $this->assertEquals(Response::STATUS_RESOURCE_NOT_FOUND, $response->status);
    }

    /**
     * @covers \spoof\lib360\api\router\Rest::handleRequest
     * @covers \spoof\lib360\api\router\Rest::translateRequest
     * @covers \spoof\lib360\api\router\Rest::translateOperation
     */
    public function testHandleRequest_Fail_InvalidClass()
    {
        $rest = new Rest('spoof\tests\lib360\api\router');
        $request = new Request();
        $request->parts[0] = 'Foo'; // Class doesn't exist
        $request->operation = 'PUT';
        $response = $rest->handleRequest($request);
        $this->assertEquals(Response::STATUS_RESOURCE_NOT_FOUND, $response->status);
    }

    /**
     * @covers \spoof\lib360\api\router\Rest::handleRequest
     * @covers \spoof\lib360\api\router\Rest::translateRequest
     * @covers \spoof\lib360\api\router\Rest::translateOperation
     */
    public function testHandleRequest_Fail_InvalidOperation()
    {
        $rest = new Rest('spoof\tests\lib360\api\router');
        $request = new Request();
        $request->parts[0] = 'HelperRestResource';
        $request->operation = 'FOO'; // Invalid operation
        $response = $rest->handleRequest($request);
        $this->assertEquals(Response::STATUS_METHOD_NOT_IMPLEMENTED, $response->status);
    }

    /**
     * @covers \spoof\lib360\api\router\Rest::handleRequest
     * @covers \spoof\lib360\api\router\Rest::translateRequest
     * @covers \spoof\lib360\api\router\Rest::translateOperation
     */
    public function testHandleRequest_Fail_InvalidMethod()
    {
        $rest = new Rest('spoof\tests\lib360\api\router');
        $request = new Request();
        $request->parts[0] = 'HelperRestResource';
        $request->operation = 'PUT'; // Valid operation, but corresponding method not defined for resource
        $response = $rest->handleRequest($request);
        $this->assertEquals(Response::STATUS_METHOD_NOT_ALLOWED, $response->status);
    }

    /**
     * @covers \spoof\lib360\api\router\Rest::handleRequest
     * @covers \spoof\lib360\api\router\Rest::translateRequest
     * @covers \spoof\lib360\api\router\Rest::translateOperation
     */
    public function testHandleRequest_Fail_ResourceException()
    {
        $rest = new Rest('spoof\tests\lib360\api\router');
        $request = new Request();
        $request->parts[0] = 'HelperRestResource';
        $request->parts[1] = 'InvalidArgumentException';
        $request->operation = 'GET';
        $response = $rest->handleRequest($request);
        $this->assertEquals(Response::STATUS_BAD_REQUEST, $response->status);
    }

    /**
     * @covers \spoof\lib360\api\router\Rest::handleRequest
     * @covers \spoof\lib360\api\router\Rest::translateRequest
     * @covers \spoof\lib360\api\router\Rest::translateOperation
     */
    public function testHandleRequest_Fail_ForbiddenException()
    {
        $rest = new Rest('spoof\tests\lib360\api\router');
        $request = new Request();
        $request->parts[0] = 'HelperRestResource';
        $request->parts[1] = 'ForbiddenException';
        $request->operation = 'GET';
        $response = $rest->handleRequest($request);
        $this->assertEquals(Response::STATUS_FORBIDDEN, $response->status);
    }

    /**
     * @covers \spoof\lib360\api\router\Rest::handleRequest
     * @covers \spoof\lib360\api\router\Rest::translateRequest
     * @covers \spoof\lib360\api\router\Rest::translateOperation
     */
    public function testHandleRequest_Success_OnePart()
    {
        $rest = new Rest('spoof\tests\lib360\api\router');
        $request = new Request();
        $request->parts[0] = 'HelperRestResource';
        $request->operation = 'POST';
        $response = $rest->handleRequest($request);
        $this->assertEquals(
            array(Response::STATUS_OK, 'foo'),
            array($response->status, $response->body),
            "Expected successful response didn't match"
        );
    }

    /**
     * @covers \spoof\lib360\api\router\Rest::handleRequest
     * @covers \spoof\lib360\api\router\Rest::translateRequest
     * @covers \spoof\lib360\api\router\Rest::translateOperation
     */
    public function testHandleRequest_Success_TwoParts()
    {
        $rest = new Rest('spoof\tests\lib360\api\router');
        $request = new Request();
        $request->parts[0] = 'HelperRestResource';
        $request->parts[1] = 'Foo';
        $request->operation = 'POST';
        $response = $rest->handleRequest($request);
        $this->assertEquals(
            array(Response::STATUS_OK, 'foo submitted'),
            array($response->status, $response->body),
            "Expected successful response didn't match"
        );
    }

    /**
     * @covers \spoof\lib360\api\router\Rest::handleRequest
     * @covers \spoof\lib360\api\router\Rest::translateRequest
     * @covers \spoof\lib360\api\router\Rest::translateOperation
     */
    public function testHandleRequest_Success_TwoPartsWithArgs()
    {
        $rest = new Rest('spoof\tests\lib360\api\router');
        $request = new Request();
        $request->parts[0] = 'HelperRestResource';
        $request->parts[1] = 'Foo';
        $request->operation = 'POST';
        $request->data['bar'] = 'baz';
        $response = $rest->handleRequest($request);
        $this->assertEquals(
            array(Response::STATUS_OK, array('bar' => 'baz')),
            array($response->status, $response->body),
            "Expected successful response didn't match"
        );
    }

    /**
     * @covers \spoof\lib360\api\router\Rest::handleRequest
     * @covers \spoof\lib360\api\router\Rest::translateRequest
     * @covers \spoof\lib360\api\router\Rest::translateOperation
     */
    public function testHandleRequest_Success_ThreeParts()
    {
        $rest = new Rest('spoof\tests\lib360\api\router');
        $request = new Request();
        $request->parts[0] = 'HelperRestResource';
        $request->parts[1] = 'Args';
        $request->parts[2] = 'foo';
        $request->operation = 'GET';
        $response = $rest->handleRequest($request);
        $this->assertEquals(
            array(Response::STATUS_OK, array('foo')),
            array($response->status, $response->body),
            "Expected successful response didn't match"
        );
    }

    /**
     * @covers \spoof\lib360\api\router\Rest::handleRequest
     * @covers \spoof\lib360\api\router\Rest::translateRequest
     * @covers \spoof\lib360\api\router\Rest::translateOperation
     */
    public function testHandleRequest_Success_ThreePartsWithArgs()
    {
        $rest = new Rest('spoof\tests\lib360\api\router');
        $request = new Request();
        $request->parts[0] = 'HelperRestResource';
        $request->parts[1] = 'Args';
        $request->parts[2] = 'foo';
        $request->operation = 'GET';
        $request->data['bar'] = 'baz';
        $response = $rest->handleRequest($request);
        $this->assertEquals(
            array(Response::STATUS_OK, array(0 => 'foo', 'bar' => 'baz')),
            array($response->status, $response->body),
            "Expected successful response didn't match"
        );
    }

    /**
     * @covers \spoof\lib360\api\router\Rest::handleRequest
     * @covers \spoof\lib360\api\router\Rest::translateRequest
     * @covers \spoof\lib360\api\router\Rest::translateOperation
     */
    public function testHandleRequest_Success_FourParts()
    {
        $rest = new Rest('spoof\tests\lib360\api\router');
        $request = new Request();
        $request->parts[0] = 'helper';
        $request->parts[1] = 'RestResource';
        $request->parts[2] = 'Args';
        $request->parts[3] = 'foo';
        $request->operation = 'GET';
        $response = $rest->handleRequest($request);
        $this->assertEquals(
            array(Response::STATUS_OK, array('foo')),
            array($response->status, $response->body),
            "Expected successful response didn't match"
        );
    }

}

?>
