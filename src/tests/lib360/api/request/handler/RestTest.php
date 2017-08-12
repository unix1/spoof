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

namespace spoof\tests\lib360\api\request\handler;

use spoof\lib360\api\request\handler\Rest;
use spoof\lib360\api\Response;
use spoof\tests\TestCase;
use spoof\tests\Util;

class RestTest extends TestCase
{

    /**
     * @covers \spoof\lib360\api\request\handler\Rest::getRequest
     */
    public function testGetRequest_NoQueryString()
    {
        $rest = new Rest();
        $_SERVER['REQUEST_URI'] = '/foo/bar/baz';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_GET = array('getarg1' => 'getval1', 'sharedarg' => 'getval');
        $_POST = array('postarg1' => 'postval1', 'sharedarg' => 'postval');
        $request = $rest->getRequest();
        $this->assertEquals(
            array(
                'POST',
                array('foo', 'bar', 'baz'),
                array('getarg1' => 'getval1', 'sharedarg' => 'getval', 'postarg1' => 'postval1'),
            ),
            array(
                $request->operation,
                $request->parts,
                $request->data,
            )
        );
    }

    /**
     * @covers \spoof\lib360\api\request\handler\Rest::getRequest
     */
    public function testGetRequest_QueryString()
    {
        $rest = new Rest();
        $_SERVER['REQUEST_URI'] = '/foo/bar/baz?foobarbaz';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_GET = array('getarg1' => 'getval1', 'sharedarg' => 'getval');
        $_POST = array('postarg1' => 'postval1', 'sharedarg' => 'postval');
        $request = $rest->getRequest();
        $this->assertEquals(
            array(
                'POST',
                array('foo', 'bar', 'baz'),
                array('getarg1' => 'getval1', 'sharedarg' => 'getval', 'postarg1' => 'postval1'),
            ),
            array(
                $request->operation,
                $request->parts,
                $request->data,
            )
        );
    }

    /**
     * @covers \spoof\lib360\api\request\handler\Rest::getRequest
     */
    public function testGetRequest_Put()
    {
        $rest = new Rest();
        $_SERVER['REQUEST_URI'] = '/foo/bar/baz';
        $_SERVER['REQUEST_METHOD'] = 'PUT';
        $_GET = array('getarg1' => 'getval1', 'sharedarg' => 'getval');
        $_PUT = array('putarg1' => 'putval1', 'sharedarg' => 'putval');
        $request = $rest->getRequest();
        // NOTE typically we'd expect 'putarg1' => 'putval1' in the data array
        // below, but this is not testable without the actual PUT HTTP request
        // so that the php://input stream exists
        $this->assertEquals(
            array(
                'PUT',
                array('foo', 'bar', 'baz'),
                array('getarg1' => 'getval1', 'sharedarg' => 'getval'),
            ),
            array(
                $request->operation,
                $request->parts,
                $request->data,
            )
        );
    }

    /**
     * Runs in separate process, otherwise it would conflict with PHPUnit's header output
     * @covers \spoof\lib360\api\request\handler\Rest::sendResponse
     * @runInSeparateProcess
     */
    public function testSendResponse()
    {
        $rest = new Rest();
        $statuses = Util::getProtectedProperty($rest, 'statusMap');
        foreach ($statuses as $key => $status) {
            $response = new Response();
            $response->status = $key;
            $response->body = 'foo';
            ob_start();
            $result = $rest->sendResponse($response);
            $output = ob_get_contents();
            ob_end_clean();
            $outputObject = json_decode($output);
            $this->assertTrue($result);
            if ($this->responseStatusMayHaveBody($key)) {
                $this->assertEquals($key, $outputObject->status);
            }
        }
    }

    private function responseStatusMayHaveBody($status)
    {
        return !in_array($status, array(
            Response::STATUS_OK_CREATED,
            Response::STATUS_AUTH_FAILED,
        ));
    }

}

?>
